<?php

namespace Sh;

abstract class SessionControl {
	
	static protected $initialized = false;
	static protected $sessionId = null;
	static protected $maxLifetime = 30;
	
	static public function init() {
		
		if( self::$initialized ) { return true; }
		
		//PRECISO CAPTURAR O IDENTIFICADOR DA SESSÃO DO USUÁRIO
		self::$sessionId = self::generateSessionId();
		//setando a sessão do usuário
		session_id(self::$sessionId);
		session_start();
		
		//INSERINDO INFORMAÇÕES DE CONTROLE NA SESSAO
		if( !isset($_SESSION['lifetime']) ) {
			$_SESSION['lifetime'] = array(
				'begin' => time(),
				'last' => $_SERVER['REQUEST_TIME']
			);
		}
		else {
			self::$maxLifetime = \Sh\Modules\variavel\variavel::getVariavelByAlias('sheer.session.maxLifeTime');
			self::$maxLifetime = (integer) self::$maxLifetime['valor'];
			
			//verifico quanto tempo faz do ultimo acesso
			$now = $_SERVER['REQUEST_TIME'];
			//calculando a quantos segundos que ocorreu o ultimo acesso
			$diferencaSeg = $now - $_SESSION['lifetime']['last'];
			$diferencaMin = $diferencaSeg/60;
			//caso a diferenca seja maior que 30 min. Deslogamos o usuário
			if( $diferencaMin > self::$maxLifetime ) {
				\Sh\AuthenticationControl::logoutUser();
			} 
			$_SESSION['lifetime']['last'] = $now;
		}
		
		self::$initialized = true;
		return true;
		
	} 
	
	/**
	 * Método responsável por gerar o identificador da sessão atual
	 * Este irá recuperar o identificador de sessão guardado em cookies no usuário
	 * 		Caso ele não encontre ele irá setar um novo cookie e irá recarregar a página para capturar o cookie
	 * 
	 * 
	 * @return Ambigous <string, NULL, unknown>
	 */
	static protected function generateSessionId () {
		
		$cookieName = 'sh_authc';
		$cookieValue = null;
		
		//CASO O COOKIE JÁ ESTEJA SETADO
		if( isset($_COOKIE[$cookieName]) ) {
			$cookieValue = $_COOKIE[$cookieName];
		}
		//CASO ELE NÃO ESTEJA SETADO, GERO UM NOVO
		else {
			$cookieValue = \Sh\Library::getUniqueIntegerCode(true);
			$domain = \Sh\ProjectConfig::getProjectConfiguration('domain');
			$path = \Sh\ProjectConfig::getProjectConfiguration('domainPath');
			
			//Removendo a porta do domínio
			$tmp = explode(':', $domain);
			$domain = $tmp[0];
			
			//120 dias
			$periodo1Dia = (60*60*24*120);
			$result = setcookie($cookieName, $cookieValue, time()+$periodo1Dia, $path, $domain, false, true);
		}
		
		//Gero o identificador da Sessao com o valor do COOKIE + Identificador do projeto + IP do usuário
		$projectId = \Sh\ProjectConfig::getProjectConfiguration('id');
		$userIp = \Sh\RuntimeInfo::getClientIp();
		$sessionId = md5($cookieValue.$projectId.$userIp);
		
		return $sessionId;
	}
	
	/**
	 * Retorna o Id da Sessão do usuario
	 * 
	 * @return string
	 */
	static public function getSessionId() {
		return self::$sessionId;
	}
	
}