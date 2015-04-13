<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Exceções para serem utilizadas nos erros com banco de dados
 *
 */
class DatabaseException extends \Sh\SheerException {
	
	protected $logger = 'database';
	
}