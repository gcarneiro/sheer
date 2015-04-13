<?php

namespace Sh;

class SheerException extends \Exception {
	
	protected $info;
	protected $logger = 'full';
	
	/**
	 * @param array $info [
	 * 		'message'
	 * 		'code'
	 * ]
	 */
	public function __construct($info) {
		
		$this->info = $info;
		parent::__construct($info['message']);
		
		//SALVANDO EM LOG
		if( $this->logger ) {
			$codigo = 'NULL';
			if( isset($info['code']) && $info['code'] != NULL ) {
				$codigo = $info['code'];
			}
			\Sh\LoggerProvider::log($this->logger, '['.$codigo.'] '.$info['message']);
		}
	}
	
	public function getErrorCode() {
		$info = $this->getInfo();
		if( !isset($info['code']) ) { $info['code'] = null; }
		return $info['code'];
	}
	
	public function getErrorMessage() {
		$info = $this->getInfo();
		if( !isset($info['message']) ) { $info['message'] = null; }
		return $info['message'];
	}
	
	
	public function getInfo () {
		return $this->info;
	}
	
	
}