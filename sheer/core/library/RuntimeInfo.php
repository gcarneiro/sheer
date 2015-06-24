<?php

namespace Sh;
	
abstract class RuntimeInfo {
	
	static protected $initialized 	= false;
	static protected $clientIp		= null;
	static protected $serverIp 		= null;
	static protected $browserProtocol = null;
	static protected $cookieEnabled	= false;
	
	static public function init() {
		
		if( self::$initialized ) { return true; }
		
		self::$clientIp			= $_SERVER['REMOTE_ADDR'];
		self::$serverIp 		= $_SERVER['SERVER_NAME'];
		self::$browserProtocol 	= $_SERVER['SERVER_PROTOCOL'];
		//Inicializando controle de agente
		\Sh\RuntimeAgentInfo::init();
		
		//Verificando existencia de cookies
		self::verifyCookies();
		
		self::$initialized = true;
		return true;
	}
	
	/**
	 * Método para retornar o protocolo utilizado no request atual
	 * 
	 * @return string
	 */
	static public function getRequestProtocol () {
		if( strpos(strtolower(self::$browserProtocol), 'http/') !== false ) {
			return 'http';
		}
		else if ( strpos(strtolower(self::$browserProtocol), 'https/') !== false ) {
			return 'https';
		}
		else {
			return 'http';
		}
	}
	
	/**
	 * Método para retornar a porta acessada pelo usuário
	 *
	 * @return string
	 */
	static public function getRequestPort () {
		if( isset($_SERVER) && isset($_SERVER['SERVER_PORT']) ) {
			return $_SERVER['SERVER_PORT'];
		}
	}
	
	/**
	 * Método para retornar o baseUrl da página atual
	 * Este já irá retornar a url base com o protocolo de acesso
	 * 
	 * @return mixed
	 */
	static public function getBaseUrl () {
		
		$domain 		= \Sh\ProjectConfig::getProjectConfiguration('domain');
		$domainPath 	= \Sh\ProjectConfig::getProjectConfiguration('domainPath');
		$protocol 		= self::getRequestProtocol();
		
		$baseUrl = $domain.'/'.$domainPath.'/';
		$baseUrl = $protocol.'://'.str_replace('//', '/', $baseUrl);
		return $baseUrl;
		
	}
	
	/**
	 * @author Guilherme
	 * Nov 13, 2010
	 * 
	 * Retorna ip do cliente
	 * @return string
	 */
	static public function getClientIp() {
		return self::$clientIp;
	}
	/**
	 * @author Guilherme
	 * Nov 13, 2010
	 * 
	 * Retorna Ip do Servidor de Aplicação
	 * @return string
	 */
	static public function getServerIp() {
		return self::$serverIp;
	}
	/**
	 * @author Guilherme
	 * Nov 13, 2010
	 * 
	 * Pega protocolo do browser do cliente
	 * @return string
	 */
	static public function getBrowserProtocol() {
		return self::$browserProtocol;
	}
	
	/**
	 * Método para verificar se os cookies estão habilitados nesta requisição
	 */
	static protected function verifyCookies () {
		self::$cookieEnabled = isset($_COOKIE['sh_isCookieEnabled']) && $_COOKIE['sh_isCookieEnabled'];
		return;		
	}
	
	/**
	 * Determina se os Cookies estão habilitados
	 * @return boolean
	 */
	static public function isCookieEnabled () {
		return self::$cookieEnabled;
	}
	
	/**
	 * @author Guilherme
	 * Nov 13, 2010
	 * 
	 * Retorna a url acessada atualmente, sem informações de domínio ou de static path
	 * @return string
	 */
// 	static public function getCurrentUrl() {
// 		$staticAddress = \Sheer\ProjectInfo::getRootUrl();
// 		$dinamicAddress = $_SERVER['REQUEST_URI'];
// 		$currentAddress = \Sheer\ProjectInfo::getDomain().$dinamicAddress;
		
// 		//removendo caracteres nao desejados
// 		$staticAddress = str_replace('http://', '/', $staticAddress);
// 		$currentAddress = str_replace('http://', '/', $currentAddress);
// 		$currentAddress = str_replace('//', '/', $currentAddress);
		
// 		$currentAddress = str_replace($staticAddress, '', $currentAddress);
		
// 		if(strlen($currentAddress)==0) { $currentAddress = '/'; }
		
// 		return $currentAddress;
// 	}
}
