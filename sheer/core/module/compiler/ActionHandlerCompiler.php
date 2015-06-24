<?php

namespace Sh;

abstract class ActionHandlerCompiler {
	
	/**
	 * Método compilador de ActionHandler, Fazemos
	 * 		compilar as informações básicas, 
	 * 		devolver o ActionHandler completo
	 * 
	 * @param \SimpleXMLElement $xmlRenderable
	 * @param \Sh\Module $module
	 * @throws \Sh\SheerException
	 * @return \Sh\DataSource
	 */
	static public function compile(\SimpleXMLElement $xmlActionHandler, \Sh\Module $module) {
		
		$id 			= (string) $xmlActionHandler->attributes()->id;
		$action 		= (string) $xmlActionHandler->attributes()->action;
		$idDataSource 	= (string) $xmlActionHandler->attributes()->datasource;
		
		//VERIFICANDO VALIDADE E CAPTURANDO DATASOURCE PRINCIPAL
		if( !isset( $module->dataSources[$idDataSource] ) ) {
			throw new \Sh\SheerException(array(
				'message'	=> 'DataSource ("'.$idDataSource.'") referenciado pelo ActionHandler ("'.$id.'") é inválido ou inexistente',
				'code'		=> 'SMC_XXXX'
			));
		}
		$dataSource = clone $module->dataSources[$idDataSource];
		
		//PRECISO PROCESSAR OS FIELDS DO DATASOURCE SETANDO OS VALORES CUSTOMIZADOS
		if( isset($xmlActionHandler->fields) && isset($xmlActionHandler->fields->field) ) {
			
			$fieldsCustomizados = array();
			
			//PROCESSANDO OS FIELDS DECLARADOS
			foreach ( $xmlActionHandler->fields->field as $xmlField ) {
				$idField = (string) $xmlField->attributes()->id;
				
				//determino se o field 
				if( !$dataSource->isField($idField) ) {
					\Sh\LoggerProvider::log('warning', 'Field "'.$idField.'" referenciado no ActionHandler "'.$id.'" não é válido');
					continue;
				}
				
				//captura o field
				$field = $dataSource->getField($idField, false);
				$fieldsCustomizados[$idField] = $idField;
				
				//verifica se é primaryKey
				if( $field->isPrimaryKey() ) { continue; }
				
				//CUSTOMIZANDO AS PROPRIEDADES
				//REQUIRED
				if( isset($xmlField->attributes()->required) ) {
					$field->setRequired( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->required) );
				}
				
				//SET_NULL_IF_BLANK
				if( isset($xmlField->attributes()->setNullIfBlank) ) {
					$field->setSetNullIfBlank( \Sh\Library::getBooleanFromXmlNode($xmlField->attributes()->setNullIfBlank) );
				}
				//DEFAULT_VALUE
				if( isset($xmlField->attributes()->defaultValue) && ( strlen((string) $xmlField->attributes()->defaultValue )) ) {
					$field->setDefaultValue( (string) $xmlField->attributes()->defaultValue );
				}
			}
			
			//REMOVENDO FIELDS DO DATASOURCE QUANDO NÃO FOR POR EXTENSAO E NÃO DECLARADOS
			if( isset($xmlActionHandler->fields->attributes()->extend) ) {
				$extend = \Sh\Library::getBooleanFromXmlNode($xmlActionHandler->fields->attributes()->extend);
				if( !$extend ) {
					
					$fields = $dataSource->getFields(false);
					foreach ( $fields as $idField=>$field ) {
						
						//verifico se o field foi customizado
						if( isset($fieldsCustomizados[$idField]) ) { continue; }
						//verifico se o field é primaryKey
						if( $field->isPrimaryKey() ) { continue; }
						
						//se nao cair em nenhum dos casos acima eu destruo o campo
						$dataSource->removeField($idField);
						
					}
					$fields = $dataSource->getFields(false);
				}
			}
		}
		
		//Criando ActionHandler
		$actionHandler = new \Sh\ActionHandler($id, $dataSource, $action);
		
		//DETERMINANDO SE ESTE ASSUME O GREENCARD
		if( isset($xmlActionHandler->attributes()->greencard) && \Sh\Library::getBooleanFromXmlNode($xmlActionHandler->attributes()->greencard) ) {
			$actionHandler->setGreenCard(true);
		} 
		
		//PROCESSANDO AS PERMISSÕES
		if( $xmlActionHandler->permissions ) {
			
			//Verificando se alteramos a permissão default
			$permDefault = null;
			if( isset($xmlActionHandler->permissions->attributes()->default) ) {
				$permDefault = (string) $xmlActionHandler->permissions->attributes()->default;
			}
			$actionHandler->setPermissionDefault($permDefault);
			
			
			//Iterando por permissoes customizadas
			foreach ( $xmlActionHandler->permissions->perm as $xmlPerm ) {
				
				$tmpPermission = array(
					'profile' => null,
					'accept' => true
				);
				
				//Verificando a existencia do profile
				if( !isset($xmlPerm->attributes()->profile) ) {
					continue;
				}
				$tmpPermission['profile'] = (string) $xmlPerm->attributes()->profile;
				if( strlen($tmpPermission['profile']) < 3 ) { continue; }
				
				//Definindo a aceitação
				if( isset($xmlPerm->attributes()->accept) ) {
					$tmpPermission['profile'] = \Sh\Library::getBooleanFromXmlNode($xmlPerm->attributes()->accept);
				}
				
				//Setando a permissão no actionHandler
				$actionHandler->setPermission($tmpPermission['profile'], $tmpPermission['accept']);
			}
			
		}
		
		return $actionHandler;
	}
	
	/**
	 * Fabrica o ActionHandler add default para o DataSource do módulo
	 * @param \Sh\DataSource $dataSource
	 * @param \Sh\Module $module
	 * @return \Sh\ActionHandler
	 */
	static public function getDefaultAddActionHandlerFromDataSource (\Sh\DataSource $dataSource, \Sh\Module $module) {
		$id = $dataSource->getId().'_add';
		$action = 'add';
		
		$actionHandler = new \Sh\ActionHandler($id, $dataSource, $action);
		return $actionHandler;
	}
	
	/**
	 * Fabrica o ActionHandler update default para o DataSource do módulo
	 * @param \Sh\DataSource $dataSource
	 * @param \Sh\Module $module
	 * @return \Sh\ActionHandler
	 */
	static public function getDefaultUpdateActionHandlerFromDataSource (\Sh\DataSource $dataSource, \Sh\Module $module) {
		$id = $dataSource->getId().'_update';
		$action = 'update';
	
		$actionHandler = new \Sh\ActionHandler($id, $dataSource, $action);
		return $actionHandler;
	}
	
	/**
	 * Fabrica o ActionHandler delete default para o DataSource do módulo
	 * @param \Sh\DataSource $dataSource
	 * @param \Sh\Module $module
	 * @return \Sh\ActionHandler
	 */
	static public function getDefaultDeleteActionHandlerFromDataSource (\Sh\DataSource $dataSource, \Sh\Module $module) {
		$id = $dataSource->getId().'_delete';
		$action = 'delete';
	
		$actionHandler = new \Sh\ActionHandler($id, $dataSource, $action);
		return $actionHandler;
	}
		
}