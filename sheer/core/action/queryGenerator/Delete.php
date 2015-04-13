<?php

namespace Sh\QueryGenerator;

/**
 * @author guilherme
 * 
 * Classe geradora de querys para inserção a partir de ActionHandlers
 *
 */
class Delete {
	
	/*
	 * Query padrão
	 */
	const QUERY_PATTERN = 'DELETE FROM {table} WHERE {pkComparison}';
	
	/**
	 * @var \Sh\ActionHandler
	 */
	protected $actionHandler;
	
	/**
	 * @var array
	 */
	protected $data;
	
	/*
	 * Elementos da query
	*/
	protected $table 			= '';
	protected $pkComparison 	= '';
	
	
	public function __construct(\Sh\ActionHandler $actionHandler) {
		//SALVO O ACTIONHANDLER
		$this->actionHandler = $actionHandler;
		
	}
	
	/**
	 * Método gerador da query
	 * @param array $data
	 * @return mixed
	 */
	public function getQuery ($data) {
		
		//GERO A COMPARACAO POR PK
		$this->createPKComparison($data);
		
		//GERO O NOME DA TABLE
		$this->createTableQuery();
		
		//CRIANDO A QUERY
		$replacement = array('{table}', '{pkComparison}');
		$value = array($this->table, $this->pkComparison);
		$query = str_replace($replacement, $value, self::QUERY_PATTERN);
		
		return $query;
		
	}
	
	protected function createPKComparison ($data) {
		$primaryKeyField = $this->actionHandler->getDataSource()->getPrimaryKey();
		$pkValue = $data[$primaryKeyField->getId()];
		
		$pkComparison = $primaryKeyField->getId().'="'.$pkValue.'"';
		$this->pkComparison = $pkComparison;
	}
	
	/**
	 * Método para gerar o campo "table" da query
	 */
	protected function createTableQuery () {
		$this->table = $this->actionHandler->getDataSource()->getTable();
	}
	
	
}