<?php

namespace Sh;

class FieldHtml extends DataSourceField {
	
	protected $dataType 			= 'html';
	protected $renderType			= 'html';
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * Neste iremos remover todas as quebras de linha PHP_EOL
	 * 
	 * @param string $data
	 * @return string
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		
		$data = str_replace(PHP_EOL, '', $data);
		
		return $data;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato Sheer
	 * 
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToSheer ($data) {
		
		$data = str_replace(PHP_EOL, '', $data);
		
		return $data;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato primitivo
	 * 
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToPrimitive ($data) {
		
		$data = str_replace(PHP_EOL, '', $data);
		
		return $data;
	}
	
}