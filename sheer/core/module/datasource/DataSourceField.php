<?php

namespace Sh;

/**
 * @author guilherme
 * Classe padrão para controlar os campos do Sheer
 *
 */
abstract class DataSourceField {
	
	protected $id;
	protected $name;
	protected $dataType;
	protected $required				= false;
	protected $setNullIfBlank		= true;
	protected $primaryKey			= false;
	protected $primaryName			= false;
	protected $defaultValue;
	protected $lookup				= false;
	/*
	 * Configurações de exibição e processamento
	 */
	protected $mask					= null;
	protected $validationType		= null;
	protected $uppercase 			= false;
	protected $lowercase 			= false;
	protected $renderType			= 'text';
	
	protected $options				= null;
	protected $value;
	
	
	public function __construct($id, $name) {
		
		$this->setId($id);
		$this->setName($name);
		
		$this->__autoconfig();
		
	}
	
	/*
	 * Métodos para validação de valor
	*/
// 	public function validateValuePrimitive ($data) {
// 		if( $data && strlen($data) > 0 ) {
// 			return true;
// 		}
// 		return false;
// 	}
	
// 	public function validateValueSheer ($data) {
// 		if( $data && strlen($data) > 0 ) {
// 			return true;
// 		}
// 		return false;
// 	}
	
	/**
	 * Método para validação do dado. Esperamos o dado formatado no modelo que o usuário deve inputa-lo
	 * @param array $data
	 * @return boolean
	 */
	public function validateValueInput ($data) {
		if( strlen($data) > 0 ) {
			return true;
		}
		return false;
	}
	
	/*
	 * Métodos para input do dado no field
	 */
	
	/**
	 * Método de input de dado no field. Esperamos o dado na forma primitiva [ Banco de dados, xml, json ]
	 * @param string $data
	 */
	public function setValuePrimitive ($data) {
		$this->value = $data;
	}
	
	/**
	 * Método de input de dado no field. Esperamos o dado formatado no modelo Sheer
	 * @param string $data
	 */
	public function setValueSheer ($data) {
		$this->value = $data;
	}
	
	/**
	 * Método de input de dado no field. Esperamos o dado formatado no modelo que o usuário deve inputa-lo
	 * @param unknown $data
	 */
	public function setValueInput ($data) {
		$this->value = $data;
	}
	
	/*
	 * Métodos para output do dado do field
	 */
	
	/**
	 * Método que recupera o valor do campo formatado de forma primitiva [ Banco de dados, xml, json ]
	 * @return string
	 */
	public function getValuePrimitive () {
		return $this->value;
	}
	
	/**
	 * Método que recupera o valor do campo formatado para o Sheer
	 * @return string
	 */
	public function getValueSheer () {
		return $this->value;
	}
	
	/**
	 * Método de autoconfiguração após criação
	 */
	protected function __autoconfig() {
		
	}
	
