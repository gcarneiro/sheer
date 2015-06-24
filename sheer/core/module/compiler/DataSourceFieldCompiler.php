<?php

namespace Sh;

abstract class DataSourceFieldCompiler {
	
	/**
	 * Método responsável por compilar e retonar um field corretamente a partir de um xmlField
	 * Executa toda a sua configuração
	 * 
	 * @param \SimpleXMLElement $xmlField
	 * @return \Sh\DataSourceField
	 */
	static public function compiler(\SimpleXMLElement $xmlField) {
		
		$id = (string) $xmlField->attributes()->id;
		$name = (string) $xmlField->attributes()->name;
		$dataType = (string) $xmlField->attributes()->dataType;
		
		$field = self::getFieldObjectFromDataType($id, $name, $dataType);
		
		//IREI TRATAR OS PARAMETROS DE CONFIGURACAO PARA O FIELD
		//REQUIRED
		if( isset($xmlField->attributes()->required) ) {
			$field->setRequired( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->required) );
		}
		
		//SET_NULL_IF_BLANK
		if( isset($xmlField->attributes()->setNullIfBlank) ) {
			$field->setSetNullIfBlank( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->setNullIfBlank) );
		}
		
		//PRIMARY_KEY
		if( isset($xmlField->attributes()->primaryKey) ) {
			$field->setPrimaryKey( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->primaryKey) );
		}
		
		//PRIMARY_NAME
		if( isset($xmlField->attributes()->primaryName) ) {
			$field->setPrimaryName( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->primaryName) );
		}
		
		//DEFAULT_VALUE
		if( isset($xmlField->attributes()->defaultValue) && ( strlen((string) $xmlField->attributes()->defaultValue )) ) {
			$field->setDefaultValue( (string) $xmlField->attributes()->defaultValue );
		}
		
		//LOOKUP
		if( isset($xmlField->attributes()->lookup) ) {
			$field->setLookup( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->lookup) );
		}
		
		//MASK
		if( isset($xmlField->attributes()->mask) ) {
			$field->setMask( (string) $xmlField->attributes()->mask );
		}
		
		//UPPERCASE
		if( isset($xmlField->attributes()->uppercase) ) {
			$field->setUpperCase( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->uppercase) );
		}
		
		//LOWERCASE
		if( isset($xmlField->attributes()->lowercase) ) {
			$field->setLowerCase( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->lowercase) );
		}
		
		//OPTIONS
		if( isset($xmlField->options) ) {
			$optionsConfig = self::parseOptionsConfigFromXmlNode($xmlField->options);
			if( $optionsConfig ) {
				$field->setOptions($optionsConfig);
			}
		}
		
		//PICTUREMAP
		if( isset($xmlField->pictures) && isset($xmlField->pictures->map) ) {
			$field->setPicturesMapXml($xmlField->pictures);
		}
		
		return $field;
	}
	
	/**
	 * Método para obter as configurações de opções do Nó Xml
	 * @param \SimpleXMLElement $node
	 * @return false se não existir opções válidas
	 * 			array ("optionsFromDataProvider", "optionsFromVariable", "keyName", "valueName", "renderType", "blankOption")
	 */
	static protected function parseOptionsConfigFromXmlNode (\SimpleXMLElement $node) {
		
		$optionsSourceFound = false;
		
		$config = array(
			'optionsFromDataProvider' 	=> null,
			'optionsFromVariable'		=> null,
			'keyName'					=> null,
			'valueName'					=> null,
			'renderType'				=> 'select',
			'blankOption'				=> null
		);
		
		//VERIFICO SE AS OPÇÕES VEM DE UM DATAPROVIDER
		if( !$optionsSourceFound && isset($node->attributes()->getOptionsFromDataProvider) ) {
			$config['optionsFromDataProvider'] = (string) $node->attributes()->getOptionsFromDataProvider;
			//verificando se o valor é válido
			if( strpos($config['optionsFromDataProvider'], '/') !== false ) {
				$optionsSourceFound = true;
			}
		}
		
		//VERIFICO SE AS OPÇÕES VEM DE UMA VARIAVEL
		if( !$optionsSourceFound && isset($node->attributes()->getOptionsFromVariable) ) {
			$config['optionsFromVariable'] = (string) $node->attributes()->getOptionsFromVariable;
			//verificando se o valor é válido
			if( strpos($config['optionsFromVariable'], '/') !== false ) {
				$optionsSourceFound = true;
			}
		}
		
		//SÓ DEVO PROSSEGUIR SE ENCONTREI UMA FONTE VALIDA DE DADOS
		if( !$optionsSourceFound ) { return false; }
		
		//renderType
		if( isset($node->attributes()->renderType) ) { $config['renderType'] = self::getRenderTypeFromString( (string) $node->attributes()->renderType ); }
		
		if( isset($node->attributes()->keyName) ) { $config['keyName'] = (string) $node->attributes()->keyName; }
		if( isset($node->attributes()->valueName) ) { $config['valueName'] = (string) $node->attributes()->valueName; }
		if( isset($node->attributes()->blankOption) ) { $config['blankOption'] = (string) $node->attributes()->blankOption; }
		
		return $config;
		
	}
	
	/**
	 * Determina qual é o renderType a partir de uma string qualquer
	 * @param string $string
	 * @return string
	 */
	static protected function getRenderTypeFromString ( $string ) {
		
		switch ( strtolower($string) ) {
			case 'checkbox':
				return 'checkbox';
			case 'radio':
				return 'radio';
			default:
				return 'select';
		}
		
	}
	
	/**
	 * Função que recebe as informações básicas para um field e retorna o Objeto correto
	 * @param string $id
	 * @param string $name
	 * @param string $dataType
	 * @return \Sh\DataSourceField
	 */
	static protected function getFieldObjectFromDataType($id, $name, $dataType) {
		
		$classCall = '\Sh\Field';
		
		switch ( strtolower($dataType) ) {
			case 'integer':
				$classCall .= 'Integer';
				break;
			case 'decimal':
				$classCall .= 'Decimal';
				break;
			case 'float':
				$classCall .= 'Float';
				break;
			case 'dinheiro':
				$classCall .= 'Dinheiro';
				break;
				
			case 'string':
				$classCall .= 'String';
				break;
			case 'text':
				$classCall .= 'Text';
				break;
			case 'email':
				$classCall .= 'Email';
				break;
			case 'html':
				$classCall .= 'Html';
				break;
				
			case 'date':
				$classCall .= 'Date';
				break;
			case 'datetime':
				$classCall .= 'DateTime';
				break;
				
			case 'file':
				$classCall .= 'File';
				break;
			case 'image':
				$classCall .= 'Image';
				break;
			default:
				$classCall = '\Sh\Project\\'.$dataType;
				$classExists = null;
				$classExists = class_exists($classCall);
				if(!$classExists){
					$classCall = self::getClassCallFromCustomDataType($dataType);
				}
				break;
		}
		return new $classCall($id, $name);
		
	}
	
	/**
	 * Método para determinar DataType do Field a partir de um DataType desconhecido
	 * @param string $dataType
	 * @throws \Sh\SheerException
	 */
	static protected function getClassCallFromCustomDataType ($dataType) {
		
		try {
			//PRECISO VERIFICAR SE O TIPO DE DADO É CUSTOMIZADO
			if( strpos($dataType, 'class:') === false ) {
				throw new \Sh\SheerException(array(
					'message'=>null,
					'code'=>-1
				));
			}
				
			//PASSANDO PARA CÁ DEVEMOS TER UMA CLASSE CUSTOMIZADA PARA O FIELD, IREMOS BUSCAR
			$className = str_replace('class:', '', $dataType);
			//Caso o nome da classe não comece com \, iremos inserir a barra para determinar namespace \;
			if( $className[0] != '\\' ) {
				$className = '\\'.$className;
			}
		
			//VERIFICANDO SE A CLASSE EXISTE
			if( !class_exists($className, false) ) {
				throw new \Sh\SheerException(array(
					'message'=>null,
					'code'=>-2
				));
			}
				
			//VERIFICANDO SE A CLASSE PERTENCE É HERANCA DE DATASOURCEFIELD
			if( !is_subclass_of($className, '\Sh\DataSourceField') ) {
				throw new \Sh\SheerException(array(
					'message'=>null,
					'code'=>-3
				));
			}
				
			$classCall = $className;
		}
		catch (\Sh\SheerException $e) {
			$info = $e->getInfo();
			if( $info['code'] < 0 ) {
				\Sh\LoggerProvider::log('warning', 'Assumindo tipo "String" para field com configuração de DataType desconhecida. [Code:"'.$info['code'].'"]');
			}
			else {
				var_dump('Erro desconhecido');
				exit;
			}
			
			$classCall = '\Sh\FieldString';
		}
		
		return $classCall;
	}
	
}