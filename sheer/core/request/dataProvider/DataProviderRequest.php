<?php

namespace Sh;

abstract class DataProviderRequest {
	
	/**
	 * Método para efetuar o processamento do DataProvider desejado.
	 * Este já irá processar e efetuar o retorno via JSON
	 */
	static public function process() {
	
		if( !isset($_GET['dp']) || !strpos($_GET['dp'], '/') ) {
			var_dump('nada');
			exit;
		}
		
		//carregando dados
		$filters = array_merge($_GET, $_POST, $_FILES);
		$data = \Sh\ContentProviderManager::loadContent($_GET['dp'], $filters, null);
		
		//gerando resposta
		echo json_encode($data);
	}
}
