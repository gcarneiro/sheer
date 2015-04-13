<?php

namespace Sh;

class PageProcessor {
	
	//configurações iniciais
	protected $config;
	protected $pageLink;
	
	//configurações do template
	protected $templatePath;
	protected $template;
	
	//configurações da página
	protected $pageTitle;
	protected $pageDescription;
	protected $pageStyles = array();
	protected $pageScripts = array();
	
	public function __construct($config, $pageLink) {
		
		$this->config = $config;
		$this->pageLink = $pageLink;
		
		if( !isset($this->config['config']) ) {
			throw new \Sh\FatalErrorException(array(
				'message' => 'Configurações da página "'.$this->pageLink.'" são inválidas',
				'code' => 'SPR_XXXX'
			));
		}
		
		$this->loadTemplate();
		$this->loadPageConfiguration();
		
	}
	
	
	
	/**
	 * Método responsável por executar o render da Página e retornar o html final da mesma
	 * @return mixed
	 */
	public function render () {
		
		$html = $this->template;
		
		//criando baseUrl
		$baseUrl = '<base href="'.\Sh\RuntimeInfo::getBaseUrl().'" />';
		
		/*
		 * Consumindo todos os alias de conteudo e aplicando para gerar html final
		 * Alias suportados:
		 * 		template -> Carrega templates estaticos
		 * 		holder -> Carrega holders para geração de html
		 * 		renderer -> Executa um renderable explicitado de forma direta
		 */
		do {
			$searchs = array();
			$replaces = array();
			
			//HOLDERS
			$tmp = $this->renderHolders($html);
			$searchs = array_merge($searchs, $tmp['searchs']);
			$replaces = array_merge($replaces, $tmp['replaces']);
			
			//RENDERABLES
			$tmp = $this->renderRenderers($html);
			$searchs = array_merge($searchs, $tmp['searchs']);
			$replaces = array_merge($replaces, $tmp['replaces']);
			
			//BUSCANDO SUBTEMPLATES
			$tmp = $this->renderSubTemplates($html);
			$searchs = array_merge($searchs, $tmp['searchs']);
			$replaces = array_merge($replaces, $tmp['replaces']);
			
			//GERANDO HTML FINAL
			$html = str_replace($searchs, $replaces, $html);
			
			$regex = '/{{(template|holder|renderer)\.([\w\+\-\.\/]+)}}/';
			$matches = preg_match($regex, $html);
			
		} while ($matches);
		
		//SUBSTITUINDO ELEMENTOS DE CONFIGURACAO
		$searchs = array(
			'{{page.baseUrl}}',
			'{{page.title}}',
			'{{page.styles}}',
			'{{page.scripts}}'
		);
		$replaces = array(
			$baseUrl,
			\Sh\PageRuntimeConfiguration::getTitle(),
			$this->getStylesHtml(),
			$this->getScriptsHtml()
		);
		$html = str_replace($searchs, $replaces, $html);
		
		//REMOVENDO SOBRAS E LIXOS DE SUBSTITUIÇÃO DO HTML FINAL
// 		$html = preg_replace('/{{(.*)}}/', '', $html);
		return $html;
		
	}
	
	/**
	 * Iremos produzir o array para substituições de todos os subtemplates convocados
	 * @param string $html
	 * @return string
	 */
	protected function renderSubTemplates ($html) {
		$response = array(
				'searchs' => array(),
				'replaces' => array()
		);
		$searchs = array();
		$replaces = array();
		
		$matches = array();
		preg_match_all('/{{template\.([a-zA-Z0-9\+\-\.\/]+)}}/', $html, $matches, PREG_SET_ORDER);
		if( $matches ) {
			foreach ($matches as $element) {
				$match = $element[0];
				$template = $element[1];
				
				//gerando html
				$html = '';
				
				//buscando o templatePath
				$templatePath = \Sh\Library::getTemplatePath($template);
				if( $templatePath ) {
					$html = self::getProcessedTemplateFile($templatePath);
				}
		
				$response['searchs'][] = $match;
				$response['replaces'][] = $html;
			}
		}
		
		return $response;
	}
	
