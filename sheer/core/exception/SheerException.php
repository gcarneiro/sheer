<?php

namespace Sh;

class SheerException extends \Exception {
	
	protected $info;
	protected $logger = 'full';
	
	/**
	 * @param array|string $info [
	 * 		'message'
	 * 		'code'
	 * ]
	 */
	public function __construct($info) {
		
		//Determinando a mensagem da excecao
		$object = array(
			'code' => null,
			'message' => null
		);
		//verificando se foi enviado uma string
		if( is_string($info) ) {
			$object['message'] = $info;
		}
		//verificando se foi enviado uma string
		else if( is_array($info) ) {
			$object = $info;
		}
		
		$this->info = $object;
		parent::__construct($object['message']);
		
		//SALVANDO EM LOG
		if( $this->logger ) {
			$codigo = 'NULL';
			if( isset($object['code']) && $object['code'] != NULL ) {
				$codigo = $object['code'];
			}
			\Sh\LoggerProvider::log($this->logger, '['.$codigo.'] '.$object['message']);
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