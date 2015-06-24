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
	 * 
	 * @deprecated A partir de 15/05/2015, futuro Sheer 2.0 para utilizarmos self::parseList e self::parseContent
	 */
	public function parseData ( $data ) {
		
		return $data;
		
	}
	
	/**
	 * Método para efetuar o parse em uma lista.
	 * 	Esta deve ser organizada com idConteudo => array $conteudo
	 * 
	 * @param array $list
	 * 
	 * @return array
	 */
	public function parseList ( &$list ) {
		
		return $list;
		
	}
	
	/**
	 * Método para efetuar o parse em um conteudo
	 * 
	 * @param array $content => Dados direto do conteudo para efetuar o parse
	 * 
	 * @return array
	 */
	public function parseContent ( &$content ) {
		
		return $content;
		
	}
	
	public function setFilters ($filters) {
		$this->filters = $filters;
	}
	
	
	
}
