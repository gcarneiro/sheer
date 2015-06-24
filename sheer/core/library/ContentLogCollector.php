<?php

namespace Sh;

abstract class ContentLogCollector {
	
	static public function run () {
		
		try {
			
			$hoje = new \DateTime();
			
			//BUSCO OS LOGS CAPTURADOS ATÉ ONTEM
			$connection = \Sh\DatabaseConnectionProvider::getDatabaseConnection();
			$queryLoad = 'SELECT * FROM sh_contentLog WHERE opDate < "'.$hoje->format('Y-m-d').'" ORDER BY opDate DESC';
			$stm = $connection->prepare($queryLoad);
			$response = $stm->execute();
			if( !$response ) {
				throw new \Sh\SheerException(array(
					'message'=>'Erro ao recuperar os contentLogs',
					'code'=>'SCL_XXXX'
				));
			}
			
			//CAPTURANDO RESULTADOS
			$contentLogs = $stm->fetchAll(\PDO::FETCH_ASSOC);
			if( !$contentLogs ) { return true; }
			
			//REMOVO OS LOGS CAPTURADOS
			$queryRemove = 'DELETE FROM sh_contentLog WHERE opDate < "'.$hoje->format('Y-m-d').'"';
			$response = $connection->exec($queryRemove);
			if( $response === false ) {
				throw new \Sh\SheerException(array(
						'message'=>'Erro ao remover os contentLogs',
						'code'=>'SCL_XXXX'
				));
			}
			
			//capturando o logger
			$logger = \Sh\LoggerProvider::getLogger('contentLog');
			
			foreach ( $contentLogs as $elementLog ) {
				$serialized = serialize($elementLog);
				$logger->log($serialized, true);
			}
			
			$connection->commit();
			
			return true;
		}
		catch (\Sh\SheerException $e) {
			$connection->rollBack();
			return false;
		}
		
		
		
	}
	
}