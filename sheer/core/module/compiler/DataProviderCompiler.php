<?php

namespace Sh;

abstract class DataProviderCompiler {
	
	/**
	 * Váriavel de apoio para controlarmos os indexes dos relacioanentos
	 */
	static protected $relationIndex = 1;
	
	/**
	 * Variavel de apoio para obtermos o index do relationPath imediatamente
	 */
	static protected $relationsPathsIndexMap = array();
	
	
	/**
	 * Método compilador de DataProvider, Fazemos
	 * 		compilar as informações básicas, 
	 * 		processar relacionamentos
	 * 		devolver o DataProvider completo
	 * 
	 * @param \SimpleXMLElement $xmlDataProvider
	 * @param \Sh\Module $module
	 * @throws \Sh\SheerException
	 * @return \Sh\DataProvider
	 */
	static public function compile(\SimpleXMLElement $xmlDataProvider, \Sh\Module $module) {
		
		$id = (string) $xmlDataProvider->attributes()->id;
		$dataSourceId = (string) $xmlDataProvider->attributes()->dataSource;
		
		//VERIFICANDO VALIDADE E CAPTURANDO DATASOURCE PRINCIPAL
		if( !isset( $module->dataSources[$dataSourceId] ) ) {
			throw new \Sh\SheerException(array(
				'message'	=> 'DataSource ("'.$dataSourceId.'") referenciado pelo DataProvider ("'.$id.'") é inválido ou inexistente',
				'code'		=> 'SMC_XXXX'
			));
		}
		$dataSource = clone $module->dataSources[$dataSourceId];
		
		//CRIANDO DATAPROVIDER
		$dataProvider = new \Sh\DataProvider($id, $dataSource);
		
		//PROCESSANDO CONTENT PROVIDER
		$contentProvider = null;
		if( isset($xmlDataProvider->attributes()->contentProvider) && $xmlDataProvider->attributes()->contentProvider ) {
			$contentProvider = (string) $xmlDataProvider->attributes()->contentProvider;
			$dataProvider->setContentProvider($contentProvider);
		}
		
		//PROCESSANDO DATA PARSER
		$dataParser = null;
		if( isset($xmlDataProvider->attributes()->dataParser) && $xmlDataProvider->attributes()->dataParser ) {
			$dataParser = (string) $xmlDataProvider->attributes()->dataParser;
			$dataProvider->setDataParser($dataParser);
		}
		
		//PROCESSANDO MAX ROWS
		{
			$dataProvider->setMaxRows(25);
			if( isset($xmlDataProvider->maxRows) ) {
				$maxRows = (integer) $xmlDataProvider->maxRows;
				$dataProvider->setMaxRows($maxRows);
			}
		}
		
		//PROCESSANDO RELACIONAMENTOS
		{
			if( isset($xmlDataProvider->relations) && isset($xmlDataProvider->relations->relation) ) {
				foreach ( $xmlDataProvider->relations->relation as $xmlRelation ) {
					$relation = self::compileRelation($xmlRelation, $dataSource);
					$dataProvider->pushRelation($relation);
				}
			}
			
			//PROCESSANDO RELACIONAMENTOS VINDO DE ADDONS
			/*if( $dataSource->hasPublicationMetadata() ) {
				
				$dataSourceMetadata = \Sh\ModuleFactory::getModuleDataSource('publicationMetadata', 'publicationMetadata');
				
				$relation = array();
				$relation['id']				= '/publicationMetadata';
				$relation['alias']			= 'publicationMetadata';
				$relation['index']			= self::$relationIndex++;
				$relation['parentIndex']	= 0;
				$relation['idDataSource']	= 'publicationMetadata/publicationMetadata';
				$relation['dataSource']		= $dataSourceMetadata;
				$relation['leftKey']		= $dataSource->getPrimaryKey(false);
				$relation['rightKey']		= $dataSourceMetadata->getField('contentId', false);
				$relation['rightName']		= $dataSourceMetadata->getField('contentId', false);
				$relation['dataFormatter']	= 'fieldContent';
				$relation['relations']		= array();
				
				$dataProvider->pushRelation($relation);
				
				//MAPEANDO RELACIONAMENTO NO MAPA DE CAMINHO DE RELACIONAMENTOS
				self::$relationsPathsIndexMap[ $relation['id'] ] = $relation['index'];
			}*/
			
			//SETANDO RELATIONS INDEXPATH
			$dataProvider->setRelationsPathIndexMap(self::$relationsPathsIndexMap);
		}
		
		//PROCESSANDO FILTROS
		{
			//PROCESSANDO OS FILTROS APLICADOS
			if( isset($xmlDataProvider->filters) && isset($xmlDataProvider->filters->filter) ) {
				foreach( $xmlDataProvider->filters->filter as $xmlFilter ) {
					$filter = self::compileFilter($xmlFilter);
					$dataProvider->pushFilter($filter);
				}
			}
			
			//PROCESSANDO E RECUPERANDO O CUSTOMQUERY PARA OS FILTROS
			if( isset($xmlDataProvider->filters) && isset($xmlDataProvider->filters->customQuery) ) {
				$customQuery = (string) $xmlDataProvider->filters->customQuery;
				if( strlen($customQuery) ) {
					$dataProvider->setFiltersCustomQuery($customQuery);
				}
			}
		}
		
		//PROCESSANDO ORDENACAO
		{
			if( isset($xmlDataProvider->sort) && isset($xmlDataProvider->sort->by) ) {
				foreach( $xmlDataProvider->sort->by as $xmlSort) {
					$sort = array();
					$sort['field'] 			= (string) $xmlSort->attributes()->field;
					$sort['order'] 			= strtolower((string) $xmlSort->attributes()->order);
					$sort['relationPath'] 	= (string) $xmlSort->attributes()->relationPath;
					$sort['relationIndex'] 	= self::getRelationIndexFromRelationPath($sort['relationPath']);
					
					//DETERMINANDO MODELO DE ORDENACAO
					$sort['order'] = self::getSortOrderFromString($sort['order']);
					
					$dataProvider->pushSort($sort);
				}
			}
			//SE NÃO ENCONTRAMOS NENHUMA REGRA DE ORDENAÇÃO DEVEMOS CONSIDERAR O PRIMARYNAME DO DATASOURCE PRINCIPAL, SE NÃO TIVER PRIMARYNAME, USAMOS O PRIMARYKEY MESMO
			else {
				$primaryKey = $dataSource->getPrimaryKey();
				$primaryName = $dataSource->getPrimaryName();
				//buscando primaryName
				if( $primaryName ) {
					$sort = array();
					$sort['field'] 			= $primaryName->getId();
					$sort['order'] 			= 'asc';
					$sort['relationPath'] 	= '';
					$sort['relationIndex'] 	= 0;
					$dataProvider->pushSort($sort);
				}
				//se nao encontrou primaryName olhamos o primaryKey
				else if ( $primaryKey ) {
					$sort = array();
					$sort['field'] 			= $primaryKey->getId();
					$sort['order'] 			= 'asc';
					$sort['relationPath'] 	= '';
					$sort['relationIndex'] 	= 0;
					$dataProvider->pushSort($sort);
				}
			}
		}
		
		//PROCESSANDO GROUPBY
		{
			if ( isset($xmlDataProvider->group) && isset($xmlDataProvider->group->by) ) {
				foreach( $xmlDataProvider->group->by as $xmlGroup ) {
					$group = array();
					$group['field'] 		= (string) $xmlGroup->attributes()->field;
					$group['relationPath'] 	= (string) $xmlGroup->attributes()->relationPath;
					$group['relationIndex'] = self::getRelationIndexFromRelationPath($group['relationPath']);
					
					$dataProvider->pushGroup($group);
				}
			}
		}
		
		return $dataProvider;
	}
	
