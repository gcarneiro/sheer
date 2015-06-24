<?php

namespace Sh\QueryGenerator;

/**
 * @author guilherme
 * 
 * Classe geradora de querys para inserção a partir de ActionHandlers
 *
 */
class Update {
	
	/*
	 * Query padrão
	 */
	const QUERY_PATTERN = 'UPDATE {table} SET {fieldlist} WHERE {pkComparison}';
	
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
	protected $fieldlist 		= '';
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
		
		//EFETUO O PROCESSAMENTO DO FIELDLIST E DOS VALORES
		$this->createFieldListAndValuesQuery($data);
		
		//GERO A COMPARACAO POR PK
		$this->createPKComparison($data);
		
		//GERO O NOME DA TABLE
		$this->createTableQuery();
		
		//CRIANDO A QUERY
		$replacement = array('{table}', '{fieldlist}', '{pkComparison}');
		$value = array($this->table, $this->fieldlist, $this->pkComparison);
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
	 * Método para gerar o fieldList do datasource com os fields desejados
	 */
	protected function createFieldListAndValuesQuery ($data) {
		
		$queryFieldList	= '';
		$fields = $this->actionHandler->getDataSource()->getFields(false);
		
		//itero por todos os fields gerando tanto o fieldList quanto capturando o seu valor
		//somente escapo o primaryKey
		foreach ( $fields as $field ) {
			
			//verifico se é o primaryKey para não alterar
			if( $field->isPrimaryKey() ) {
				continue;
			}
			//inserindo separador
			if( $queryFieldList ) { 
				$queryFieldList .= ', ';
			}
			
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
				$queryFieldList .= $field->getId() .'="'.$sqlValue.'"';
			}
			//o valor não foi enviado
			else {
				if( $field->getSetNullIfBlank() ) {
					$queryFieldList .= $field->getId() .'=NULL';
				}
				else {
					$queryFieldList .= $field->getId() .'=""';
				}
			}
			
		}
		
		$this->fieldlist = $queryFieldList;
	}
	
	/**
	 * Método para gerar o campo "table" da query
	 */
	protected function createTableQuery () {
		$this->table = $this->actionHandler->getDataSource()->getTable();
	}
	
	
}