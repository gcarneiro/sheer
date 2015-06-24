<?php

namespace Sh;

abstract class RendererManager {
	
	/**
	 * Método responsável por receber a identificação completa de um Renderable, juntamente a parametros customizados e renderiza-lo.
	 * 
	 * @param string $renderable Identificador de Renderable completo idModule/idRenderable
	 * @param string $style Identificador do Estilo que o Renderable deve utilizar
	 * @param array $parameters
	 * @param number $page
	 * @throws \Sh\SheerException
	 * @return string | false em caso de erro
	 */
	static function render ($renderable, $style=null, $parameters=array(), $page=1) {
		
		try {
			list($idModule, $idRenderable) = explode('/', $renderable);
			
			//CARREGANDO MODULO
			$module = \Sh\ModuleFactory::getModuleFull($idModule);
			if( !$module ) {
				throw new \Sh\SheerException(array(
					'message' => 'Erro ao carregar módulo do Renderable',
					'code' => 'SR_XXXX'
				));
			}
			
			//CAPTURANDO RENDERABLE RESPONSAVEL
			//verificando existencia de renderable declarados em xml
			if( isset($module->renderables[$idRenderable]) ) {
				$renderable = $module->renderables[$idRenderable];
			}
			//Verificar a existencia de arquivo de estilo com este nome
			//se existir vamos criar um renderable runtime para utilizar
			else {
				//Criando renderable falso
				$renderable = new Renderable($module->id, $idRenderable);
				//criando estilo runtime
				$rdStyle = new RenderableStyle($idRenderable, $idRenderable.'.php');
				$renderable->pushStyle($rdStyle);
			}
			
			//RENDERIZANDO
			$renderer = new \Sh\Renderer($renderable, $parameters);
			$html = $renderer->render($style);
			return $html;
		}
		catch ( \Sh\SheerException $e ) {
			return false;
		}
		
	}
	
	/**
	 * Método responsável por renderizar um estilo qualquer a partir de dados e parametros enviados pelo desenvolvedor
	 * Este irá efetuar quase a mesma coisa que um renderable, mas sem as propriedades de dataProviders.
	 * 
	 * @param string $idModule
	 * @param string $stylePath
	 * @param array $content
	 * @param array $parameters
	 * @throws \Sh\SheerException
	 * @return string
	 */
	static public function renderStyle ( $idModule, $stylePath, $content=array(), $parameters=array() ) {
		
		//IREI VERIFICAR QUE O MÓDULO EXISTE
		$modulo = \Sh\ModuleFactory::getModuleFull($idModule);
		if( !$modulo ) {
			throw new \Sh\SheerException(array(
				'message' => 'Erro ao carregar módulo do Renderable',
				'code' => 'SR_XXXX'
			));
		}

		//BUSCANDO O ARQUIVO DE ESTILO DESEJADO
		//Carrego os paths de instancias dos módulos
		$moduleConfig = \Sh\ModuleControl::getModuleConfig($idModule);
		$modulePaths = $moduleConfig['path'];
		
		//determinando path total do arquivo
		$filePath = null;
		
		//buscando nos paths de styles e templates para e encontrar arquivo de estilo
		foreach ($modulePaths as $p) {
			$tmpStylePath = $p.'styles/'.$stylePath;
			$tmpTemplatePath = $p.'styles/'.$stylePath;
			//verificando se arquivo é um estilo
			if( is_file($tmpStylePath) ) {
				$filePath = $tmpStylePath;
				break;
			}
			//Verificando se arquivo é um template
			else if( is_file($tmpTemplatePath) ) {
				$filePath = $tmpTemplatePath;
				break;
			}
		}
		
		//verificando existencia de arquivo de estilo
		if( !$filePath ) {
			throw new \Sh\SheerException(array(
				'message' => 'Arquivo de estilo não encontrado',
				'code' => 'SR_XXXX'
			));
		}
		
		//GERANDO INFORMACOES DO RENDERER A SER FORNECIDA PARA O ESTILO
		$rendererInfo = array(
			'module' => $idModule
		);
		
		//COMEÇO O TRATAMENTO DE BUFFER
		ob_start();
		//EXECUTANDO A RENDERIZACAO
		\Sh\shRendererDoRender($filePath, $content, $rendererInfo, $parameters);
		//CAPTURO O BUFFER
		$html = ob_get_contents();
		//LIMPO E ENCERRO O BUFFER
		ob_end_clean();
		
		//NESTE MOMENTO ACABEI A RENDERIZAÇÃO DO CONTEUDO E DEVO RETORNAR O HTML CAPTURADO
		return $html;
		
		
	}
	
	
	
}