	/**
	 * Iremos processar um nó de relacionamento
	 * Esta função irá processar todos os seus atributos e saberá capturar todos os campos não necessários
	 * Ela também irá processar os relacionamentos filhos daquele relacionamento em questão, a tornando uma funcao recursiva
	 * Esta função também irá gerar o index dos relacionamentos e criar o mapeamento de RelationPath para RelationIndex
	 * 
	 * @param \SimpleXMLElement $xmlRelation
	 * @param \Sh\DataSource $dataSourceParent
	 * @throws \Sh\SheerException
	 * @return array()
	 * 		id
	 * 		alias			=> Identificador simples, sem hierarquia
	 * 		index			=> Inteiro representativo do relacionamento
	 * 		parentIndex		=> Inteiro representativo do index do Pai
	 * 		idDataSource
	 * 		dataSource
	 * 		leftKey
	 * 		rightKey
	 * 		rightName
	 * 		dataFormatter	=> string indicando modelo de formatacao de dados
	 * 		relations		=> array com relacionamentos que nem este
	 */
	static protected function compileRelation(\SimpleXMLElement $xmlRelation, \Sh\DataSource $dataSourceParent, $parentInfo = array('id'=>'', 'index'=>0)) {
		
		$relation = array();
		$relation['id']				= $parentInfo['id'] .'/'. (string) $xmlRelation->attributes()->id;
		$relation['alias']			= (string) $xmlRelation->attributes()->id;
		$relation['index']			= self::$relationIndex++;
		$relation['parentIndex']	= $parentInfo['index'];
		$relation['idDataSource']	= (string) $xmlRelation->attributes()->dataSource;
		$relation['dataSource']		= null;
		$relation['leftKey']		= null;
		$relation['rightKey']		= null;
		$relation['rightName']		= null;
		$relation['dataFormatter']	= 'fieldContent';
		$relation['relations']		= array();
		
		//VERIFICANDO IDENTIFICADOR DE DATASOURCE
		if( strpos( $relation['idDataSource'], '/' ) === false ) {
			throw new \Sh\SheerException(array(
				'message' => 'Indetificador de DataSource para o relacionamento "'.$relation['id'].'" é inválida.',
				'code' => 'SMC_XXXX'
			));
		}
		
		//CAPTURANDO DATASOURCE CORRETO PARA O RELACIONAMENTO
		list($idModule, $idDataSource) = explode('/', $relation['idDataSource']);
		$dataSource = \Sh\ModuleFactory::getModuleDataSource($idModule, $idDataSource);
		$relation['dataSource'] = $dataSource;
		
		//DETERMINANDO LEFT KEY
		{
			//existe leftkey setado
			if( isset($xmlRelation->attributes()->leftKey) ) {
				$fieldId = (string) $xmlRelation->attributes()->leftKey;
				//verificando a existencia do field no dataSource
				if( $dataSourceParent->isField($fieldId) ) {
					$relation['leftKey'] = $dataSourceParent->getField($fieldId);
				}
				else {
					\Sh\LoggerProvider::log('warning', 'Field referenciado para leftKey no relacionamento "'.$relation['id'].'" é inválido. Assumindo PrimaryKey.');
				}
			}
			//NÃO TENDO ENCONTRADO UM LEFT KEY VALIDO ASSUMIMOS O PRIMARY KEY
			if( !$relation['leftKey'] ) {
				$relation['leftKey'] = $dataSourceParent->getPrimaryKey();
			}
		}
		
		//DETERMINANDO CONTROLES DO DATASOURCE ATUAL
		//DETERMINANDO RIGHT KEY
		{
			//existe rightKey setado
			if( isset($xmlRelation->attributes()->rightKey) ) {
				$fieldId = (string) $xmlRelation->attributes()->rightKey;
				//verificando a existencia do field no dataSource
				if( $dataSource->isField($fieldId) ) {
					$relation['rightKey'] = $dataSource->getField($fieldId);
				}
				else {
					\Sh\LoggerProvider::log('warning', 'Field referenciado para rightKey no relacionamento "'.$relation['id'].'" é inválido. Assumindo PrimaryKey.');
				}
			}
			//NÃO TENDO ENCONTRADO UM LEFT KEY VALIDO ASSUMIMOS O PRIMARY KEY
			if( !$relation['rightKey'] ) {
				$relation['rightKey'] = $dataSource->getPrimaryKey();
			}
		}
		
		//DETERMINANDO RIGHT NAME
		{
			//existe rightName setado
			if( isset($xmlRelation->attributes()->rightName) ) {
				$fieldId = (string) $xmlRelation->attributes()->rightName;
				//verificando a existencia do field no dataSource
				if( $dataSource->isField($fieldId) ) {
					$relation['rightName'] = $dataSource->getField($fieldId);
				}
				else {
					\Sh\LoggerProvider::log('warning', 'Field referenciado para rightName no relacionamento "'.$relation['id'].'" é inválido. Assumindo PrimaryName.');
				}
			}
			//NÃO TENDO ENCONTRADO UM LEFT KEY VALIDO ASSUMIMOS O PRIMARY KEY
			if( !$relation['rightName'] ) {
				$relation['rightName'] = $dataSource->getPrimaryName();
			}
		}
		
		//DATA FORMATTER
		$relation['dataFormatter']		= self::getDataFormatterFromString( (string) $xmlRelation->attributes()->dataFormatter);
		
		//MAPEANDO RELACIONAMENTO NO MAPA DE CAMINHO DE RELACIONAMENTOS 
		self::$relationsPathsIndexMap[ $relation['id'] ] = $relation['index'];
		
		//PROCESSANDO SUBRELACIONAMENTOS
		if( isset($xmlRelation->relations) && isset($xmlRelation->relations->relation) ) {
			foreach( $xmlRelation->relations->relation as $xmlSubRelation ) {
				$subRelation = self::compileRelation( $xmlSubRelation, $relation['dataSource'], array('id'=>$relation['id'], 'index'=>$relation['index']) );
				$relation['relations'][$subRelation['id']] = $subRelation;
			}
		}
		
		return $relation;
	}
	
