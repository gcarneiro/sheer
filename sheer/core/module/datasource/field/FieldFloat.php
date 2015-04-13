<?php

namespace Sh;

class FieldFloat extends DataSourceField {
	
	protected $dataType = 'float';
	protected $mask				= 'float';
	protected $validationType	= 'number';
	
	public function validateValueInput ($data) {
		if( is_numeric($data) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * @param unknown $data
	 * @return float
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		//Se o input não for numerico ou for null retorno null
		if( !is_numeric($data) || $data === null ) { return null; }
		//retorno o seu float
		return (float) $data;
	}
	
	/**
	 * //FIXME avaliar como devemos fazer essa conversão, pois o dados inputado pelo usuário deveria vir com vírgula
	 * Método para formatar o dado inputado para o formato primitivo
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToPrimitive ($data) {
		//Se o input não for numerico ou for null retorno null
		if( !is_numeric($data) || $data === null ) { return null; }
		//retorno o seu float
		return (float) $data;
	}
	
}