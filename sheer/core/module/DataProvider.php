<?php

namespace Sh;

/**
 * @author guilherme
 * Classe responsável pelos DataProviders do Sheer
 * 
 * 
 * 
 * 
 * TODO FAZER UM MAPEAMENTO DIRETO PARA O RELATIONPATH PARA O INDEX DE ACESSO DE CADA RELACIONAMENTO
 *
 */
class DataProvider {
	
	protected $module;
	protected $id;
	
	protected $dataParser;
	protected $contentProvider;
	
	/**
	 * @var \Sh\DataSource
	 */
	protected $dataSource;
	
	/**
	 * Controlador de relacionamentos
	 */
	protected $relations = array();
	
	/**
	 * Controlador dos index dos relacionamentos indexados pelos ids dos relacionamentos
	 */
	protected $relationsPathIndexMap = array();
	
	/**
	 * Controlador dos filtros
	 * @var string
	 */
	protected $filters = array();
	
	/**
	 * Query customizada do filtro
	 * @var string
	 */
	protected $filtersCustomQuery = null;
	
	/**
	 * Controlador de objetos para ordenação
	 */
	protected $sortable = array();
	
	protected $group = array();
	
	/**
	 * Campo responsável por determinar quantos registrar trazer do banco
	 */
	protected $maxRows = 25;
	
	
	public function __construct($id, \Sh\DataSource $dataSource) {
		
		$this->module = $dataSource->getModuleId();
		$this->id = $id;
		$this->dataSource = $dataSource;
		
	}
	
	/**
	 * Método para setar o contentProvider customizado do dataProvider
	 * 
	 * @param string $idContentProvider
	 */
	public function setContentProvider ( $idContentProvider ) {
		
		$className = '\\Sh\\Modules\\'.$this->module.'\\'.$idContentProvider;
		
		//verificando existencia de classe
		if( !class_exists($className) ) {
			\Sh\LoggerProvider::log('warning', 'ContentProvider "'.$className.'" é inválido.');
			return;
		}
		
		//verificando dependencia
		if( !is_subclass_of($className, '\\Sh\\GenericContentProvider') ) {
			\Sh\LoggerProvider::log('warning', 'ContentProvider "'.$className.'" não preenche dependencia de \Sh\GenericContentProvider.');
			return;
		}
		
		$this->contentProvider = $className;
	}
	
	/**
	 * Método para setar o dataParser do dataProvider
	 * 
	 * @param string $idDataParser
	 */
	public function setDataParser ( $idDataParser ) {
		
		$className = '\\Sh\\Modules\\'.$this->module.'\\'.$idDataParser;
		
		//verificando existencia de classe
		if( !class_exists($className) ) {
			\Sh\LoggerProvider::log('warning', 'DataParser "'.$className.'" é inválido.');
			return;
		}
		
		//verificando dependencia
		if( !is_subclass_of($className, '\\Sh\\GenericDataParser') ) {
			\Sh\LoggerProvider::log('warning', 'DataParser "'.$className.'" não preenche dependencia de \Sh\GenericDataParser.');
			return;
		}
		
		$this->dataParser = $className;
		
	}
	
	/**
	 * Determina se o DataProvider possui um dataParser
	 * @return boolean
	 */
	public function hasDataParser () {
		
		return !!$this->dataParser;
		
	}
	
	/**
	 * Método para recuperar o dataParser do dataProvider
	 * 
	 * @return \Sh\GenericDataParser
	 */
	public function getDataParser () {
		
		if( !$this->hasDataParser() ) { return null; }
		
		return new $this->dataParser();
		
	}
	
	/**
	 * Método que irá retornar o nome da classe do ContentProvider a aplicar
	 * 
	 * @return string
	 */
	public function getContentProvider () {
		
		if( !$this->contentProvider ) {
			return '\\Sh\\ContentProvider';
		}
		return $this->contentProvider;
		
	}
	
	/**
	 * Recupera o array que mapeia os filtros aceitos pelo dataProvider
	 * @return array
	 * 		id
	 * 		relationPath
	 * 		relationIndex
	 * 		field
	 * 		operator
	 * 		dateFunction
	 * 		defaultValue
	 * 		parameter
	 * 		required
	 */
	public function getFilters () {
		return $this->filters;
	}
	
	/**
	 * Recupera o customQuery dos filtros
	 * @return string
	 */
	public function getFiltersCustomQuery() {
		return $this->filtersCustomQuery;
	}
	
