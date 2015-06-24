<?php

namespace Sh;


/**
 * @author guilherme
 *
 * Classe responsável por retornar valores para variaveis de forma runtime
 * Variáveis implementadas
 * 		datetime [
 * 			now
 * 			date
 * 			month
 * 			year
 * 			day
 * 			weekday
 * 			time
 * 			hour
 * 			minute
 * 			second
 * 		]
 * 		user [
 * 			id
 * 			nome
 * 			email
 * 			ip
 * 		]
 * 
 * TODO Implementar variaveis
 * 		sysvar
 * 		function
 * 		
 */
abstract class RuntimeVariables {
	
	/**
	 * Método que determina se o valor passado faz parte de um alias para Runtime Variable
	 * @param string $value
	 * @return boolean
	 */
	static public function isAliasValue ($alias) {
		//verificacao de string, alias values sao sempre strings
		if( !is_string($alias) ) {
			return false;
		}
		
		$path = explode('.', $alias);
		//verificamos se ele possui os dois elementos pelo menos
		if ( isset($path[0]) && isset($path[1]) ) {
			//verificando se é alias de runtime value
			switch ($path[0]) {
				case 'date':
				case 'datetime':
				case 'time':
				case 'user':
				case 'param':
				case 'post':
				case 'get':
					return true;
			}
		}
		//verificando se ele é um alias para o projeto
		return !!self::getProjectAliasValue($alias);
	}
	
	/**
	 * Vamos retornar o valor para o alias passado
	 * 
	 * 
	 * 
	 * @param string $alias
	 * @return undefined 
	 * 		Caso o alias seja um alias e for encontrado retornamos o seu valor
	 * 		Caso o alias seja um alias mas o seu valor não foi encontrado, iremos retornar null
	 * 		Caso o alias não seja um alias iremos retornar false
	 */
	static public function getAliasValue($alias) {
		
		$path = explode('.', $alias);
		
		switch ($path[0]) {
			case 'date': 
				return self::date($path); 
			case 'datetime': 
				return self::datetime($path); 
			case 'time': 
				return self::time($path); 
			case 'user': 
				return self::user($path);
			case 'param':
				return self::param($path);
			case 'post':
				return self::post($path);
			case 'get':
				return self::get($path);
			default: 
				return self::getProjectAliasValue($alias); 
		}
		
	}
	
	/**
	 * Método para buscar o alias pelo projeto e determinar se é/ou possui alias value
	 * 
	 * @param string $alias
	 * @return unknown
	 */
	static protected function getProjectAliasValue ( $alias ) {
		
		//verificando existencia da classe e da função do projeto para alias value
		if( !is_callable('\\Sh\\Project\\RuntimeVariables::getAliasValue') ) {
			return false;
		}
		$projectAlias = \Sh\Project\RuntimeVariables::getAliasValue($alias);
		return $projectAlias;
		
	}
	
	/**
	 * Recuperador de valores para runtime de parametro generico
	 * @param array $path
	 * @return string|NULL
	 */
	static protected function param ( $path ) {
		
		if( isset($_POST[$path[1]]) ) {
			return $_POST[$path[1]];
		}
		else if( isset($_GET[$path[1]]) ) {
			return $_GET[$path[1]];
		}
		return null;
	}
	
	/**
	 * Recuperador de valores para runtime de parametro POST
	 * @param array $path
	 * @return string|NULL
	 */
	static protected function post ( $path ) {
	
		if( isset($_POST[$path[1]]) ) {
			return $_POST[$path[1]];
		}
		return null;
	}
	
	/**
	 * Recuperador de valores para runtime de parametro POST
	 * @param array $path
	 * @return string|NULL
	 */
	static protected function get ( $path ) {
	
		if( isset($_GET[$path[1]]) ) {
			return $_GET[$path[1]];
		}
		return null;
	}
	
	/**
	 * Recuperador de valores para runtime variables datetime
	 * @param array $path
	 * @return string|NULL
	 */
	static protected function datetime($path) {
		
		$date = new \DateTime();
		
		switch ($path[1]) {
			case 'now':
				return $date->format('d/m/Y H:i:s');
			case 'date':
				return $date->format('d/m/Y');
			case 'day':
				return $date->format('d');
			case 'month':
				return $date->format('m');
			case 'year':
				return $date->format('Y');
			case 'weekday':
				return $date->format('N');
			case 'time':
				return $date->format('H:i:s');
			case 'hour':
				return $date->format('H');
			case 'minute':
				return $date->format('i');
			case 'second':
				return $date->format('s');
			case 'tomorrow':
				$date->add(new \DateInterval('P01D'));
				return $date->format('d/m/Y H:i:s');
			case 'yesterday':
				$date->sub(new \DateInterval('P01D'));
				return $date->format('d/m/Y H:i:s');
			case 'firstDay':
				return $date->format('01/m/Y H:i:s');
			case 'lastDay':
				return $date->format('t/m/Y H:i:s');
			default: 
				return null;
		}
	}
	
	/**
	 * Recuperador de valores para runtime variables time
	 * @param array $path
	 * @return string|NULL
	 */
	static protected function time($path) {
	
		$date = new \DateTime();
	
		switch ($path[1]) {
			case 'now':
				return $date->format('H:i:s');
			case 'time':
				return $date->format('H:i:s');
			case 'hour':
				return $date->format('H');
			case 'minute':
				return $date->format('i');
			case 'second':
				return $date->format('s');
			default:
				return null;
		}
	}
	
	/**
	 * Recuperador de valores para runtime variables date
	 * @param array $path
	 * @return string|NULL
	 */
	static protected function date($path) {
	
		$date = new \DateTime();
		
		switch ($path[1]) {
			case 'now':
				return $date->format('d/m/Y');
			case 'date':
				return $date->format('d/m/Y');
			case 'day':
				return $date->format('d');
			case 'month':
				return $date->format('m');
			case 'year':
				return $date->format('Y');
			case 'weekday':
				return $date->format('N');
			case 'tomorrow':
				$date->add(new \DateInterval('P01D'));
				return $date->format('d/m/Y');
			case 'yesterday':
				$date->sub(new \DateInterval('P01D'));
				return $date->format('d/m/Y');
			case 'firstDay':
				return $date->format('01/m/Y');
			case 'lastDay':
				return $date->format('t/m/Y');
			default:
				return null;
		}
	}

	/**
	 * Recuperador de valores para runtime variables do usuário autenticado
	 * @param array $path
	 * @return string|NULL
	 */
	static protected function user($path) {
		
		$userInfo = \Sh\AuthenticationControl::getAuthenticatedUserInfo();
		$response = null;
		
		switch ( $path[1] ) {
			case 'id':
				$response = $userInfo['id'];
				break;
			case 'idUserGroup':
				$response = $userInfo['idUserGroup'];
				break;
			case 'name':
			case 'nome':
				$response = $userInfo['nome'];
				break;
			case 'email':
				$response = $userInfo['email'];
				break;
			case 'login':
				$response = $userInfo['login'];
				break;
			case 'habilitado':
				$response = $userInfo['habilitado'];
				break;
			case 'multiSecao':
				$response = $userInfo['multiSecao'];
				break;
			case 'ip':
				$response = \Sh\RuntimeInfo::getClientIp();
				break;
			default:
				return null;
		}
		return $response;
	}
	
}