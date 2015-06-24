<?php

namespace Sh;

class FieldDinheiro extends FieldFloat {
	
	protected $dataType = 'dinheiro';
	protected $mask				= 'decimal';
	protected $validationType	= 'number';
	
	public function validateValueInput ($data) {
		
		//preciso remover os "." e trocar o "," por "."
		$data = str_replace(array('.', ','), array('', '.'), $data);
		
		if( is_numeric($data) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * @param float $data
	 * @return string
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		
		if( $data === null ) { return null; }
		
		$data = number_format((float) $data, 2, ',', '.');
		
		return $data;
	}
	
	/**
	 * //FIXME avaliar como devemos fazer essa conversão, pois o dados inputado pelo usuário deveria vir com vírgula
	 * Método para formatar o dado inputado para o formato primitivo
	 * @param string $data
	 * @return float
	 */
	static public function formatInputDataToPrimitive ($data) {
		
		//preciso remover os "." e trocar o "," por "."
		$data = str_replace(array('.', ','), array('', '.'), (string) $data);
		//Se não for numérico retorno null
		if( !is_numeric($data) ) {
			return null;
		}
		//arredondo ele para duas casas apenas por se tratar de dinheiro
		$data = round((float) $data, 2);
		
		if( $data === null ) { return null; }
		return (float) $data;
	}
	
	/**
	 * Método para formatar o dado do Sheer para a forma que um usuário o inputa
	 * @param string $data
	 * @return number
	 */
	static public function formatSheerDataToPrimitive ($data) {
		//preciso remover os "." e trocar o "," por "."
		$data = str_replace(array('.', ','), array('', '.'), (string) $data);
		//Se não for numérico retorno null
		if( !is_numeric($data) ) {
			return null;
		}
		return (float) $data;
	}
		
}