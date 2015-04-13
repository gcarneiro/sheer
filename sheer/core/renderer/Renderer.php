<?php

namespace Sh;

/**
 * @author guilherme
 *
 *
 * Classe responsável por efetuar a renderização de Renderables dos Módulos
 */
class Renderer {
	
	/**
	 * @var \Sh\Renderable
	 */
	protected $renderable;
	
	protected $parameters = array();
	
	public final function __construct(\Sh\Renderable $renderable, $parameters=array()) {
		
		$this->renderable = $renderable;
		
		//vamos processar os parametros a serem utilizados pelo Renderer
		$this->parseParameters($parameters);
		
	}
	
	/**
	 * Método responsável por efetuar a renderização do Renderable
	 * @param string $idStyle
	 * @return string html final gerado
	 */
	public final function render($idStyle=null) {
		
		//CARREGANDO DADOS DOS DATAPROVIDERS
		$content = $this->getContentFromDataProviders();
		
		//TODO AINDA PRECISO CARREGAR OS DATASOURCES REFERENCIADOS
		
		//GERANDO INFORMACOES DO RENDERER A SER FORNECIDA PARA O ESTILO
		$rendererInfo = array(
			'module' => $this->renderable->getModuleId(),
			'renderable' => $this->renderable->getId()
		);
		
		//PRECISO BUSCAR O ARQUIVO DE ESTILO RESPONSÁVEL PELA RENDERIZAÇÃO
		$filePath = $this->getStyleFilePath($idStyle);
		
		//COMEÇO O TRATAMENTO DE BUFFER
		ob_start();
		//EXECUTANDO A RENDERIZACAO
		\Sh\shRendererDoRender($filePath, $content, $rendererInfo, $this->parameters);
		//CAPTURO O BUFFER
		$html = ob_get_contents();
		//LIMPO E ENCERRO O BUFFER
		ob_end_clean();
		
		//SE EXISTIR HTML PARA SER DEVOLVIDO, IREI CRIAR UM HOLDER HTML PARA IDENTIFICAR OS LIMITES DO RENDERER
		//FIXME REMOVI POR NAO SABER SE SERÁ ÚTIL
// 		if( $html ) {
// 			$id = 'sh-rd-'.$this->renderable->getModuleId().'-'.$this->renderable->getId();
// 			$html = '<div id="'.$id.'">'.$html.'</div>';
// 		}
		
		//RETORNO O RENDERABLE RENDERIZADO
		return $html;
	}
	
