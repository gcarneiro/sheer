<?php

namespace Sh;

class FieldInteger extends DataSourceField {
	
	protected $dataType = 'integer';
	protected $mask				= 'integer';
	protected $validationType	= 'integer';
	
	public function validateValueInput ($data) {
		
		//preciso remover os "." e trocar o "," por "."
		$data = str_replace(array('.', ','), '', $data);
		
		if( is_numeric($data) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * @param unknown $data
	 * @return integer
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		
		//Se o input não for numerico ou for null retorno null
		if( !is_numeric($data) || $data === null ) { return null; }
		//retorno o seu inteiro
		return (integer) $data;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato primitivo
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToPrimitive ($data) {
		
		//Removendo . e ,
		$data = str_replace(array('.', ','), '', $data);
		
		//Se o input não for numerico ou for null retorno null
		if( !is_numeric($data) || $data === null ) { return null; }
		//retorno o seu inteiro
		return (integer) $data;
	}
	
}