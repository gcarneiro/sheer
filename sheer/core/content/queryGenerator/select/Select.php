<?php

namespace Sh\QueryGenerator;

class Select {
	
	/*
	 * Querys padrões para recuperação de dados
	 */
	const QUERY_PATTERN = 'SELECT {fieldlist} FROM {table} {tableJoin} {where} {group} {sort} {limit} {offset}';
	
	/**
	 * @var \Sh\DataProvider
	 */
	protected $dataProvider;
	
	protected $filters 			= array();
	protected $configs				= array();
	
	
	/*
	 * Elementos da query
	*/
	protected $fieldlist 	= '';
	protected $table 		= '';
	protected $tableJoin 	= '';
	protected $sort 		= '';
	protected $groupBy 	= '';
	protected $limit 		= '';
	protected $offset 		= '';
	protected $where 		= '';
	
	
	public function __construct(\Sh\DataProvider $dataProvider, $filters=array(), $configs=array()) {
		
		$this->dataProvider = $dataProvider;
		if( !is_array($filters) ) 		{ $filters = array(); }
		if( !is_array($configs) ) 		{ $configs = array(); }
		
	}
	
	/**
	 * @date 2015.01.22
	 * @author Guilherme
	 * 		Atualizado este método para colocar no retorno a página atual e o total de páginas
	 * 
	 * @param array $filters
	 * @param array $configs ['page'=>1, 'maxRows'=>null, 'sort'=>null, 'conn'=>null, 'requiredFilters']
	 * @throws \Sh\SheerException
	 * 
	 * @return array(
	 * 		'available' => integer Determina quantos resultados são possíveis de serem recuperados com esses dados de busca
	 * 		'total' => integer Determina o total de registos que serão retornados nesta busca
	 * 		'results' => array Traz todos os resultados recuperados,
	 * 		'navigation' => array Traz informações sobre as páginas.
	 * 			Possue 'current' e 'last'
	 * )
	 */
	public function loadData ($filters=array(), $configs=array('page'=>1, 'maxRows'=>null, 'sort'=>null, 'conn'=>null)) {
		
		//CRIANDO MODELO DE RESPOSTA
		$return = array(
			'available' 	=> 0,
			'total' 		=> 0,
			'results' 		=> array(),
			'navigation'	=> array(
				'current'		=> 1,
				'last'			=> 50
			)
		);
		
		//DETERMINANDO EXISTENCIA DE FILTROS
		if( !$filters || !is_array($filters) ) {
			$filters = array();
		}
		
		//CONFIGURATION - PAGE
		if( !isset($configs['page']) || !is_numeric($configs['page']) ) {
			$configs['page'] = 1;
		}
		
		//CONFIGURATION - MAXROWS
		if( !isset( $configs['maxRows'] ) || $configs['maxRows']===null ) {
			$configs['maxRows'] = $this->dataProvider->getMaxRows();
		}
		
		//CONFIGURATION - SORT
		$runtimeSortable = array();
		if( isset($configs) && isset($configs['sort']) ) {
			foreach ( $configs['sort'] as $sortBy ) {
				$sort = array();
				
				//verificando campo
				if( !isset($sortBy['field']) ) {
					\Sh\LoggerProvider::log('warning', 'Field não configurado para sort customizado em "'.$this->dataProvider->getModuleId().'/'.$this->dataProvider->getId().'"');
					continue;
				}
				
				$sort['field'] 			= $sortBy['field'];
				$sort['order'] 			= \Sh\DataProviderCompiler::getSortOrderFromString( (isset($sortBy['order'])) ? $sortBy['order'] : null);
				$sort['relationPath'] 	= (isset($sortBy['relationPath'])) ? (string) $sortBy['relationPath'] : null;
				$sort['relationIndex'] 	= $this->dataProvider->getRelationIndexFromRelationPath($sort['relationPath']);
					
				//DETERMINANDO MODELO DE ORDENACAO
				$sort['order'] = \Sh\DataProviderCompiler::getSortOrderFromString($sort['order']);
				
				$runtimeSortable[] = $sort;
			}
		}
		
		//CONFIGURATION CONNECTION
		//verifico se a posição conn não é um PDO, se não for seto null
		if( !isset($configs['conn']) || !is_a($configs['conn'], '\PDO')  ) {
			$configs['conn'] = null;
		}
		
		//CRIANDO CONTROLADORES PRINCIPAIS
		$registrosPossiveis = 0;
		$registrosRecuperados = 0;
		$registros = array();
		
		//CRIANDO CONTROLADORES EXTRAS
		//gerando alias de conteudo primário
		$primaryKeyContentAlias = 't0.'.$this->dataProvider->getDataSource()->getPrimaryKey()->getId();
		//mapeando controlador de relacionamento NxN que determina se devemos buscar conteudos primários
		$contentMultipleData = false;
		
		//GERANDO ELEMENTOS DA QUERY
		$this->createFieldListQuery();
		$this->createTableQuery();
		$this->createTableJoinsQuery();
		$this->createGroupQuery();
		$this->createSortQuery($runtimeSortable);
		$this->createFilterQuery($filters, $configs['requiredFilters']);
		$this->createLimitQuery($configs['maxRows']);
		$this->createOffsetQuery($configs['page'], $configs['maxRows']);
		
		//REALIZO A CONTAGEM DE TOTAL DE REGISTROS ENCONTRADOS
		{
			/*
			 * PARA CONTAR O TOTAL DE REGISTROS POSSÍVEIS EU DEVO
			 * 		SETAR O FIELD LIST APENAS PARA PEGAR O VALOR DO PRIMARY KEY PRINCIPAL CONTANDO
			 * 		AGRUPO OS CONTEUDOS PELO PRIMARY KEY PRINCIPAL
			 * 		NÃO LIMITO E NÃO USO OFFSET PARA BUSCAR TODoS OS RESULTADOS
			 * 
			 * COM ISSO TENHO UMA QUERY QUE CONTE TODoS OS CONTEUDOS POSSÍVEIS
			 */
			
			//gerando novo FieldList
			$fieldlist = 'count('.$primaryKeyContentAlias.') ';
			
			//gerando novo group
			$groupBy = $this->groupBy;
			if( strlen($this->groupBy) ) {
				$groupBy .= ','.$primaryKeyContentAlias;
			}
			else {
				$groupBy = 'GROUP BY '.$primaryKeyContentAlias;
			}
			
			$limitQuery 	= '';
			$offsetQuery 	= '';
			
			//fabricando query
			$replacement = array('{fieldlist}', '{table}', '{tableJoin}', '{where}', '{sort}', '{limit}', '{offset}', '{group}');
			$value = array($fieldlist, $this->table, $this->tableJoin, $this->where, $this->sort, $limitQuery, $offsetQuery, $groupBy);
			$tmpQuery = str_replace($replacement, $value, self::QUERY_PATTERN);
			$queryContagemResultadosPossiveis = 'SELECT count(*) as total FROM ('.$tmpQuery.') t LIMIT 1000';
			
			//logando query
			\Sh\Library::logQuery($queryContagemResultadosPossiveis);
			
			$response = \Sh\DatabaseManager::runQuery($queryContagemResultadosPossiveis, $configs['conn']);
			if( $response === false ) {
				//TODO PRECISO CRIAR O LOGGER DE QUERY EM DESENVOLVIMENTO
				var_dump($queryContagemResultadosPossiveis);
				
				throw new \Sh\DatabaseException(array(
					'message'	=> 'Erro ao executar query de contagem de conteúdos disponíveis.',
					'code'		=> 'SCP_XXXX'
				));
			}
			$response = reset($response);
			$registrosPossiveis = (integer) $response['total'];
		}
		
		//CASO NÃO TENHA ENCONTRADO NENHUM RESULTADO POSSÍVEL NEM PRECISAMOS SEGUIR EM FRENTE
		if( $registrosPossiveis === 0 ) {
			return $return;
		}
		
		//RECUPERO OS IDS DOS CONTEUDOS PRIMÁRIOS
		//SÓ DEVO EXECUTAR O PROCESSO ABAIXO EM RELACIONAMENTOS QUE CONTENHAM 1=>N OU N=>N, QUE FAZEM UM REGISTRO SE TORNAREM VÁRIOS E ACABAM COM O MAXROWS
		//FIXME hoje este processo esta sendo realizado para todos
		if( $this->tableJoin && $this->limit ) {
			$contentMultipleData = true;
		}
		
		
		{
			//RECUPERO OS IDS DOS CONTEUDOS PRIMÁRIOS
			if ($contentMultipleData) {
				/*
				* PARA DETERMINAR OS IDS DOS CONTEUDOS PRIMARIOS EU DEVO
				* 		SETAR O FIELD LIST APENAS PARA PEGAR O VALOR DO PRIMARY KEY PRINCIPAL
				* 		AGRUPO OS CONTEUDOS PELO PRIMARY KEY PRINCIPAL
				* 		LIMITO E GERO O PISO DE BUSCA (OFFSET)
				*
				* COM ISSO TENHO UMA QUERY QUE IRÁ ME TRAZER OS CONTEUDOS PRINCIPAIS QUE DEVEM SER CARREGADOS
				* IREI UTILIZAR ESSE QUERY COMO UM FILTRO NA QUERY PRINCIPAL
				*/
				//gerando novo FieldList
				$fieldlist = $primaryKeyContentAlias.' as contentId';
				
				//gerando novo group
				$groupBy = $this->groupBy;
				if( strlen($this->groupBy) ) {
					$groupBy .= ','.$primaryKeyContentAlias;
				}
				else {
					$groupBy = 'GROUP BY '.$primaryKeyContentAlias;
				}
				
				//fabricando query
				$replacement = array('{fieldlist}', '{table}', '{tableJoin}', '{where}', '{sort}', '{limit}', '{offset}', '{group}');
				$value = array($fieldlist, $this->table, $this->tableJoin, $this->where, $this->sort, $this->limit, $this->offset, $groupBy);
				$queryFiltrarConteudosPrincipais = str_replace($replacement, $value, self::QUERY_PATTERN);
				
				//logando query
				\Sh\Library::logQuery($queryFiltrarConteudosPrincipais);
				
				//executando query para buscar os conteudos principais
				//caso a resposta da query venha vazia é devido a não termos encontrados resultados
				//em caso de retorno falso temos um erro de query
				$response = \Sh\DatabaseManager::runQuery($queryFiltrarConteudosPrincipais, $configs['conn']);
				if( $response === false ) {
					//TODO PRECISO CRIAR O LOGGER DE QUERY EM DESENVOLVIMENTO
					var_dump($queryFiltrarConteudosPrincipais);
					throw new \Sh\DatabaseException(array(
						'message'	=> 'Erro ao executar query de contagem de conteúdos principais para ContentProvider.',
						'code'		=> 'SCP_XXXX'
					));
				}
				
				//determino os identificadores de conteudos primários
				$tmpTotal = 0;
				$contentIds = '';
				foreach ($response as $row) {
					if( !$contentIds ) {
						$contentIds .= '"'.$row['contentId'].'"';
					}
					else {
						$contentIds .= ',"'.$row['contentId'].'"';
					}
					++$tmpTotal;
				}
				$registrosRecuperados = $tmpTotal;
				
				//CASO NÃO TENHA ENCONTRADO NENHUM RESULTADO POSSÍVEL NEM PRECISAMOS SEGUIR EM FRENTE
				if( $registrosRecuperados === 0 ) {
					return $return;
				}
				
				//RECUPERANDO CONTEUDOS FINAIS
				{
					//DEVO FILTRAR PELOS IDS DOS CONTEUDOS OBTIDOS NA QUERY ACIMA
					//DEVO REMOVER O LIMIT E OFFSET DESTA BUSCA
					{
						$filterQuery 	= $this->where;
						$limitQuery 	= '';
						$offsetQuery 	= '';
							
						//verifico se devo considerar o filtro pelos conteudos principais
						//só considero o contentsIds por precaução, acredito ser desnecessário
						if( $contentMultipleData && $contentIds ) {
							//mapeando filtro para aceitar os ids primarios
							if( $filterQuery ) {
								$filterQuery .= ' AND '.$primaryKeyContentAlias.' IN ('.$contentIds.')';
							}
							else {
								$filterQuery = 'WHERE '.$primaryKeyContentAlias.' IN ('.$contentIds.')';
							}
						}
							
						$replacement = array('{fieldlist}', '{table}', '{tableJoin}', '{where}', '{sort}', '{limit}', '{offset}', '{group}');
						$value = array($this->fieldlist, $this->table, $this->tableJoin, $filterQuery, $this->sort, $limitQuery, $offsetQuery, $this->groupBy);
						$query = str_replace($replacement, $value, self::QUERY_PATTERN);
						
						//logando query
						\Sh\Library::logQuery($query);
						
						//Executo a query para capturar os conteudos verdadeiros
						//caso a resposta da query venha vazia é devido a não termos encontrados resultados
						//em caso de retorno falso temos um erro de query
						$data = \Sh\DatabaseManager::runQuery($query, $configs['conn']);
						if( $data === false ) {
							//TODO PRECISO CRIAR O LOGGER DE QUERY EM DESENVOLVIMENTO
							var_dump($query);
							throw new \Sh\DatabaseException(array(
									'message'	=> 'Erro ao executar query de busca de conteúdos principais para ContentProvider.',
									'code'		=> 'SCP_XXXX'
							));
						}
					}
				}
			}
			//A BUSCA NÃO CONTÉM JOINS OU LIGAÇÕES
			else {
				//fabricando query final
				$replacement = array('{fieldlist}', '{table}', '{tableJoin}', '{where}', '{sort}', '{limit}', '{offset}', '{group}');
				$value = array($this->fieldlist, $this->table, $this->tableJoin, $this->where, $this->sort, $this->limit, $this->offset, $this->groupBy);
				$queryFinal = str_replace($replacement, $value, self::QUERY_PATTERN);
				
				//logando query
				\Sh\Library::logQuery($queryFinal);
				
				//caso a resposta da query venha vazia é devido a não termos encontrados resultados
				//em caso de retorno falso temos um erro de query
				$data = \Sh\DatabaseManager::runQuery($queryFinal, $configs['conn']);
				if( $data === false ) {
					//TODO PRECISO CRIAR O LOGGER DE QUERY EM DESENVOLVIMENTO
					var_dump($queryFinal);
					throw new \Sh\DatabaseException(array(
							'message'	=> 'Erro ao executar query de busca de conteúdos principais para ContentProvider.',
							'code'		=> 'SCP_XXXX'
					));
				}
				$registrosRecuperados = count($data);
			}
		}
		
		//Definindo última página
		$lastPage = 1;
		if( $configs['maxRows'] ) {
			$lastPage = $registrosPossiveis / $configs['maxRows'];
			$lastPage = ceil($lastPage);
		}
		
		
		//criando resultado final
		$return['available']	= $registrosPossiveis;
		$return['total']		= $registrosRecuperados;
		$return['results']		= $data;
		$return['navigation']['current'] 	= $configs['page'];
		$return['navigation']['last'] 		= $lastPage;
		
		return $return;
	} 
	