	/**
	 * Indica se o field em questão é primaryKey do DataSource
	 * @return boolean
	 */
	public function isPrimaryKey () {
		if( $this->primaryKey ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Indica se o field em questão é primaryName do DataSource
	 * @return boolean
	 */
	public function isPrimaryName () {
		if( $this->primaryName ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Método que determina se o campo possui opções para valor
	 * @return boolean
	 */
	public function hasOptions()				{ return !!$this->options; }
	
	/**
	 * Método que determina se o campo possuir opções que venham de uma variavel
	 * @return boolean
	 */
	public function hasOptionsFromVariable() {
		if( $this->options['optionsFromVariable'] ) {
			return true;
		}
		return false;
	}
	/**
	 * Método que determina se o campo possuir opções que venham de um DataProvider
	 * @return boolean
	 */
	public function hasOptionsFromDataProvider() {
		if( $this->options['optionsFromDataProvider'] ) {
			return true;
		}
		return false;
	}
	
	
	
	public function setId($id) 							{ $this->id = $id; }
	public function setName($name) 						{ $this->name = $name; }
	public function setRequired($required) 				{ $this->required = $required; }
	public function setSetNullIfBlank($setNullIfBlank) 	{ $this->setNullIfBlank = $setNullIfBlank; }
	public function setPrimaryKey($primaryKey) 			{ $this->primaryKey = $primaryKey; }
	public function setPrimaryName($primaryName) 			{ $this->primaryName = $primaryName; }
	public function setDefaultValue($defaultValue) 		{ $this->defaultValue = $defaultValue; }
	public function setLookup($lookup)					{ $this->lookup = !!$lookup; }
	public function setOptions($options)					{
		$this->options = $options;
	}

	public function setMask ($mask)						{ $this->mask = \Sh\Library::isMaskValid($mask); }
	public function setUpperCase ($upper) 				{ $this->uppercase = $upper; }
	public function setLowerCase ($lower) 				{ $this->lowercase = $lower; }
	
	/**
	 * Método criado especialmente para ser extendido pelo dataType image
	 * 
	 * @param \SimpleXMLElement $xmlPictureMap
	 * @return boolean
	 */
	public function setPicturesMapXml ( \SimpleXMLElement $xmlPictureMap ) {
		return true;
	}
	
	public function getId()					{ return $this->id; }
	public function getName()				{ return $this->name; }
	public function getDataType()			{ return $this->dataType; }
	public function getRequired()			{ return $this->required; }
	public function getSetNullIfBlank()		{ return $this->setNullIfBlank; }
	public function getPrimaryKey()			{ return $this->primaryKey; }
	public function getPrimaryName()		{ return $this->primaryName; }
	public function getDefaultValue()		{ return $this->defaultValue; }
	public function getLookup()				{ return $this->lookup; }
	public function getOptions()			{ return $this->options; }
	public function getMask()	 			{ return $this->mask; }
	public function getRenderType()	 		{ return $this->renderType; }
	public function getValidationType()		{ return $this->mask; }
	public function getUpperCase() 			{ return $this->uppercase; }
	public function getLowerCase() 			{ return $this->lowercase; }
	
	public function getPicturesMap () {
		return null;
	}
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * @param unknown $data
	 * @return unknown
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		return $data;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato Sheer
	 * @param unknown $data
	 * @return unknown
	 */
	static public function formatInputDataToSheer ($data) {
		return $data;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato primitivo
	 * @param unknown $data
	 * @return unknown
	 */
	static public function formatInputDataToPrimitive ($data) {
		return $data;
	}
	
	/**
	 * Método para formatar o dado do Sheer para a forma que um usuário o inputa
	 * @param unknown $data
	 * @return unknown
	 */
	static public function formatSheerDataToInput ($data) {
		return $data;
	}
	
	/**
	 * Método para determinar se o dado é Sheer
	 * 
	 * @param unknown $data
	 * @return boolean
	 */
	static public function isSheerFormat( $data ) {
		return true;
	}
	
	/**
	 * Método para determinar se o dado é Input de usuário
	 *
	 * @param unknown $data
	 * @return boolean
	 */
	static public function isInputFormat( $data ) {
		return true;
	}
	
	/**
	 * Método para determinar se o dado é primitivo
	 *
	 * @param unknown $data
	 * @return boolean
	 */
	static public function isPrimitiveFormat( $data ) {
		return true;
	}
	
	/**
	 * Método responsável por trazer as opções a serem utilizadas por um campo. 
	 * Não faz distinção se é de variavel ou módulo.
	 * 
	 * @param array $optionsConfig Configurações do campo
	 * @param array $dpFilters Dados para serem utilizados no filtro do DataProvider caso venha de lá
	 * @return array
	 */
	static public function getOptionsDataFromConfig ($optionsConfig, $dpFilters=array()) {
		//VERIFICAMOS SE A FONTE DOS DADOS ESTA BEM CONFIGURADA
		if( 
			(!isset($optionsConfig['optionsFromVariable']) || !$optionsConfig['optionsFromVariable'])
			&& (!isset($optionsConfig['optionsFromDataProvider']) || !$optionsConfig['optionsFromDataProvider'])
		) {
			return null;
		}
		
		$optionsData = array();
		
		//DETERMINO SE AS OPÇÕES VEM DE UM DATAPROVIDER OU DE UMA VARIAVEL
		//buscando de uma variavel
		if( $optionsConfig['optionsFromVariable'] ) {
			
			//VERIFICANDO BLANKOPTION
			if( $optionsConfig['blankOption'] ) {
				$optionsData[''] = $optionsConfig['blankOption'];
			}
			
			//determino a classe e a variavel
			list($className, $variable) = explode('/', $optionsConfig['optionsFromVariable']);
			//efetuo a busca da variavel
			try {
				$reflected = new \ReflectionProperty($className, $variable);
				$optionsData = $optionsData + $reflected->getValue();
			}
			catch (\ReflectionException $e) {
				return null;
			}
		}
		//buscando de um DataProvider
		//FIXME Inicialmente estou obrigando a descrição do KeyName e ValueName, o ideal seria depois buscar o PrimaryKey e PrimaryName
		else if ( $optionsConfig['optionsFromDataProvider'] ) {
			
			//VERIFICANDO BLANKOPTION
			if( $optionsConfig['blankOption'] ) {
				$optionsData[''] = $optionsConfig['blankOption'];
			}
			
			//BUSCANDO REGISTROS VÁLIDOS
			$response = \Sh\ContentProviderManager::loadContent($optionsConfig['optionsFromDataProvider'], $dpFilters);
			if( !$response || $response['total'] < 1 ) { return $optionsData; }
			
			//DETERMINANDO KEYNAME E VALUENAME
			{
				//BUSCO DATAPROVIDER E DATASOURCE SE NECESSÁRIO
				if( !$optionsConfig['keyName'] || !$optionsConfig['valueName'] ) {
					list($idModule, $idDataProvider) = explode('/', $optionsConfig['optionsFromDataProvider']);
					$dataProvider = \Sh\ModuleFactory::getModuleDataProvider($idModule, $idDataProvider);
					$dataSource = $dataProvider->getDataSource();
				}
				//determinando keyName
				if( !$optionsConfig['keyName'] ) {
					$optionsConfig['keyName'] = $dataSource->getPrimaryKey()->getId();
				}
				//determinando valueName
				if( !$optionsConfig['valueName'] ) {
					$optionsConfig['valueName'] = $dataSource->getPrimaryName()->getId();
				}
			}
			
			//RECUPERANDO OS DADOS E GERANDO ARRAY FINAL
			$tmpOptionsData = $response['results'];
			foreach ($tmpOptionsData as $opt) {
				$key = $opt[ $optionsConfig['keyName'] ];
				$value = $opt[ $optionsConfig['valueName'] ];
				$optionsData[$key] = $value;
			}
		}
		
		if( !$optionsData ) { $optionsData = null; }
		
		return $optionsData;
	}
	
	
}