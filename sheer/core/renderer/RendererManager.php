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
	
	
	
}