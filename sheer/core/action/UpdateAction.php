<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Action Padrão para atualização de conteúdo
 *
 */
class UpdateAction extends \Sh\GenericAction {
	
	protected $generatePkValue = false;
	protected $processAddons = true;
	protected $ignorePkValidation = false;
	protected $parseData = true;
	
	protected function doAction($data) {
		
		//CAPTURO SOMENTE OS DADOS NECESSÁRIOS PARA A OPERAÇÃO
		$dataFinal = $this->extractPrimitiveData($data);
		
		//CRIO O GERADOR DE QUERYs
		$queryGenerator = new \Sh\QueryGenerator\Update($this->actionHandler);
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
	 * Iremos tratar o Update para fazermos duas operações principais
	 * 		- Remover a obrigatoriedade de todos os campos menos PK
	 * 		- Remover os campos que não tiveram seu valor enviado para que não atuem na atualização
	 */
	protected function prepare($data) {
		
		$fs = $this->actionHandler->getDataSource()->getFields(false);

		foreach ( $fs as $idField=>$field ) {
			
			//REMOVE O CAMPO QUNADO NÃO FOR PK E NÃO TENHA SIDO ENVIADO
			if( !$field->isPrimaryKey() && !array_key_exists($idField, $data) ) {
				$this->actionHandler->getDataSource()->removeField($field->getId());
			}
			
			//ANTIGO
			/*
			//REMOVE A OBRIGATORIEDADE DO CAMPO NÃO SENDO PK
			if( !$field->isPrimaryKey() ) {
				$field->setRequired(false);
			}
			
			//VERIFICO SE O DADO FOI ENVIADO PARA REMOVE-LO
			//se o dado não foi enviado e não é required irei remove-lo
			if( !array_key_exists($idField, $data) && !$field->getRequired() ) {
				$this->actionHandler->getDataSource()->removeField($field->getId());
			}
			 */
		}
		
		return $data;
		
	}
	
	/**
	 * Addon para guardar os metadados da atualizacao do conteudo
	 * @param array $result
	 */
	protected function addonPublicationMetadata ($result) {
	
		$now = date('Y-m-d H:i:s');
		$userId = \Sh\AuthenticationControl::getAuthenticatedUserInfo('id');
		$contentId = $this->contentPrimaryKey;
		$datasourceId = $this->actionHandler->getModuleId().'/'.$this->actionHandler->getDataSource()->getId();
	
		$query = 'UPDATE sh_publicationMetadata SET updated="'.$now.'", updatedBy='.\Sh\DatabaseLibrary::getStatement($userId).', revision=revision+1 WHERE contentId="'.$contentId.'" && datasourceId="'.$datasourceId.'" LIMIT 1;';
		$response = $this->connection->exec($query);
		if( $response === false ) {
			$this->throwPDOError();
		}
	
	}
	
}
