<?php

namespace Sh;

/**
 * @author Guilherme
 * 
 * Controlador geral do Facebook para a aplicação
 *
 */
abstract class Facebook {
	
	static protected $inicializado = false;
	
	/**
	 * Método para inicializar o FacebookAPI
	 * @throws \Sh\SheerException
	*/
	static public function init () {
	
		//verificação de inicialização
		if( self::$inicializado ) {
			return;
		}
		self::$inicializado = true;
		
		$facebook = \Sh\ProjectConfig::getFacebookConfiguration();
		if( $facebook['enable'] ) {
			\Facebook\FacebookSession::setDefaultApplication($facebook['appId'], $facebook['appSecret']);
		}
	
	}
	
	/**
	 * Método para converter o gender do facebook em 'M'|'F'|'NULL'
	 * 
	 * @param string $gender
	 * @return string|NULL
	 */
	static public function getSexoFromGender ( $gender ) {
		
		if( $gender == 'male' ) {
			return 'M';
		}		
		else if ( $gender == 'female' ) {
			return 'F';
		}
		return null;
	}
}