<?php

namespace Sh {
	
	/**
	 * Sheer Plataform Version 0.0.1
	 * @author Guilherme
	 * 24/08/2010
	 *
	 * This class will provider all log objects.
	 */
	abstract class LoggerProvider {
		
		protected static $logger = array();
		protected static $initialized = false;
		
		/**
		 * Setup configurated loggers
		 * TODO ACEITAR UM ARQUIVO DE LOGGERS.XML PARA O PROJETO TAMBÉM
		 */
		public static function init() {
			//Controle de inicialização
			if(self::$initialized == true){
				return false;
			}
			self::$initialized = true;
			
			//CAPTURANDO E PROCESSANDO ARQUIVOS DE LOGS CONFIGURADOS
			//Processamos o do Sheer
			if( is_file(SH_LOG_CONFIG_FILE) ) {
				$xmlFile = simplexml_load_file(SH_LOG_CONFIG_FILE);
				self::startLoggers($xmlFile);
			}
			//Processamos o do Projeto
			if( is_file(SH_PROJECT_LOG_CONFIG_FILE) ) {
				$xmlFile = simplexml_load_file(SH_PROJECT_LOG_CONFIG_FILE);
				self::startLoggers($xmlFile);
			}
		}
		
		/**
		 * Create all loggers objects
		 */
		private static function startLoggers( \SimpleXMLElement $xmlFile ) {
			
			
			foreach($xmlFile->logger as $l) {
				$attributes = $l->attributes();
				$logger					= null;
				$data 					= array();
				$data['id']				= (string) $attributes->id;
				$data['name']			= (string) $attributes->name;
				$data['type']			= (string) $attributes->type;
				$data['class']			= (string) $attributes->class;
				$data['file']			= (string) $l->file;
				$data['maxFileSize']	= (string) $l->maxFileSize;
				$logger = new Logger($data);
				
				self::$logger[$logger->getId()] = $logger;
			}
			
			
		}
		
		/**
		 * Return an Logger Object
		 * @return Logger
		 */
		public static function getLogger($id) {
			self::init();
			if(isset(self::$logger[$id])) {
				return self::$logger[$id];
			}
			else {return null;}
		}
		
		/**
		 * This will log a message without return a logger object
		 * @param string $id
		 * @param string $message
		 * @param string $simpleMessage Habilita log simples de linha única
		 */
		public static function log($id, $message, $simpleMessage=false) {
			$logger = self::getLogger($id);
			if($logger) {
				$logger->log($message, $simpleMessage);
			}
		}
	}
}