	/**
	 * Método para gerar o fieldList considerando todos os fields de todos os relacionamentos
	 * 
	 * //TODO Passar a considerar apenas os fields desejados dentro daquele relacionamento
	 */
	protected function createFieldListQuery () {
		
		//iniciando variaveis controladoras
		$prefix 		= 't0';
		$queryFieldList	= '';
		
		//INICIALMENTE IREI GERAR OS CAMPOS DO DATASOURCE PRINCIPAL
		$dataSource = $this->dataProvider->getDataSource();
		$fields = $dataSource->getFields();
		foreach( $fields as $idField => $field ) {
			//inserindo delimitador
			if( $queryFieldList ) { $queryFieldList .= ','; }
			$queryFieldList .= $prefix.'.'.$idField;
		}
		
		//AGORA TRATO OS FIELDS DOS RELACIONAMENTOS
		$relations = $this->dataProvider->getRelationsQueue();
		if( $relations ) {
			//PARA CARA RELACIOMAENTO GERO A SUA LISTA DE FIELDS
			foreach( $relations as $idRelation=>$relation ) {
				//criando prefixo com dependencia do index do relacionamento
				$prefix = 't'.$relation['index'];
				$fields = $relation['dataSource']->getFields();
				foreach( $fields as $idField => $field ) {
					//inserindo separador
					if( $queryFieldList ) { $queryFieldList .= ','; }
					$queryFieldList .= $prefix.'.'.$idField.' AS '.$prefix.'_'.$idField;
				}
				
			}
		}
		
		$this->fieldlist = $queryFieldList;
		
	}
	
