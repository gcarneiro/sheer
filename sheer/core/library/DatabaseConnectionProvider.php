<?php

namespace Sh;

/**
 * @author guilherme
 * Classe responsável por prover as conexões com o banco de dados
 *
 */
abstract class DatabaseConnectionProvider {
	
	static protected $connections = array();
	
	/**
	 * Efetua uma nova conexão com o banco de dados iniciando uma nova transação
	 * @param string|array $settings
	 * 		string => Identificador de conexao pre configurada
	 * 		array
	 * 			driver => string
	 * 			host => string
	 * 			username => string
	 * 			password => string
	 * 			database => string
	 * 
	 * @return \PDO
	 */
	static public function newDatabaseConnection ( $settings = null ) {
		
		//DETERMINANDO O TIPO DE CONEXAO
		//sendo previamente cadastrada 
		if ( is_string($settings) && strlen($settings) > 0 ) {
			//busca a conexão nas configurações
			$config = \Sh\ProjectConfig::getDatabaseConfiguration($settings);
			if( !$config ) { return null; }
		}
		//sendo um objeto de configuração
		else if ( is_array($settings) ) {
			$config = $settings;
		}
		//não tendo sido passada, assumo a default
		else {
			$settings = 'default';
			//busca a conexão nas configurações
			$config = \Sh\ProjectConfig::getDatabaseConfiguration($settings);
			if( !$config ) { return null; }
		}
		
		//efetuando conexão
		$connection = self::connect($config);
		
		//verificando se a conexão é válida
		if( !$connection ) {
			return null;
		}
		
		//iniciando transação
		$connection->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
		$connection->beginTransaction();
		
		return $connection;
		
	}
	
	/**
	 * Método para recuperar conexões com o banco de dados
	 * Este sabe fazer um cache de conexões evitando abertura de novas conexões sem necessidade
	 * @param string $idConnection
	 * @return \PDO
	 */
	static public function getDatabaseConnection ( $idConnection = 'default' ) {
		
		//Buscando conexão
		//caso não encontre efetuamos uma nova conexão
		if( !isset( self::$connections[$idConnection] ) || !self::$connections[$idConnection] ) {
			$connection = self::newDatabaseConnection($idConnection);
			self::$connections[$idConnection] = $connection;
		}
		else {
			$connection = self::$connections[$idConnection];
		}
		
		//verificando se a conexão é válida
		if( !$connection ) {
			return null;
		}
		
		//verificando existencia de transacao e abrindo caso não exista
		if ( !$connection->inTransaction() ) {
			$connection->beginTransaction();
		}
		
		return $connection;
		
		
	}
	
	/**
	 * Método responsável por efetuar a conexão junto ao banco
	 * Deve receber o array de informações de conexão com o banco
	 * @param array $connectionInfo
	 * 		driver
	 * 		host
	 * 		username
	 * 		password
	 * 		database
	 * @return \PDO
	 */
	static protected function connect ( $connectionInfo ) {
		try {
			$dsn = 'mysql:host='.$connectionInfo['host'];
			if( isset($connectionInfo['database']) && $connectionInfo['database'] ) {
				$dsn .= ';dbname='.$connectionInfo['database'];
			}
			
			$connection = new \PDO($dsn, $connectionInfo['username'], $connectionInfo['password']);
			$connection->exec('SET NAMES utf8;');
			return $connection;
		}
		catch (\PDOException $e) {
			\Sh\LoggerProvider::log('database', 'Erro ao tentar realizar conexão com banco. PDO: '.$e->getMessage());
			\Sh\LoggerProvider::log('error', 'Erro ao tentar realizar conexão com banco. PDO: '.$e->getMessage());
			var_dump($e);
			return false;
		}
	}
	
}