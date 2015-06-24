<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Setup de módulos do SHEER
 *
 */
abstract class SheerSetup {
	
	static protected $initialized = false;
	
	static protected $moduleControl = array();
	
	static public function init () {
	
		if( self::$initialized ) {
			return true;
		}
		
		//ANTES DE TUDO INICIALIZO OS MÓDULO
		\Sh\ModuleControl::init();
		//INICIALIZO SESSAO E INFORMACOES DA REQUISICAO CASO SEJA POR WEB SERVER
		if( !SH_CLI ) {
			\Sh\RuntimeInfo::init();
			\Sh\SessionControl::init();
			\Sh\AuthenticationControl::init();
		}
		//OUTRAS
		\Sh\ImageLibrary::init();
		\Sh\Cielo::init();
		\Sh\Facebook::init();
		
		self::$initialized = true;
	}
	
}