	/**
	 * Método para gerar o campo "table" da query
	 */
	protected function createTableQuery () {
		$this->table = $this->dataProvider->getDataSource()->getTable().' as t0';
	}
	
	
	
	/**
	 * Método para gerar as querys de JOIN considerando as tabelas relacionadas
	 * 
	 * TODO PRECISO CONSIDERAR SE DEVE SER LEFT JOIN OU APENAS JOIN
	 */
	protected function createTableJoinsQuery () {
		
		//iniciando variaveis controladoras
		$queryTableJoin		= '';
		
		//Carregando relacionamentos
		$relations = $this->dataProvider->getRelationsQueue();
		
		//enquanto existir relacionamentos mapeados
		if($relations) {
			foreach( $relations as $idRelation=>$relation ) {
				
				//inserindo separador
				if( $queryTableJoin ) { $queryTableJoin .= ' '; }
				
				$prefix = 't'.$relation['index'];
				$parentPrefix = 't'.$relation['parentIndex'];
				$queryTableJoin .= 'LEFT JOIN '.$relation['dataSource']->getTable().' as '.$prefix.' ON '.$parentPrefix.'.'.$relation['leftKey']->getId().' = '.$prefix.'.'.$relation['rightKey']->getId();
				
			}
		}
		
		$this->tableJoin = $queryTableJoin;
		
	}
	
