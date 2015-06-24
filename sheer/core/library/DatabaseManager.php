<?php

namespace Sh;

abstract class DatabaseLibrary {
	
	/**
	 * Método para receber um valor e o seu tipo e retornar como deve ser inserido numa query processando nulo
	 * @param string $value
	 * @param string $type
	 * @return string
	 * 
	 * TODO PROCESSAR TIPOS INTEIROS, DETERMINAR SE ACEITA NULL
	 */
	static public function getStatement ( $value, $type='string' ) {
		
		$response = '';
		
		if( !$value || strlen($value)==0 ) {
			$response = 'NULL';
		}
		else {
			$response = '"'.$value.'"';
		}
		
		return $response;
		
	}
	
}

/**
 * @author guilherme
 * Classe generica para recuperar conexões e efetuar operações nos bancos de dados
 *
 */
abstract class DatabaseManager {
	
	/**
	 * Método que irá executar uma query específica utilizando uma conexão de preferencia do desenvolvedor
	 * Este método é mais lendo do que puxar uma conexão e rodar por conta própria pois ele já trata todos os dados de uma só vez retornando um array completo
	 * @param string $query
	 * @param string|\PDO $idConnection
	 * @return array
	 */
	static public function runQuery ( $query, $connection='default' ) {
		
		//Determinando se estamos recebendo o id de uma conexão ou já a conexão pronta
		if( is_string($connection) || !$connection ) {
			$connection = \Sh\DatabaseConnectionProvider::getDatabaseConnection($connection);
		}
		
		//verificando existencia da conexao
		if ( !is_a($connection, '\PDO') ) {
			return false;
		}
		
		//carregando resultados
		$result = $connection->query($query);
		
		if( !$result ) { return false; }
		
		$response = $result->fetchAll(\PDO::FETCH_ASSOC);
		return $response;
		
	}
	
	/**
	 * Método que transforma um array de string para uma lista de strings a ser utilizado dentro de IN statements
	 * 
	 * @param array[string] $array
	 * @return string
	 */
	static public function makeSqlListFromArray ( $array ) {
		//determino os identificadores de conteudos primários
		$sqlQuery = '';
		foreach ($array as &$row) {
			if( !$sqlQuery ) {
				$sqlQuery .= '"'.$row['contentId'].'"';
			}
			else {
				$sqlQuery .= ',"'.$row['contentId'].'"';
			}
		}
		return $sqlQuery;
	}
	
}