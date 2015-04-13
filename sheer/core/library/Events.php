<?php
namespace Sh;

/**
 * @author Guilherme
 *
 * Classe para escuta de eventos globais
 * Nesta qualquer um pode dar trigger e inserir escutas
 */
abstract class Events {
	
	use EventDrivenBehavior;
	
	/**
	 * Customizo o trigger para ser publico e qualquer um soltar
	 * 
	 * @param string $event
	 * @param \PDO $conn
	 */
	static public function trigger ($event, \PDO $conn = null) {
		self::executeTrigger($event, $conn);
	}
	
}

/**
 * @author Guilherme
 * Trait para incorporar as funções de controle de evento dentro de uma classe qualquer.
 * A classe que implementar esta Trait terá um controlador de eventos própria
 */
trait EventDrivenBehavior {

	static private $eventMapper = array();

	/**
	 * Gatilho para executar as funções de um evento
	 * @param string $event
	 * @param \PDO $conn
	 */
	static protected function trigger ($event, \PDO $conn = null) {
		
		$trigger = 'self::executeTrigger';
		
		call_user_func_array($trigger , func_get_args());
		
	}
	
	/**
	 * Método final para executar as funções a partir de um trigger
	 * @param string $event
	 * @param \PDO $conn
	 */
	static final protected function executeTrigger($event, \PDO $conn = null) {
		
		//tratando array de parametros
		$arguments = func_get_args();
		reset($arguments);
		unset($arguments[key($arguments)]);
		
		if( isset( self::$eventMapper[$event] ) ) {
			foreach (self::$eventMapper[$event] as $closure) {
				call_user_func_array($closure, $arguments);
			}
		}
		
	}

	static public function on ($event, \Closure $closure) {

		//verifico se o evento já está mapeado
		if( !isset(self::$eventMapper[$event]) ) {
			self::$eventMapper[$event] = array();
		}

		//inserindo evento no mapa
		self::$eventMapper[$event][] = $closure;
	}

}