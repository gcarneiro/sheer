<?php

namespace Sh;

/**
 * @author guilherme
 * Classe responsável pelos DataSources do Sheer
 *
 */
class DataSource {
	
	protected $module;
	protected $id;
	protected $table;
	protected $parser;
	
	protected $primaryKeyId;
	protected $primaryNameId;
	
	protected $keys = array();
	
	protected $fields = array();
	
	/*
	 * Addons
	 */
	protected $publicationHistory = true;
	protected $publicationMetadata = true;
	protected $imageRepository = false;
	protected $imageRepositoryMaps = array(
		'sheer' => 'sheer'
	);
	
	public function __construct($module, $id, $table, $parser=null) {
		
		$this->module 	= $module;
		$this->id 		= $id;
		$this->table 	= $table;
		
		//Se tivermos o parser definido o setamos no namespace do módulo
		if( $parser ) {
			$this->parser	= '\\Sh\\Modules\\'.$module.'\\'.$parser;
		}
		
	}
	
	public function getId() 		{ return $this->id; }
	public function getModuleId()	{ return $this->module; }
	public function getTable()	{ return $this->table; }
	
	/**
	 * Captura um objeto para parser de dados
	 * 
	 * @return \Sh\\GenericDataParser
	 */
	public function getParser () {
		
		if( !$this->parser ) {
			return null;
		}
		
		if( !is_subclass_of($this->parser, '\\Sh\\GenericDataParser') ) {
			\Sh\LoggerProvider::log('warning', 'DataParser declarado para o DS: '.$this->module.'/'.$this->id.' não é dependente de \\Sh\\GenericDataParser');
			return null;
		}
		
		$dataParser = new $this->parser();
		return $dataParser;
		
	}
	
	/**
	 * Método para setar a informação quanto a utilização do addon
	 * @param string $addon Nome do Addon desejado [publicationHistory, publicationMetadata, imageRepository]
	 * @param boolean $enabled
	 */
	public function setAddon($addon, $enabled) {
		
		switch ($addon) {
			case 'publicationHistory':
				$this->publicationHistory = (boolean) $enabled;
				break;
			case 'publicationMetadata':
				$this->publicationMetadata = (boolean) $enabled;
				break;
			case 'imageRepository':
				$this->imageRepository = (boolean) $enabled;
				break;
		}
		return;
	}
	/**
	 * Método para inserir um novo mapa para o image repository
	 * @param string $mapId
	 */
	public function addImageRepositoryMap ( $mapId ) {
		$this->imageRepositoryMaps[$mapId] = $mapId;
	}
	
	public function hasPublicationHistory () { return $this->publicationHistory; }
	public function hasPublicationMetadata () { return $this->publicationMetadata; }
	public function hasImageRepository () { return $this->imageRepository; }
	public function getImageRepositoryMaps() { return $this->imageRepositoryMaps; }
	
	/**
	 * Método para vincular Field ao DataSource
	 * @param \Sh\DataSourceField $field
	 */
	public function pushField(\Sh\DataSourceField $field) {
		
		if( isset($this->fields[$field->getId()]) ) {
			\Sh\LoggerProvider::log('warning', 'Identificador de field já utilizado previamente para o dataSource. Sobrescrevendo anterior. [DS:"'.$this->id.'", Field:"'.$field->getId().'"]');
		}
		
		$this->fields[$field->getId()] = $field;
		
	}
	
	/**
	 * Método para recuperar o array que contém os fields do DataSource
	 * 
	 * Clonamos todos os campos pois o PHP passa a referencia do field
	 * 
	 * @param boolean $clone Determina se devemos clonar os fields
	 * @return multitype:\Sh\DataSourceField 
	 */
	public function getFields($clone=true) {
		$fieldsArray = array();
		$clone = !!$clone;
		
		if( $clone ) {
			foreach( $this->fields as $f ) {
				$fieldsArray[$f->getId()] = clone $f;
			}
		}
		else {
			$fieldsArray = $this->fields;
		}
		
		return $fieldsArray;
	}
	
	/**
	 * Método para indicar o primaryKeyId do DataSource
	 * @param unknown $primaryKeyId
	 */
	public function setPrimaryKey($primaryKeyId) {
		$this->primaryKeyId = $primaryKeyId;
	}
	/**
	 * Recupera o field que é primaryKey do DataSource
	 * @param boolean $clone Determina se o campo deve ser clonado 
	 * @return \Sh\DataSourceField
	 */
	public function getPrimaryKey($clone=true) {
		if( $this->primaryKeyId && isset( $this->fields[$this->primaryKeyId] ) ) {
			return $this->getField($this->primaryKeyId, $clone);
		}
		return null;
	
	}
	
	/**
	 * Método para indicar o primaryNameId do DataSource
	 * @param unknown $primaryNameId
	 */
	public function setPrimaryName($primaryNameId) {
		$this->primaryNameId = $primaryNameId;
	}
	/**
	 * Recupera o field que é primaryName do DataSource
	 * @param boolean $clone Determina se o campo deve ser clonado 
	 * @return \Sh\DataSourceField
	 */
	public function getPrimaryName($clone=true) {
		if( $this->primaryNameId && isset( $this->fields[$this->primaryNameId] ) ) {
			return $this->getField($this->primaryNameId, $clone);
		}
		return null;
	}
	
	/**
	 * Determina se o identificador corresponde a algum field do DataSource
	 * @param string $idField
	 */
	public function isField($idField) {
		return isset($this->fields[$idField]);
	}
	
	/**
	 * Recupera um field do DataSource pelo seu identificador
	 * @param string $idField
	 * @param boolean $clone Determina se devemos clonar o objeto
	 * @return \Sh\DataSourceField
	 */
	public function getField($idField, $clone=true) {
		$clone = !!$clone;
		$f = null;
		
		if( isset($this->fields[$idField]) ) {
			$f = $this->fields[$idField];
			if($clone) { $f = clone $f; }
	
			return $f;
		}
		return null;
	}
	
	/**
	 * Método utilizado para remover um field do DataSource
	 * Utilizado com muito cuidado.
	 * 		Atualmente utilizado apenas em
	 * 			\Sh\ActionHandlerCompiler
	 * @param unknown $idField
	 */
	public function removeField($idField) {
		unset($this->fields[$idField]);
	}
	
	/**
	 * Função para customizar o clone do objeto.
	 * Precisamos altera-lo para os fields não forem passados como referência, e sim como novos objetos.
	 */
	public function __clone() {
		
		$clonedFields = $this->getFields();
		$this->fields = $clonedFields;
		
	}
	
	
	
	
}