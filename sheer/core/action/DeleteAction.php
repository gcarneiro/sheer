<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Action Padrão para deleção de conteúdo
 *
 */
final class DeleteAction extends GenericAction {
	
	protected $generatePkValue = false;
	protected $processAddons = true;
	protected $ignorePkValidation = false;
	protected $parseData = true;
	
	protected function doAction($data) {
		
		//CAPTURO SOMENTE OS DADOS NECESSÁRIOS PARA A OPERAÇÃO
		$dataFinal = $this->extractPrimitiveData($data);
		
		//CRIO O GERADOR DE QUERYs
		$queryGenerator = new \Sh\QueryGenerator\Delete($this->actionHandler);
		$query = $queryGenerator->getQuery($dataFinal);
		\Sh\Library::logQuery($query);
		
		//EXECUTO A QUERY
		$affectedRows = $this->connection->exec($query);
		if( $affectedRows === false ) { $this->throwPDOError(); }
		
		return array(
			'status' => true,
			'code' => null,
			'data' => $dataFinal
		);
		
	}
	
	/**
	 * @see \Sh\GenericAction::prepare()
	 * Para o action de deleção preciso remover todos os campos do dataSource e somente considerar o primaryKey que é o unico importante para deleção.
	 */
	protected function prepare($data) {
		$fields = $this->actionHandler->getDataSource()->getFields(false);
		foreach ($fields as $idField=>$field) {
			if( $field->isPrimaryKey() ) {
				continue;
			}
			$this->actionHandler->getDataSource()->removeField($field->getId());
		}
		return $data;
	}
	
}