	/**
	 * Método responsável por compilar um filtro de um DataProvider
	 * @param \SimpleXMLElement $xmlFilter
	 * @return array
	 * 		id
	 * 		relationPath		=> Path para o relacionamento
	 * 		relationIndex		=> Index para o relacionamento
	 * 		field
	 * 		operator
	 * 		dateFunction
	 * 		defaultValue
	 * 		parameter
	 * 		required
	 * 		useNullIfBlank
	 */
	static protected function compileFilter(\SimpleXMLElement $xmlFilter) {
		
		$filter = array();
		
		//CAMPOS OBRIGATÓRIOS
		$filter['id'] 				= (string) $xmlFilter->attributes()->id;
		$filter['relationPath']		= (string) $xmlFilter->attributes()->relationPath;
		$filter['relationIndex']	= self::getRelationIndexFromRelationPath($filter['relationPath']);
		$filter['field'] 			= (string) $xmlFilter->attributes()->field;
		
		//verificando que o relationPath comece com / para ser root
		if( !$filter['relationPath'] || $filter['relationPath'][0] != '/' ) {
			$filter['relationPath'] = '/'.$filter['relationPath'];
		}
		
		//OPERATOR
		$filter['operator']			= self::getOperatorFromString( (string) $xmlFilter->attributes()->operator );
		
		//DATE FUNCTION
		$filter['dateFunction']		= null;
		if( isset($xmlFilter->attributes()->dateFunction) ) {
			$filter['dateFunction']		= self::getDateFunctionFromString ( (string) $xmlFilter->attributes()->dateFunction );
		}
		
		//DEFAULT VALUE
		$filter['defaultValue']		= null;
		if( isset($xmlFilter->attributes()->defaultValue) && strlen((string) $xmlFilter->attributes()->defaultValue) ) {
			$filter['defaultValue'] = (string) $xmlFilter->attributes()->defaultValue;
		}
		
		//PARAMETER
		$filter['parameter'] = $filter['id'];
		if( isset($xmlFilter->attributes()->parameter) && strlen((string) $xmlFilter->attributes()->parameter) ) {
			$filter['parameter'] = (string) $xmlFilter->attributes()->parameter;
		}
		
		//REQUIRED
		$filter['required'] = false;
		if( isset($xmlFilter->attributes()->required) ) {
			$filter['required'] = \Sh\Library::getBooleanFromXmlNode($xmlFilter->attributes()->required);
		}
		
		//USE NULL IF BLANK
		$filter['useNullIfBlank'] = false;
		if( isset($xmlFilter->attributes()->useNullIfBlank) ) {
			$filter['useNullIfBlank'] = \Sh\Library::getBooleanFromXmlNode($xmlFilter->attributes()->useNullIfBlank);
		}
		return $filter;
	}
	