	/**
	 * Iremos produzir o array para substituições de todos os renderers dispostos direto
	 * Esta função deve retornar com um array de searchs e outro para replaces
	 * @param string  $html
	 * @return array
	 * 		'searchs' => array
	 * 		'replaces' => array
	 */
	protected function renderRenderers ($html) {
	
		$response = array(
				'searchs' => array(),
				'replaces' => array()
		);
		$searchs = array();
		$replaces = array();
	
		$matches = array();
		preg_match_all('/{{renderer\.([a-zA-Z0-9\+\-\.\/]+)}}/', $html, $matches, PREG_SET_ORDER);
		if( $matches ) {
			foreach ($matches as $element) {
				$match = $element[0];
				$idRenderer = $element[1];
				
				$holderHtml = $this->renderRendererById($idRenderer);
				
				$response['searchs'][] = $match;
				$response['replaces'][] = $holderHtml;
			}
		}
	
		return $response;
	}
	
	/**
	 * Iremos produzir o array para substituições de todos os holders
	 * Esta função deve retornar com um array de searchs e outro para replaces
	 * Esta também irá considerar o Wrapper da configuração
	 * 
	 * @param string  $html
	 * @return array
	 * 		'searchs' => array
	 * 		'replaces' => array
	 */
	protected function renderHolders ($html) {
		
		$response = array(
			'searchs' => array(),
			'replaces' => array()
		);
		$searchs = array();
		$replaces = array();
		
		//buscando holders especificados
		$matches = array();
		preg_match_all('/{{holder\.([a-zA-Z0-9\+\-\.]+)}}/', $html, $matches, PREG_SET_ORDER);
		//processando matches
		if( $matches ) {
			foreach ($matches as $element) {
				$match = $element[0];
				$idHolder = $element[1];
				
				$holderHtml = '';
				
				//SE O HOLDER NÃO ESTIVER CONFIGURADO NA PÁGINA O DEIXAMOS VAZIO
				if( !isset($this->config['holders'][$idHolder]) ) {
					$response['searchs'][] = $match;
					$response['replaces'][] = $holderHtml;
					continue;
				}
				
				//ESTANDO CONFIGURADO NA PÁGINA IREMOS PROCESSAR E PRODUZIR O SEU HTML
				foreach ( $this->config['holders'][$idHolder] as $idRenderer => $config ) {
					
					$holderItemHtml = '';
					
					//VERIFICANDO SE O OBJETO É DE RENDERER OU MODULETEMPLATE
					//Renderer
					if( (!is_array($config) && $config) || (is_array($config) && (!isset($config['type']) || strtolower($config['type']) == 'renderer') ) ) {
						$holderItemHtml .= $this->renderRendererById($idRenderer, $config);
					}
					//ModuleTemplate
					else if ( is_array($config) && (!isset($config['type']) || strtolower($config['type']) == 'moduletemplate') ) {
						$holderItemHtml .= $this->renderModuleTemplateById($idRenderer, $config);
					}
					
					//VERIFICANDO EXISTENCIA DE WRAPPER
					if( isset($config['wrapper']) && $config['wrapper'] ) {
						$holderItemHtml = str_replace('{{html}}', $holderItemHtml, $config['wrapper']);
					}
					
					$holderHtml .= $holderItemHtml;
				}
				
				
				$response['searchs'][] = $match;
				$response['replaces'][] = $holderHtml;
			}
		}
		
		return $response;
	}
	