	/**
	 * Método responsável por processar todos os dataProviders e retornar os conteudos carregados
	 * @return array
	 * 		idDataProvider => array(\Sh\ContentProvider return)
	 */
	protected final function getContentFromDataProviders () {
		
		$dataProviders = $this->renderable->getDataProviders();
		$dataProviders = $dataProviders['dataProviders'];
		
		//DEFININDO CONTROLADOR DE CONTEUDOS
		$content = array();
		
		if( !$dataProviders ) { return null; }
		
		//FIXME devo passar este processamento para dentro do \Sh\RenderableDataProvider?
		foreach ( $dataProviders as $idDataProvider=>$dataProvider ) {
			
			//DETERMINANDO FILTROS E CONTROLANDO O FILTER PROCESSOR
			//busco para ver se devo utilizar uma funcao para processamento dos filtros
			$filters = $this->parameters;
			$filterProcessor = $dataProvider->getFilterProcessor();
			
			//MAPEANDO QUAIS SÃO OS FILTROS OBRIGATÓRIOS
			$requiredFilters = array();
			
			//tratando primeiro os filtros customizados pelo desenvolvedor
			if( $filterProcessor && isset($filterProcessor['filters']) && $filterProcessor['filters'] ) {
				foreach ($filterProcessor['filters'] as $by=>$fil) {
					$valor = null;
					//buscando o valor pelo defaultValue primeiro
					if( isset($fil['defaultValue']) ) {
						$valor = \Sh\RuntimeVariables::getAliasValue($fil['defaultValue']);
						//nao sendo um alias
						if( $valor === false ) { 
							$valor = $fil['defaultValue'];
						}
					}
					
					//VERIFICO O PARAMETRO ENVIADO, CASO EXISTA UTILIZO ELE
					//Verificando se o filterProcessor customiza o nome do parametro
					if( isset($fil['param']) ) {
						//se o parametro customizado possuir valor
						if( isset($filters[$fil['param']]) ) {
							$valor = $filters[$fil['param']];
						}
						
					}
					//filterProcessor NÃO customiza o nome do parametro
					else {
						//caso exista valor para o filtro utilizando o by como parametro
						if ( isset($filters[$by]) ) {
							$valor = $filters[$by];
						}
					}
					
					//Verificando se o filtro é obrigatório
					if( isset($fil['required']) && is_bool($fil['required']) ) {
						$requiredFilters[$by] = $fil['required'];
					}
					
					//inserindo valor nos filtros
					$filters[$by] = $valor;
				}
			}
			
			//tratando o filterProcessor function 
			if( $filterProcessor && isset($filterProcessor['function']) && $filterProcessor['function'] ) {
				$fn = '\\Sh\\Modules\\'.$this->renderable->getModuleId().'\\'.$filterProcessor['function'];
				if( function_exists($fn) ) {
					$filters = $fn($filters);
				}
				
			}
			
			//DEFININDO CONFIGURAÇÕES EXTRAS
			$configuration = array();
			//sort customizado
			if( $dataProvider->getSort() ) {
				$configuration['sort'] = $dataProvider->getSort();
			}
			//maxRows customizado
			if( $dataProvider->getMaxRows() !== null && is_numeric($dataProvider->getMaxRows()) ) {
				$configuration['maxRows'] = $dataProvider->getMaxRows();
			}
			//Marcando nas configurações customizadas os filtros obrigatórios
			$configuration['requiredFilters'] = $requiredFilters;
			
			//buscando dados
			$data = \Sh\ContentProviderManager::loadContent($idDataProvider, $filters, $configuration);
			$content[$idDataProvider] = $data;
			
			//TODO PRECISO IMPLEMENTAR A FUNCAO DE DATAPROCESSOR
		}
		
		return $content;
	}
	
	/**
	 * Determina o caminho do arquivo de estilo responsável pela renderização atual
	 * @throws \Sh\SheerException
	 * @return string
	 */
	protected function getStyleFilePath ($idStyle=null) {
		
		//IREI BUSCAR O ESTILO RESPONSÁVEL PELA RENDERIZAÇÃO
		$style = $this->renderable->getStyle($idStyle);
		
		//BUSCO OS PATHS ONDE O MÓDULO POSSUI INSTANCIAS
		$moduleConfig = \Sh\ModuleControl::getModuleConfig($this->renderable->getModuleId());
		$modulePaths = $moduleConfig['path'];
		
		//INICIANDO BUSCA DE ARQUIVO DE ESTILO
		$filePath = null;
		
		//buscando nos paths para encontrar o arquivo de estilo
		foreach ($modulePaths as $p) {
			$tmpPath = $p.'styles/'.$style->getPath();
			if( is_file($tmpPath) ) {
				$filePath = $tmpPath;
				break;
			}
		}
		
		//caso não encontrado irei buscar nos estilos padrões
		if( !$filePath ) {
			//vou produzir caminhos temporarios para os padroes do Projeto e Sheer
			$projectStyle = SH_PROJECT_STYLES_PATH.$style->getPath();
			$sheerStyle = SH_RENDERER_STYLES_PATH.$style->getPath();
			//buscando cada um
			if( is_file($projectStyle) ) {
				$filePath = $projectStyle;
			}
			else if( is_file($sheerStyle) ) {
				$filePath = $sheerStyle;
			}
			
		}
		
		//verificando existencia de arquivo de estilo
		if( !$filePath ) {
			throw new \Sh\SheerException(array(
					'message' => 'Arquivo de estilo não encontrado',
					'code' => 'SR_XXXX'
			));
		}
		
		return $filePath;
	}
	
	/**
	 * Método responsável por processar todos os parametros recebidos na requisição
	 * @param array $parameters
	 */
	protected final function parseParameters ($parameters) {
		
		$dataRequest = array_merge($_GET, $_POST, $_FILES, $parameters);
		$this->parameters = $dataRequest;
		
	}
	
}

function shRendererDoRender($filePath, $content, $rendererInfo, $requestParameters) {
	
	include $filePath;
	
}