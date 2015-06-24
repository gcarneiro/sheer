<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Action Padrão para inserção de conteúdo
 *
 */
class AddAction extends \Sh\GenericAction {
	
	protected $generatePkValue = true;
	protected $processAddons = true;
	protected $ignorePkValidation = false;
	protected $parseData = true;
	
	protected function doAction($data) {
		
		//CAPTURO SOMENTE OS DADOS NECESSÁRIOS PARA A OPERAÇÃO
		$dataFinal = $this->extractPrimitiveData($data);
		
		//CRIO O GERADOR DE QUERYs
		$queryGenerator = new \Sh\QueryGenerator\Add($this->actionHandler);
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
	 * Addon para guardar os metadados da criação do conteudo
	 * @param array $result
	 */
	protected function addonPublicationMetadata ($data) {
		
		$now = date('Y-m-d H:i:s');
		$userId = \Sh\AuthenticationControl::getAuthenticatedUserInfo('id');
		
		//criando objeto
		$contentControl = array(
			'contentId'		=> $this->contentPrimaryKey,
			'datasourceId' 	=> $this->actionHandler->getModuleId().'/'.$this->actionHandler->getDataSource()->getId(),
			'created' 		=> $now,
			'createdBy' 	=> $userId,
			'updated' 		=> $now,
			'updatedBy' 	=> $userId,
			'revision' 		=> 1
		);
		
		$query = 'INSERT INTO sh_publicationMetadata VALUES("'.$contentControl['contentId'].'", "'.$contentControl['datasourceId'].'", "'.$contentControl['created'].'", '.\Sh\DatabaseLibrary::getStatement($contentControl['createdBy']).', "'.$contentControl['updated'].'", '.\Sh\DatabaseLibrary::getStatement($contentControl['createdBy']).', NULL, NULL, 1)';
		$response = $this->connection->exec($query);
		if( $response === false ) {
			$this->throwPDOError();
		}
	
	}
	
}