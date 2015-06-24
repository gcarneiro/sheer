<?php

namespace Sh;

abstract class ProjectConfig {
	
	static protected $initialized = false;
	static protected $configuration = null;
	
	/**
	 * Método de inicialização da classe e seus valores
	 * Este implica em erro fatal em caso de não conseguir ler/parsear arquivo de configurações
	 * @return boolean
	 */
	static protected function init() {
		
		if( self::$initialized ) { return true; }
		
		$contentConfig = file_get_contents(SH_PROJECT_CONFIG_JSON);
		$config = json_decode($contentConfig, true);
		
		if( $config === null ) {
			throw new \Sh\FatalErrorException(array(
				'message'	=> 'Erro Fatal ao tentar recuperar dados de configuração do projeto',
				'code'		=> 'SFE_XXXX'
			));
		}
		
		/*
		 * @date 15.01.14
		 * Processar as informações de domínios permitidos e qual domínio está sendo utilizado
		 * Neste iremos verificar o dominio acessado e escolher entre um dos permitidos, caso não encontre encerro a execução
		 */
		if( strrpos($config['project']['domain'], ';') ) {
			$dominios = explode(';', $config['project']['domain']);
			//Verificando se temos o domínio dentro dos permitidos
			$dominioAcessado = \Sh\RuntimeInfo::getServerIp();
			//Operando para cada domínio removendo a sua porta de acesso caso exista
			$dominioEncontrado = null;
			foreach ( $dominios as $idDom=>$dom ) {
				//removendo porta
				$tmp = array(0=>$dom);
				if( strrpos($dom, ':') !==false ) {
					$tmp = explode(':', $dom);
				}
				//verificando dominio
				if( $tmp[0] == $dominioAcessado ) {
					$dominioEncontrado = $dom;
				}
			}
			//Verificando se o domínio acessado é permitido
			if( $dominioEncontrado === null && !SH_CLI ) {
				throw new \Sh\FatalErrorException(array(
					'code' => null,
					'message' => 'O domínio acessado não é permitido para acesso a esse projeto'
				));
			}
			//dominio acessado é permitido
			$config['project']['domain'] = $dominioEncontrado;
		}
			
		self::$configuration = $config;
		
		//VERIFICANDO SE A VARIAVEL DE AMBIENTE "environment" ESTA SETADA
		//@deprecated não iremos mais utilizar isto pois era uma gambiarra para o imagick
		if( !isset(self::$configuration['project']['environment']) ) {
			self::$configuration['project']['environment'] = 'linux';
		}
		else {
			switch (self::$configuration['project']['environment']) {
				case 'linux': case 'windows':
					break;
				default:
					self::$configuration['project']['environment'] = 'linux';
			}
		}
		
		return true;
		
	}
	
	/**
	 * Método para recuperar as configurações do Projeto.
	 * 
	 * @param string $info
	 */
	static public function getProjectConfigurationSettings () {
	
		self::init();
		$configuration = null;
		if( isset(self::$configuration['settings']) ) {
			$configuration = self::$configuration['settings'];
		}
		return $configuration;
	}
	
	/**
	 * Método para recuperar as configurações do Projeto.
	 * É possível recuperar todas as informações do projeto ou somente um dado específico
	 * 
	 * @param string $info
	 * @return array|string
	 * 		Se o parametro $info for enviado, iremos tentar buscar a configuração em questão e retorna-la
	 * 			Informações disponíveis [
	 * 				"id", "name", "description", "domain", "domainPath"
	 * 			]
	 * 		Se não tiver sido enviado iremos retornar todo o objeto de configurações do projeto
	 * 
	 */
	static public function getProjectConfiguration ($info=null) {
		
		self::init();
		$configuration = null;
		
		if( !$info ) {
			$configuration = self::$configuration['project'];
		}
		else {
			if( isset(self::$configuration['project'][$info]) ) {
				$configuration = self::$configuration['project'][$info];
			}
		}
		return $configuration;
	}
	
	/**
	 * Método para recuperar as configurações de acesso a um banco de dados
	 * É possível o acesso direto a uma conexão específica enviando o parametro idConnection
	 * 
	 * @param string $idConnection
	 * @return array
	 * 		Se o id da conexão não for enviado retornamos um array listando todos as conexões existentes
	 * 		Se for enviado retornamos os dados da conexão se for encontrado e NULL caso a conexão não seja encontrada
	 */
	static public function getDatabaseConfiguration ($idConnection = null) {
		
		self::init();
		
		$connection = null;
		if( $idConnection === null ) {
			$connection = self::$configuration['database'];
		}
		else {
			if( isset(self::$configuration['database']) && isset(self::$configuration['database'][$idConnection]) ) {
				$connection = self::$configuration['database'][$idConnection];
			}
		}
		return $connection;
	}
	
	/**
	 * Método para recuperar as configurações de acesso a um servidor de email
	 * É possível o acesso direto a um servidor específico enviando o parametro idConnection
	 * 
	 * @param string $idConnection
	 * @return array
	 * 		Se o id do servidor não for enviado retornamos um array listando todos os servidores disponíveis
	 * 		Se for enviado retornamos os dados do servidor se for encontrado e NULL caso o servidor não seja encontrada
	 */
	static public function getMailerConfiguration ($idMailer = null) {
		
		self::init();
		
		$mailer = null;
		if( $idMailer === null ) {
			$mailer = self::$configuration['mailer'];
		}
		else {
			if( isset(self::$configuration['mailer']) && isset(self::$configuration['mailer'][$idMailer]) ) {
				$mailer = self::$configuration['mailer'][$idMailer];
			}
		}
		return $mailer;
	}
	
	/**
	 * Recupera o controlador do facebook para a aplicação
	 * @return [enable', 'appId', 'appSecret']
	 */
	static public function getFacebookConfiguration () {
		$facebook = null;	
		
		if( isset(self::$configuration['facebook']) ) {
			$facebook = self::$configuration['facebook'];
		}
		else {
			$facebook = [
				'enable' => false,
				'appId' => null,
				'appSecret' => null
			];
		}
		
		return $facebook;
		
	}
	
	/**
	 * Recupera o controlador do facebook para a aplicação
	 * @return [enable', 'appId', 'appSecret', 'apiKey']
	 */
	static public function getGoogleConfiguration () {
		$google = null;
		
		if( isset(self::$configuration['google']) ) {
			$google = self::$configuration['google'];
		}
		else {
			$google = [
				'enable' => false,
				'appId' => null,
				'appSecret' => null,
				'apiKey' => null
			];
		}
		
		return $google;
		
	}
	
	/**
	 * Recupera o controlador do instagram para a aplicação
	 * @return [enable', 'appId', 'appSecret']
	 */
	static public function getInstagramConfiguration () {
		$instagram = null;	
		
		if( isset(self::$configuration['instagram']) ) {
			$instagram = self::$configuration['instagram'];
		}
		else {
			$instagram = [
				'enable' => false,
				'appId' => null,
				'appSecret' => null
			];
		}
		
		return $instagram;
		
	}
	
	
	
}