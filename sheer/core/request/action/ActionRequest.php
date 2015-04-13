<?php

namespace Sh;

abstract class ActionRequest {
	
	/**
	 * Método para efetuar o processamento da ação desejada. 
	 * Este já irá processar e retornar exatamente o que o actionHandler deverá retornar
	 */
	static public function process() {
		//Caso não tenha enviado as informações do actionHandler
		if( !isset($_GET['ah']) || !strpos($_GET['ah'], '/') ) {
			return array(
				'status' => false,
				'code' => null,
				'message' => 'ActionHandler não foi configurado para este request'
			);
		}
		
		$actionResponse = \Sh\ContentActionManager::doAction($_GET['ah'], null);
		return $actionResponse;
	}
	
}