<?php

namespace Sh;

abstract class DataSourceCompiler {
	
	
	/**
	 * Método compilador de DataSource, Fazemos
	 * 		compilar as informações básicas, 
	 * 		processar todos os fields 
	 * 		devolver o DataSource completo
	 * 
	 * @param \SimpleXMLElement $xmlDataSource
	 * @param \Sh\Module $module
	 * @throws \Sh\SheerException
	 * @return \Sh\DataSource
	 */
	static public function compile(\SimpleXMLElement $xmlDataSource, \Sh\Module $module) {
		
		$id = (string) $xmlDataSource->attributes()->id;
		$table = (string) $xmlDataSource->attributes()->table;
		
		//Verificando se temos o dataParser setado
		$dataParser = null;
		if( isset($xmlDataSource->attributes()->parser) ) {
			$dataParser = (string) $xmlDataSource->attributes()->parser;
		}
		
		$dataSource = new \Sh\DataSource($module->id, $id, $table, $dataParser);
		
		//VERIFICANDO VALIDADE DOS FIELDS DO DATASOURCE
		if( !isset($xmlDataSource->fields) || !isset($xmlDataSource->fields->field) ) {
			throw new \Sh\SheerException(array(
				'message'=>'Não existem campos definidos para o DataSource',
				'code'=>'SMC_XXXX'
			));
		}
		
		//PROCESSANDO OS FIELDS DO DATASOURCE
		foreach( $xmlDataSource->fields->field as $xmlField ) {
			$field = \Sh\DataSourceFieldCompiler::compiler($xmlField);
			$dataSource->pushField($field);
			
			//marcando o primaryKey do DataSource
			if( $field->isPrimaryKey() ) {
				$dataSource->setPrimaryKey($field->getId());
			}
			
			//marcando o primaryName do DataSource
			if( $field->isPrimaryName() ) {
				$dataSource->setPrimaryName($field->getId());
			}
		}
		
		//Verifico se o módulo possui PrimaryKey setado
		if( $dataSource->getPrimaryKey(false) == null ) {
			throw new \Sh\SheerException(array(
				'code' => 'SMC_XXXX',
				'message' => 'DataSource "'.$dataSource->getModuleId().'/'.$dataSource->getId().'" não possui primaryKey definido.'
			));
		}
		//Verifico se o módulo possui PrimaryName setado
		if( $dataSource->getPrimaryName(false) == null ) {
			throw new \Sh\SheerException(array(
				'code' => 'SMC_XXXX',
				'message' => 'DataSource "'.$dataSource->getModuleId().'/'.$dataSource->getId().'" não possui primaryName definido.'
			));
		}
		
		//PROCESSANDO OS ADDONS
		if( isset( $xmlDataSource->addons ) ) {
			
			//verificando publicationHistory
			if( isset($xmlDataSource->addons->publicationHistory) && isset($xmlDataSource->addons->publicationHistory->attributes()->enabled) ) {
				$enabled = self::xmlStringToBoolean($xmlDataSource->addons->publicationHistory->attributes()->enabled);
				$dataSource->setAddon('publicationHistory', $enabled);
			}
			
			//verificando publicationMetadata
			if( isset($xmlDataSource->addons->publicationMetadata) && isset($xmlDataSource->addons->publicationMetadata->attributes()->enabled) ) {
				$enabled = self::xmlStringToBoolean($xmlDataSource->addons->publicationMetadata->attributes()->enabled);
				$dataSource->setAddon('publicationMetadata', $enabled);
			}
			
			//verificando publicationHistory e registrando mapas
			if( isset($xmlDataSource->addons->imageRepository) && isset($xmlDataSource->addons->imageRepository->attributes()->enabled) ) {
				$enabled = self::xmlStringToBoolean($xmlDataSource->addons->imageRepository->attributes()->enabled);
				$dataSource->setAddon('imageRepository', $enabled);
				//Determinando os mapas
				if( isset($xmlDataSource->addons->imageRepository->map) ) {
					foreach ( $xmlDataSource->addons->imageRepository->map as $xmlMap ) {
						if( isset($xmlMap->attributes()->id) ) {
							$mapId = (string) $xmlMap->attributes()->id;
							$dataSource->addImageRepositoryMap($mapId);
						}
					}
				}
			}
			
		}
		
		return $dataSource;
	}
	
	static protected function xmlStringToBoolean ( $string ) {
		
		if( $string == 'false' || $string == '0' ) {
			return false;
		}
		return true;
		
	}
	
	
}