<?php

namespace Sh;

abstract class RendererRequest {
	
	/**
	 * Método para efetuar o processamento do Renderer desejado.
	 * Este já irá processar e efetuar o retorno via JSON
	 * 
	 * FIXME PRECISO DETERMINAR A PÁGINA DESEJADA E MELHORAR OS FILTROS
	 */
	static public function process( $page=null ) {
	
		if( !isset($_GET['rd']) || !strpos($_GET['rd'], '/') ) {
			var_dump('nada');
			exit;
		}
		//Setando rdstyle como null para caso não tiver um estilo setado
		$filters = array_merge($_GET, $_POST, $_FILES);
		$rdStyle = null;
		
		/**
		 * Verificando se veio um style como parametro do renderer
		 * 		Antigamente usavamos o parametro "rdStyle", ele será depreciado
		 * 		@deprecated em favor do novo parametro "shStyle"
		 */
		if(isset($filters['rdStyle'])){
			//Se vier seto seu valor em idStyle
			$rdStyle = $filters['rdStyle'];
		}
		
		//buscando renderer
		$html = \Sh\RendererManager::render($_GET['rd'], $rdStyle, $filters);
		
		$returnString = '';
		
		//VERIFICO SE EXISTE O PARAMETRO htmlResponse
		//CASO EXISTA E SEU VALOR SEJA 1, IREI DEVOLVER DIRETAMENTE O HTML E NÃO O JSON
		if( !isset($_GET['htmlResponse']) || $_GET['htmlResponse'] !== '1' ) {
			
			//gerando resposta
			$response = array(
					'status' => true,
					'html' => $html
			);
			
			$returnString = json_encode($response);
			
		}
		else {
			$returnString = $html;
		}
		
		echo $returnString;
	}
}

