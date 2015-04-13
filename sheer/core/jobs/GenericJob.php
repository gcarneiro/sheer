<?php

namespace Sh;

/**
 * @author Guilherme
 * 
 * Classe padrão para jobs
 *
 */
abstract class GenericJob {
	
	protected $moment;
	
	public final function __construct( \DateTime $horario ) {
		$this->moment = $horario;
	}
	
	/**
	 * Método a ser extendido pelos jobs que serão executados
	 * 
	 * @param \DateTime $horario
	 */
	abstract public function run ( ) ;
	
}