	/**
	 * Método para gerar a query de filtros (WHERE) do sql 
	 * 
	 * @param array $filterData
	 * @param array $requiredFilters 
	 * 		Este permite customizar quais os filtros serão obrigatórios para a chamada corrente
	 * 		Ese foi adicionado para passarmos a aceitar o required na expansão do dataProvider do Renderable
	 */
	protected function createFilterQuery ($filterData, $requiredFilters=null) {
		$queryFilter = '';
		$queryFilterMap = array();
		$queryTemplate = '';
		
		$relationsQueue = $this->dataProvider->getRelationsQueue();
		
		$filterConfig = $this->dataProvider->getFilters();
		if( $filterConfig ) {
			
			//ITERO ENTRE OS FILTROS GERANDO O MAPA DE COMPARACOES E O QUERYPATTERN
			foreach ($filterConfig as $idFilter=>$filter) {
				
				//injeto a comparação no queryTemplate
				if( strlen($queryTemplate) == 0 ) {
					$queryTemplate .= '{'.$idFilter.'}';
				}
				else {
					$queryTemplate .= ' AND {'.$idFilter.'}';
				}
				
				//VAMOS BUSCAR O OBJETO FIELD A SER FILTRADO
				$filterDataSource = null;
				$filterField = null;
				{
					//capturando dataSource
					$relationPath = $filter['relationPath'];
					if( $relationPath == '/' ) {
						$filterDataSource = $this->dataProvider->getDataSource();
					}
					else {
						$tmpRelation = $relationsQueue[$relationPath];
						$filterDataSource = $tmpRelation['dataSource'];
					}
					
					//capturando field correto
					$filterField = $filterDataSource->getField( $filter['field'] );
				}
				
				//Vamos customizar o valor do required de acordo com o requiredFilters enviado runtime
				if( is_array($requiredFilters) && isset($requiredFilters[$idFilter])) {
					$filter['required'] = $requiredFilters[$idFilter];
				}

				//Busco a query de comparação e insiro no mapa de comparações
				$filterProcessor 			= new \Sh\QueryGenerator\FilterProcessor($filter, $filterField, $filterData);
				$queryFilterMap[$idFilter] 	= $filterProcessor->processFilter();
			}
			
			//BUSCO O CUSTOMQUERY E DEFINO O NOVO PATTERN
			$queryCustomTemplate = $this->dataProvider->getFiltersCustomQuery();
			if( $queryCustomTemplate && strlen($queryCustomTemplate) ) {
				$queryTemplate = $queryCustomTemplate;
			}
			
			//TRADUZO O QUERY PATTERN PARA A QUERY FINAL
			if( $queryFilterMap ) {
				$queryFilter = $queryTemplate;
				
				foreach ( $queryFilterMap as $idFilter=>$filterQuery ) {
					$alias = '{'.$idFilter.'}';
					$queryFilter = str_replace($alias, $filterQuery, $queryFilter);
				}
			}
			
			//INSERINDO MARCADOR DE WHERE
			if( strlen($queryFilter) ) {
				$queryFilter = 'WHERE '.$queryFilter;
			}
			
			$this->where = $queryFilter;
		}
	}
	
