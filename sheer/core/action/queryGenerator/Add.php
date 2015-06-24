<?php

namespace Sh\QueryGenerator;

/**
 * @author guilherme
 * 
 * Classe geradora de querys para inserção a partir de ActionHandlers
 *
 */
class Add {
	
	/*
	 * Query padrão
	 */
	const QUERY_PATTERN = 'INSERT INTO {table} {fieldlist} VALUES {values}';
	
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
	protected $fieldlist 	= '';
	protected $table 		= '';
	protected $values 		= '';
	
	
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
		
		//EFETUO O PROCESSAMENTO DO FIELDLIST E DOS VALORES
		$this->createFieldListAndValuesQuery($data);
		
		//GERO O NOME DA TABLE
		$this->createTableQuery();
		
		//CRIANDO A QUERY
		$replacement = array('{table}', '{fieldlist}', '{values}');
		$value = array($this->table, $this->fieldlist, $this->values);
		$query = str_replace($replacement, $value, self::QUERY_PATTERN);
		
		return $query;
		
	}
	
	/**
	 * Método para gerar o fieldList do datasource com os fields desejados
	 */
	protected function createFieldListAndValuesQuery ($data) {
		
		$queryFieldList	= '';
		$queryValues = '';
		$first = true;
		$fields = $this->actionHandler->getDataSource()->getFields(false);
		
		//itero por todos os fields gerando tanto o fieldList quanto capturando o seu valor
		foreach ( $fields as $field ) {
			if( !$first ) { 
				$queryFieldList .= ', ';
				$queryValues .= ', ';
			}
			
			//GERANDO O FIELDLIST
			$queryFieldList .= $field->getId();
			
			//GERANDO O VALOR
			
			//determinando o valor enviado [considero enviado se for array ou tiver pelo menos um caracter]
			$valorEnviado = false;
			if( isset($data[$field->getId()]) && ( is_array($data[$field->getId()]) || strlen($data[$field->getId()]) > 0 ) ) {
				$valorEnviado = true;
			}
			
			//se o valor foi enviado corretamente
			if( $valorEnviado ) {
				//Removo a conversao input=>primitive daqui e jogo pro actionClass para conseguir inserir arquivos
// 				$sqlValue = $field::formatInputDataToPrimitive($data[$field->getId()]);
				$sqlValue = addslashes($data[$field->getId()]);
				$queryValues .= '"'.$sqlValue.'"';
			}
			//o valor não foi enviado
			else {
				if( $field->getSetNullIfBlank() ) {
					$queryValues .= 'NULL';
				}
				else {
					$queryValues .= '""';
				}
			}
			
			
			
			$first = false;
		}
		
		$queryFieldList = '('.$queryFieldList.')';
		$queryValues = '('.$queryValues.')';
		
		$this->fieldlist = $queryFieldList;
		$this->values = $queryValues;
	}
	
	/**
	 * Método para gerar o campo "table" da query
	 */
	protected function createTableQuery () {
		$this->table = $this->actionHandler->getDataSource()->getTable();
	}
	
	
}