	/**
	 * Método para obter atraves da string de relationPath o relationIndex relacionado
	 * @param string $relationPath
	 * @return integer
	 * 
	 * Efetua a mesma operação que o método "dataProvider::getRelationIndexFromRelationPath".
	 */
	static protected function getRelationIndexFromRelationPath ($relationPath) {
		
		//Verificando e validando o relationPath
		if( !$relationPath || $relationPath[0] != '/' ) {
			$relationPath = '/'.$relationPath;
		}
		
		//BUSCANDO INDEX DO RELACIONAMENTO
		$index = null;
		if( isset(self::$relationsPathsIndexMap[$relationPath]) ) {
			$index = (integer) self::$relationsPathsIndexMap[$relationPath];
		}
		if( $relationPath == '/' || !$index ) { $index = 0; }
		
		return $index;
	}
	
	/**
	 * Definimos o dataFormatter a partir do dataFormatter passado
	 * @param string $dataFormatter
	 * @return string
	 */
	static protected function getDataFormatterFromString ( $dataFormatter ) {
		
		switch( $dataFormatter ) {
			case 'inlineContentPrefix':
			case 'fieldContent':
			case 'fieldContentMultiple':
			case 'relatedContent':
			case 'relatedContentMultiple':
				$dataFormatter = $dataFormatter;
				break;
			default:
				$dataFormatter = 'fieldContent';
				break;
		}
		return $dataFormatter;
	}
	