	/**
	 * Método para gerar as querys de SORT BY considerando as tabelas relacionadas
	 */
	protected function createSortQuery ($runtimeSortable) {
		
		//INICIANDO VARIAVEIS CONTROLADORAS
		$querySort		= '';
		
		//VERIFICANDO RUNTIME SORTABLE
		if( $runtimeSortable ) {
			foreach ( $runtimeSortable as $sortby ) {
				//inserindo separador
				if( $querySort ) { $querySort .= ','; }
				else { $querySort .= 'ORDER BY '; }
				
				$prefix = 't'.$sortby['relationIndex'];
				//criando o sort
				if( $sortby['order'] == 'random' ) {
					$querySort .= 'RAND()';
				}
				else {
					$querySort .= $prefix.'.'.$sortby['field'].' '.strtoupper($sortby['order']);
				}
			}
		}
		//VERIFICANDO SORTABLE DE DATAPROVIDER
		else {
			
			$sortable = $this->dataProvider->getSortable();
			//tendo sort proprio
			if($sortable) {
				foreach ( $sortable as $sortby ) {
			
					//inserindo separador
					if( $querySort ) { $querySort .= ','; }
					else { $querySort .= 'ORDER BY '; }
			
					$prefix = 't'.$sortby['relationIndex'];
					//criando o sort
					if( $sortby['order'] == 'random' ) {
						$querySort .= 'RAND()';
					}
					else {
						$querySort .= $prefix.'.'.$sortby['field'].' '.strtoupper($sortby['order']);
					}
			
				}
			}
			//nao tendo assumo o primaryName
			else {
				//verificando primaryName
				$primaryName = $this->dataProvider->getDataSource()->getPrimaryName(false);
				if( $primaryName ) {
					$querySort .= 'ORDER BY t0.'.$primaryName->getId().' ASC';
				}
			}
			
		}
		
		$this->sort = $querySort;
	}
	