	/**
	 * Recupera o array que mapeia os SortBy impostos ao dataProvider
	 * @return array
	 * 		field 			=> identificador do field a ser ordenado
	 * 		relationPath	=> Path para a relação de onde o field pertence
	 * 		relationIndex	=> Index para a relação de onde o field pertence
	 * 		order			=> Ordem para a ordenação. ASC | DESC | CUSTOM
	 */
	public function getSortable() {
		return $this->sortable;
	}
	
	/**
	 * Recupera o array que mapeia os Groups By impostos ao dataProvider
	 * @return array
	 * 		field 			=> identificador do field a ser agrupado
	 * 		relationPath	=> Path para a relação de onde o field pertence
	 * 		relationIndex	=> Index para a relação de onde o field pertence
	 */
	public function getGroup() {
		return $this->group;
	}
	
	/**
	 * Recupera o array que mapeia todos os relacionamentos do DataProvider e seus DataSources
	 * O mapeamento é devolvido em formato de árvore
	 * @return array
	 * 		id 				=> Identificador do relacionamento
	 * 		alias			=> Identificador simples do relacionamento, sem hierarquia
	 * 		index			=> Index identificador do relacionamento
	 * 		parentIndex		=> Index identificador do relacionamento pai
	 * 		idDataSource	=> Identiricador do DataSource
	 * 		dataSource		=> [\Sh\DataSource] Objeto do data source referenciado 
	 * 		leftKey			=> [\Sh\DataSourceField] Objeto que representa o campo de ligação do datasource principal
	 * 		rightKey		=> [\Sh\DataSourceField] Objeto que representa o campo de ligação do datasource secundário
	 * 		rightName		=> [\Sh\DataSourceField] Objeto que representa o campo de nomenclatura do datasource secundário
	 * 		dataFormatter	=> Mapeia o formator de dados do relacionamento [ fieldContent| ]
	 * 		relations		=> [array] Mapeia os relacionamentos do datasource secundário
	 */
	public function getRelations () {
		return $this->relations;
	}
	
	/**
	 * Recupera os relacionamentos aplicados ao dataSource em forma de fila, sem se preocupar com a hierarquia
	 * Mapeamento devolvido sem hierarquia e em formato de fila
	 * @return array
	 * 		id 				=> Identificador do relacionamento
	 * 		alias			=> Identificador simples do relacionamento, sem hierarquia
	 * 		index			=> Index identificador do relacionamento
	 * 		parentIndex		=> Index identificador do relacionamento pai
	 * 		idDataSource	=> Identiricador do DataSource
	 * 		dataSource		=> [\Sh\DataSource] Objeto do data source referenciado 
	 * 		leftKey			=> [\Sh\DataSourceField] Objeto que representa o campo de ligação do datasource principal
	 * 		rightKey		=> [\Sh\DataSourceField] Objeto que representa o campo de ligação do datasource secundário
	 * 		rightName		=> [\Sh\DataSourceField] Objeto que representa o campo de nomenclatura do datasource secundário
	 * 		dataFormatter	=> Mapeia o formator de dados do relacionamento [ fieldContent| ]
	 */
	public function getRelationsQueue () {
		if( !$this->relations ) { return array(); }
		
		$rels 		= $this->relations;
		$queue 		= array();
		
		while ( $rels ) {
			//Retiro o meu relacionamento atual do array de mapeamento
			$relation = array_pop($rels);
			//inserindo os relacionamentos filhos no array de controle de relacionamentos
			foreach ($relation['relations'] as $subRel) {
				$rels[$subRel['id']] = $subRel;
			}
			//removo o elemento relations no meu novo mapeador
			unset($relation['relations']);
			//insiro o relacionamento na fila
			$queue[$relation['id']] = $relation;
			
		}
		
		return $queue;
	}
	
	/**
	 * Recupera o DataSource principal do DataSource
	 * @return \Sh\DataSource
	 */
	public function getDataSource () {
		return $this->dataSource;
	}
	
	/**
	 * Método para inserir um novo relacionamento ao dataProvider
	 * @param array $relation
	 * 		id 				=> Identificador do relacionamento
	 * 		index			=> Index identificador do relacionamento
	 * 		parentIndex		=> Index identificador do relacionamento pai
	 * 		idDataSource	=> Identiricador do DataSource
	 * 		dataSource		=> [\Sh\DataSource] Objeto do data source referenciado 
	 * 		leftKey			=> [\Sh\DataSourceField] Objeto que representa o campo de ligação do datasource principal
	 * 		rightKey		=> [\Sh\DataSourceField] Objeto que representa o campo de ligação do datasource secundário
	 * 		rightName		=> [\Sh\DataSourceField] Objeto que representa o campo de nomenclatura do datasource secundário
	 * 		dataFormatter	=> Mapeia o formator de dados do relacionamento [ fieldContent| ]
	 * 		relations		=> [array] Mapeia os relacionamentos do datasource secundário
	 */
	public function pushRelation($relation) {
		if( isset( $this->relations[ $relation['id'] ] ) ) {
			\Sh\LoggerProvider::log('warning', 'DataProvider já possui relation com mesmo identificador "'.$relation['id'].'". Sobrescrevendo anterior.');
		}
		$this->relations[ $relation['id'] ] = $relation;
	}
	
