<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Exceções para serem utilizadas em casos de testes
 *
 */
class TestException extends \Sh\SheerException {
	
	protected $logger = 'testing';
	
}