	/**
	 * Método para gerar as querys de GROUP BY considerando as tabelas relacionadas
	 */
	protected function createGroupQuery () {
		
		//iniciando variaveis controladoras
		$queryGroup		= '';
		
		$groupby = $this->dataProvider->getGroup();
		
		if($groupby) {
			foreach ( $groupby as $group ) {
		
				//inserindo separador
				if( $queryGroup ) { $queryGroup .= ','; }
				else { $queryGroup .= 'GROUP BY '; }
		
				$prefix = 't'.$group['relationIndex'];
				
				$queryGroup .= $prefix.'.'.$group['field'];
			}
		}
		
		$this->groupBy = $queryGroup;
		
	}
	
	/**
	 * Método para determinar o número de registros primários a serem recuperados
	 */
	protected function createLimitQuery( $maxRows=null ) {
		$queryLimit = '';
		
		//determinando maxRows
		if( is_numeric($maxRows) ) {
			$maxRows = (int) $maxRows;
		}
		
		//gerando a query
		if( $maxRows !== 0 ) {
			$queryLimit = 'LIMIT '.$maxRows;
		}
		$this->limit = $queryLimit;
	}
	
	/**
	 * Método para determinar a partir de qual registro devemos trazer os novos registros
	 */
	protected function createOffsetQuery ($page, $maxRows) {
		$queryOffset = '';
		
		//determinando maxRows
		if( is_numeric($maxRows) ) {
			$maxRows = (int) $maxRows;
		}
		
		//gerando a query
		if( $maxRows !== 0 ) {
			$queryOffset = 'OFFSET '.($maxRows * ($page-1));
		}
		$this->offset = $queryOffset;
	}
	
}