	/**
	 * Definimos o operador final a partir o operator passado
	 * @param string $operator
	 * @return string
	 */
	static protected function getOperatorFromString ( $operator ) {
		
		switch( $operator ) {
			case 'equal':
			case 'like':
			case 'likeSplit':
// 			case 'likeSufix':
// 			case 'likePrefix':
			case 'greater':
			case 'greaterOrEqual':
			case 'less':
			case 'lessOrEqual':
			case 'different':
			case 'in':
			case 'notIn':
			case 'isNull':
			case 'isNotNull':
			case 'periodFuture':
			case 'periodPast':
			case 'dataFimValido':
				$operator = $operator;
				break;
			default:
				$operator = 'equal';
				break;
		}
		return $operator;
	}
	
	/**
	 * Definimos se devemos aplicar alguma função de data para o filtro
	 * @param string $strDateFunction
	 * @return string
	 */
	static protected function getDateFunctionFromString ( $strDateFunction ) {
		
		$dateFunction = null;
	
		switch( strtolower($strDateFunction) ) {
			case 'date':
			case 'year':
			case 'month':
			case 'daymonth':
			case 'monthyear':
			case 'day':
			case 'hour':
			case 'minute':
			case 'second':
				$dateFunction = $strDateFunction;
				break;
		}
		return $dateFunction;
	}
	
	/**
	 * Método que recebe uma string e retorna a ordenação correta aplicada
	 * @param string $string
	 * @return string
	 */
	static public function getSortOrderFromString ($string) {
		$string = strtolower($string);
		//DETERMINANDO MODELO DE ORDENACAO
		switch ( $string ) {
			case 'asc': case 'desc': case 'random': break;
			default: $sort['order'] = 'asc';
		}
		
		return $string;
	}
	
}