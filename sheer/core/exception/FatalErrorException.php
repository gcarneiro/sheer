<?php

namespace Sh;

class FatalErrorException extends \Sh\SheerException {
	
	public function __construct($info) {
		
		parent::__construct($info);
		$this->fatalException();
		
	}
	
	protected function fatalException () {
		
		if( $this->getErrorCode() == 'SPR_0001' ) {
			\Sh\Library::Redirect('.');
			exit;
		}
		else {
			\Sh\Library::Redirect('index.php?p=shErrorPage&m='.\urlencode($this->getErrorMessage()));
			exit;
		}
		
		var_dump($this);
		die;
		
	}
	
}