	/**
	 * Método para inserir o relationsPathIndexMap que serve para efetuar um mapa de idRelation para indexRelation.
	 * Isso serve para termos acesso direto ao index devido 
	 * @param array $relationsPathIndexMap
	 */
	public function setRelationsPathIndexMap ($relationsPathIndexMap) {
		$this->relationsPathIndexMap = $relationsPathIndexMap;
	}
	
	/**
	 * Método para obter atraves da string de relationPath o relationIndex relacionado
	 * @param string $relationPath
	 * @return integer
	 */
	public function getRelationIndexFromRelationPath ( $relationPath ) {
		//Verificando e validando o relationPath
		if( !$relationPath || $relationPath[0] != '/' ) {
			$relationPath = '/'.$relationPath;
		}
		
		//BUSCANDO INDEX DO RELACIONAMENTO
		$index = 0;
		if( isset($this->relationsPathIndexMap[$relationPath]) ) {
			$index = (integer) $this->relationsPathIndexMap[$relationPath];
		}
		if( $relationPath == '/' || !$index ) { $index = 0; }
		
		return $index;
	}
	
	/**
	 * Método para inserir novo filtro ao DataProvider
	 * @param array $filter
	 * 		id
	 * 		relationPath
	 * 		relationIndex
	 * 		field
	 * 		operator
	 * 		defaultValue
	 * 		parameter
	 * 		required
	 * 		useNullIfBlank
	 */
	public function pushFilter($filter) {
		if( isset( $this->filters[ $filter['id'] ] ) ) {
			\Sh\LoggerProvider::log('warning', 'DataProvider já possui filtro com mesmo identificador "'.$filter['id'].'". Sobrescrevendo anterior.');
		}
		$this->filters[ $filter['id'] ] = $filter;
		
	}
	
	/**
	 * Método para limpar todos os filtros do dataProvider
	 */
	public function clearFilters () {
		$this->filters = array();
	}
	
	/**
	 * Método para setar a query de filtros customizada
	 * @param string $filterCustomQuery
	 */
	public function setFiltersCustomQuery ($filterCustomQuery) {
		$this->filtersCustomQuery = $filterCustomQuery;
	}
	
	/**
	 * Método para inserir novo agrupador ao DataProvider
	 * @param array $group
	 * 		field 			=> identificador do field a ser agrupado
	 * 		relationPath	=> Path para a relação de onde o field pertence
	 * 		relationIndex	=> Index para a relação de onde o field pertence
	 */
	public function pushGroup($group) {
		$id = $group['relationPath'].'/'.$group['field'];
		if( isset( $this->group[$id] ) ) {
			\Sh\LoggerProvider::log('warning', 'DataProvider já possui agrupamento pelo campo "'.$id.'". Sobrescrevendo anterior.');
		}
		$this->group[$id] = $group;
	}
	
	/**
	 * Método para inserir novo sort ao DataProvider
	 * @param array $sort
	 * 		field 			=> identificador do field a ser ordenado
	 * 		relationPath	=> Path para a relação de onde o field pertence
	 * 		relationIndex	=> Index para a relação de onde o field pertence
	 * 		order			=> Ordem para a ordenação. ASC | DESC | CUSTOM
	 */
	public function pushSort($sort) {
		$id = $sort['relationPath'].'/'.$sort['field'];
		if( isset( $this->sortable[$id] ) ) {
			\Sh\LoggerProvider::log('warning', 'DataProvider já possui ordenação pelo campo "'.$id.'". Sobrescrevendo anterior.');
		}
		$this->sortable[$id] = $sort;
	}
	
	
	public function setMaxRows($maxRows) 		{ $this->maxRows = (integer) $maxRows; }
	public function getMaxRows() 				{ return $this->maxRows; }
	
	
	public function getId() 					{ return $this->id; }
	public function getModuleId()				{ return $this->module; }
	public function getDataSourceId()			{ return $this->dataSource->getId(); }
	
	
	
	
	
}