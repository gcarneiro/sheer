<?php

namespace Sh;

abstract class GenericDataParser {
	
	protected $filters = array();
	
	/**
	 * Método para tratar os filtros do ContentProvider
	 * Este recebrá os filtros recebidos diretamente pelo ContentProvider e poderá executar operações sobre esses dados.
	 * 
	 * @param array $filters
	 * @return array
	 */
	public function parseFilters ( $filters ) {
		
		return $filters;
		
	}
	
	/**
	 * Método para tratar os dados recuperados do ContentProvider
	 * Irá receber os dados depois de tratados e poderá executar operações sobre eles.
	 * 
	 * @param array $data
	 * @return array
	 */
	public function parseData ( $data ) {
		
		return $data;
		
	}
	
	public function setFilters ($filters) {
		$this->filters = $filters;
	}
	
	
	
}
