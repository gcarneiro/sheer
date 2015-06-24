<?php

namespace Sh;

class FatalErrorException extends \Sh\SheerException {
	
	public function __construct($info) {
		
		parent::__construct($info);
		$this->fatalException();
		
	}
	
	protected function fatalException () {
		
		var_dump($this);
		die;
		
	}
	
}