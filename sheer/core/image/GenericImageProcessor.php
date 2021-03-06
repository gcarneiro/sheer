<?php

namespace Sh;

/**
 * @author Guilherme
 * 
 * Classe genérica para processamento de imagens a ser extendida e declarada no PicturesMap
 *
 */
abstract class GenericImageProcessor {
	
	/**
	 * @var \Imagick
	 */
	protected $image;
	/**
	 * @var array
	 */
	protected $info;
	/**
	 * @var array
	 */
	protected $pictureMap;
	
	/**
	 * Método construtor para o Processador de Imagens
	 * 
	 * @param \Imagick $image
	 * @param array $info
	 */
	final public function __construct( \Imagick &$image, $info, $pictureMap ) {
		
		$this->image = $image;
		$this->info = $info;
		$this->pictureMap = $pictureMap;
		
	}
	
	/**
	 * Método a ser chamado pelo ImageLibrary
	 * Este método irá chamar o método extendível por parte do desenvolvedor
	 * 
	 * @return Imagick
	 */
	final public function execute() {
		$this->process();
		return $this->image;
	}
	
	abstract public function process ();
	
}