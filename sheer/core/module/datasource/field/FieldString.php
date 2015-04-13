<?php

namespace Sh;

class FieldString extends DataSourceField {
	
	protected $dataType = 'string';
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * Neste iremos remover todas as quebras de linha PHP_EOL
	 *
	 * @param string $data
	 * @return string
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		return $data;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato Sheer
	 *
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToSheer ($data) {
	
		$data = str_replace(PHP_EOL, '<br />', $data);
	
		return $data;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato primitivo
	 *
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToPrimitive ($data) {
	
		$data = str_replace(PHP_EOL, '<br />', $data);
	
		return $data;
	}
	
	/**
	 * Formato o dado do Sheer para input, isto é, altero os valores de <br /> para \n
	 * @param unknown $data
	 * @return mixed
	 */
	static public function formatSheerDataToInput ($data) {
		$data = str_replace('<br />', PHP_EOL, $data);
	
		return $data;
	}
	
}