	/**
	 * Método para renderizar um template de um módulo
	 * Esta também irá considerar o Wrapper da configuração
	 * 
	 * @param string $idModuleTemplate ( idModule/templatePath )
	 * @param array $config
	 * @return string
	 */
	protected function renderModuleTemplateById ($idModuleTemplate, $config=array()) {
		
		
		list($idModule, $idTemplate) = explode('/', $idModuleTemplate);
		
		//verificando existencia do módulo
		if( !\Sh\ModuleControl::isModule($idModule) ) {
			return '';
		}
		
		//buscando configuracoes do módulo
		$moduleConfig = \Sh\ModuleControl::getModuleConfig($idModule);
		
		//BUSCANDO TEMPLATE
		$templatePath = null;
		foreach ( $moduleConfig['path'] as $p ) {
			$p = str_replace('//', '/', $p.'/templates/'.$idTemplate);
			if( is_file($p) ) {
				$templatePath = $p;
				break;
			}
		}
		
		//caso o não encontre, retorna vazio
		if( !$templatePath ) {
			return '';
		}
		//Retorna o html do template
		return $this->loadTemplateFromFile($templatePath);
	}
	
	
	/**
	 * Método para renderizar um Renderer específico com suas configurações de execução
	 * @param string $idRenderer
	 * @param array $config [
	 * 		'style', 'parameters'
	 * ]
	 * @return string
	 */
	protected function renderRendererById ($idRenderer, $config=array()) {
		
		$parameters = array();
		$style = null;
		
		//buscando o estilo desejado
		if( isset($config['style']) ) {
			$style = $config['style'];
		}
		//buscando os parametros para carregar
		if( isset($config['parameters']) ) {
			foreach ( $config['parameters'] as $param => $value ) {
				//determinando se o valor é um valor de alias
				if ( $value && \Sh\RuntimeVariables::isAliasValue($value) ) {
					$value = \Sh\RuntimeVariables::getAliasValue($value);
				}
				
				$parameters[$param] = $value;
			}
		}
		
		$html = \Sh\RendererManager::render($idRenderer, $style, $parameters);
		if( !$html ) {
			\Sh\LoggerProvider::log('warning', 'Erro ao tentar realizar renderização do renderer "'.$idRenderer.'". Assumindo string vazia.');
			$html = '';
		}
		
		return $html;
	}
	
	/**
	 * Método para gerar a chamada HTML dos scripts a serem utilizados no template
	 * Este irá aceitar tanto scripts padrões quanto módulos para o require js
	 * Neste não devem ser declarados a extensão ".js"
	 * @return string
	 */
	protected function getScriptsHtml () {
		$html = '';
		
		$html .= '<script>';
		//gerando scripts configurados no json da página
		if( $this->pageScripts ) {
			$scripts = '';
			foreach ( $this->pageScripts as $scriptPath ) {
				
				//determinando se é tipo complexo
				if ( is_array($scriptPath) ) {
					var_dump($scriptPath);
					exit;
				}
				//tipo simples
				else {
					if( strlen($scripts) > 0 ) {
						$scripts .= ', ';
					}
					$scripts .= '"'.$scriptPath.'"';
// 					$html .= 'require(["'.$scriptPath.'"], function (module) { if( module != undefined && isFunction(module.init) ) { module.init(); } });';
				}
			}
			if( $scripts ) {
				$scripts = '['.$scripts.']';
				$html .= 'require(["sheer"], function (sheer) { sheer.import('.$scripts.'); });';
			}
		}
		//gerando script automatico da página, devo verificar se o arquivo existe antes
		$profile = \Sh\AuthenticationControl::getAuthenticatedUserInfo('profile');
		$autoScript = 'pages/'.$profile['alias'].'/'.$this->pageLink;
		//verificando existencia do script
		$autoScriptFullPath = 'scripts/'.$autoScript.'.js';
		if( is_file($autoScriptFullPath) ) {
			$html .= 'require(["sheer"], function (sheer) { sheer.import("'.$autoScript.'"); });';
		}
		//finalizando scripts
		$html .= '</script>';
	
		return $html;
	}
	
	/**
	 * Método para gerar a chamada HTML dos estilos a serem utilizados no template
	 * @return string
	 */
	protected function getStylesHtml () {
		$html = '';
		
		if( $this->pageStyles ) {
			foreach ( $this->pageStyles as $cssPath ) {
				$html .= '<link rel="stylesheet" type="text/css" href="'.$cssPath.'" />';
			}
		}
		
		return $html;
	}
	
	/**
	 * Método para carregar as configurações da página
	 * 		Determina as seguintes informações
	 * 			"Título", "Descrição", "Styles", "Scripts"
	 */
	protected function loadPageConfiguration () {
		
		//CARREGANDO A INFORMAÇÃO DE TÍTULO
		$this->pageTitle = \Sh\ProjectConfig::getProjectConfiguration('name');
		if( isset( $this->config['config']['title'] ) ) {
			$this->pageTitle = $this->config['config']['title'];
		}
		\Sh\PageRuntimeConfiguration::setTitle($this->pageTitle, false);
		
		//CARREGANDO A INFORMAÇÃO DE DESCRIÇÃO DA PÁGINA
		$this->pageDescription = \Sh\ProjectConfig::getProjectConfiguration('description');
		if( isset( $this->config['config']['description'] ) ) {
			$this->pageDescription = $this->config['config']['description'];
		}
		\Sh\PageRuntimeConfiguration::setDescription($this->pageDescription, false);
		
		//CARREGANDO FOLHAS DE ESTILOS CUSTOMIZADAS
		if( isset( $this->config['config']['styles'] ) ) {
			foreach ($this->config['config']['styles'] as $path=>$include) {
				!!$include;
				if( !$include ) { continue; }
				$this->pageStyles[$path] = $path;
			}
		}
		
		//CARREGANDO SCRIPTS CUSTOMIZADAS
		if( isset( $this->config['config']['scripts'] ) ) {
			foreach ($this->config['config']['scripts'] as $path=>$include) {
				!!$include;
				if( !$include ) { continue; }
				$this->pageScripts[$path] = $path;
			}
		}
		
	}
	
	/**
	 * Método para carregar o template capturando o seu HTML
	 * @throws \Sh\FatalErrorException
	 */
	protected function loadTemplate () {
		
		if( !isset( $this->config['config']['template'] ) ) {
			throw new \Sh\FatalErrorException(array(
				'message' => 'Template para página "'.$this->pageLink.'" não foi configurado',
				'code' => 'SPR_XXXX'
			));
		}
		
		$templateFile = $this->config['config']['template'];
		
		//buscando template
		$templatePath = \Sh\Library::getTemplatePath($templateFile);
		if( !$templatePath ) {
			throw new \Sh\FatalErrorException(array(
				'message' => 'Template "'.$templateFile.'" não foi encontrado',
				'code' => 'SPR_XXXX'
			));
		}		
		
		$this->templatePath = $templatePath;
		//VAMOS CARREGAR O TEMPLATE DO ARQUIVO
		$this->template = $this->loadTemplateFromFile();
	}
	
	/**
	 * Iremos carregar o template referenciado guardando todo o seu retorno
	 * @return string
	 */
	private final function loadTemplateFromFile( $file = null ) {
		
		if( !$file ) {
			$file = $this->templatePath;
		}
		
		return self::getProcessedTemplateFile($file);
	}
	
	/**
	 * Método que irá processar arquivo e gerar seu html com o controle de buffer
	 * @param string $file
	 * @return string
	 */
	protected function getProcessedTemplateFile ( $file ) {
		//verifico existencia do arquivo
		if( !is_file($file) ) {
			return '';
		}
		
		ob_start();
		require $file;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
		
	}
	
}

/**
 * @author Guilherme
 * 
 * Classe para controlar informações próprias para uma exibição da página
 *
 */
abstract class PageRuntimeConfiguration {
	
	static protected $title = null;
	static protected $description = null;
	
	static protected $tags = [];
	
	/**
	 * Método para setar o título da página corrente
	 * Utilizar este com cuidado pois ele irá sempre sobrescrever qualquer título definido anteriormente
	 * 
	 * @param string $title
	 */
	static public function setTitle( $title, $overwrite=true ) {
		
		//Se eu tiver titulo e não for para sobrescrever eu retorno
		if( self::$title && !$overwrite ) {
			return;
		}
		
		$title = (string) $title;
		self::$title = $title;
	}
	
	static public function getTitle () {
		return self::$title;
	}
	
	
	/**
	 * Método para setar a descrição da página corrente
	 * Utilizar este com cuidado pois ele irá sempre sobrescrever qualquer título definido anteriormente
	 * 
	 * @param string $title
	 */
	static public function setDescription( $description ) {
		
		//Se eu tiver descricao e não for para sobrescrever eu retorno
		if( self::$description && !$overwrite ) {
			return;
		}
		
		$description = (string) $description;
		self::$description = $description;
	}
	
	static public function getDescription () {
		return self::$description;
	}
	
}



