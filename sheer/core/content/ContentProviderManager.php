<?php

namespace Sh;

abstract class ContentProviderManager {
	
	/**
	 * Método para carregar um único conteudo através do seu PrimaryKey ID
	 * 
	 * @param string $dataSourceAlias Sendo formatado como "idModule/idDataSource"
	 * @param string $contentId Identificador de 32|36 caracteres
	 * @param \PDO $conn Conexão a ser utilizada com o banco de dados
	 * 
	 * @return array | false em caso de erro/nao encontrado
	 */
	static function loadContentById ( $dataSourceAlias, $contentId, \PDO $conn=null ) {
		
		try {
			//determinando nome do modulo e datasource
			list($idModule, $idDataSource) = explode('/', $dataSourceAlias);
			
			//Capturando dataSource para pegar primaryKeyId
			$datasource = \Sh\ModuleFactory::getModuleDataSource($idModule, $idDataSource);
			if( !$datasource ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'DataSource "'.$dataSourceAlias.'" é inválido para carregamento de conteúdo'
				));
			}
			$primaryKeyId = $datasource->getPrimaryKey(false)->getId();

			//determinando DataProvider e filtros
			$dataProvider = $idModule.'/'.$idDataSource.'_detalhes';
			$filter = array($primaryKeyId=>$contentId);
			
			//carregando conteudo
			$response = self::loadContent($dataProvider, $filter, array('conn'=>$conn));
			if( $response && $response['total'] == 1 ) {
				return reset($response['results']);
			}
			return false;
		}
		catch ( \Sh\SheerException $e ) {
			return false;
		}
		
	}
	
	/**
	 * Alias para loadContentById
	 */
	static function loadItem ( $dataSourceAlias, $contentId, \PDO $conn=null ) {
		return self::loadContentById($dataSourceAlias, $contentId, $conn);
	}
	
	/**
	 * Método responsável por receber a identificação completa de um dataProvider, juntamente a filtros e página de exibição e retornar os seus resultados.
	 * 
	 * @param string $dataProvider Identificador de dataProvide completo idModule/idDataProvider
	 * @param array $filters
	 * @param array $configs ['page'=>1, 'maxRows'=>null, 'sort'=>null, 'requiredFilters'=>null, 'conn'=>\PDO]
	 * 		page 			=> Número da página a carregar os registros
	 * 		maxRows 		=> Quantidade de registros desejadas.
	 * 								"null" Para assumir o definido no DataProvider
	 * 								0 Para trazer todos os registros disponíveis
	 * 								X para X registros
	 * 		sort			=> Opções de ordenação customizadas
	 * 							array[
	 * 								relationPath
	 * 								field
	 * 								order
	 * 							],
	 * 		requiredFilters	=> Marca quais os filtros devem ser tratados como obrigatórios nesse request
	 * 							array[idFilter=>true]
	 *		conn	=> \PDO Conexão com o banco de dados
	 * 
	 * @param \PDO $conn 
	 * 			Esta é a conexão com o banco de dados a ser utilizada, 
	 * 				esta mesma conexão pode ser enviada dentro do parametros configs
	 * 				mas se enviada diretamente como parametro terá preferencia
	 * 
	 * @throws \Sh\SheerException
	 * @return array | false em caso de erro
	 */
	static function loadContent ($dataProvider, $filters=array(), $configs=null, \PDO $conn=null ) {
		
		try {
			list($idModule, $idDataProvider) = explode('/', $dataProvider);
			
			//Começo esse array vazio para tratar o conn no parser de configuracoes
			$connConfig = array();
			
			//Começando com o $configs
			//PROCESSANDO CONFIGURAÇÕES
			//Irei assumir que a conexão como quarto parametro é mais importante que a enviada dentro das configs
			if( $conn && is_a($conn, '\PDO') ) {
				$connConfig['conn'] = $conn;
			}
			$configuration = self::getConfigurationFromPageAndConfigs($configs, $connConfig);
			
			
			//CARREGANDO MODULO
			$module = \Sh\ModuleFactory::getModuleFull($idModule);
			if( !$module ) {
				throw new \Sh\SheerException(array(
					'message' => 'Erro ao carregar módulo do Data Provider',
					'code' => 'SCP_XXXX'
				));
			}
			//CARREGANDO DATAPROVIDER
			if( !isset($module->dataProviders[$idDataProvider]) || !$module->dataProviders[$idDataProvider] ) {
				throw new \Sh\SheerException(array(
					'message' => 'Erro ao carregar dataProvider do Módulo',
					'code' => 'SCP_XXXX'
				));
			}
			$dataProvider = $module->dataProviders[$idDataProvider];
			
			//CRIANDO CONTENTPROVIDER
			$contentProvider = self::getContentProviderFromDataProvider($dataProvider);
			//buscando dados
			$data = $contentProvider->getData($filters, $configuration);
			
			return $data;
		}
		catch ( \Sh\SheerException $e ) {
			return false;
		}
		
	}
	
	/**
	 * Método responsável por receber a identificação completa de um dataProvider e irá aplicar todas as configurações customizadas pelo desenvolvedor, jutando/sobrescrevendo as configurações iniciais do dataProvider
	 * 
	 * Devemos utilizar este com cuidado dentro de loops e buscas muito diferentes
	 * FIXME devo pensar em alguma possibilidade de voltar um objeto onde eu só utilize os dados para busca
	 * 
	 * Filtros -> Obrigatório
	 * 		Este irá desconsiderar qualquer filtro já considerado no dataProvider original aproveitando todas as suas outras configurações
	 * 
	 * @param string $dataProvider $idDataProvider
	 * 
	 * @param array $filters Configuração dos fitros
	 * 		idFilter => infoFilter
	 * 			infoFilter = [string|integer] caso seja o valor a ser buscado. Será assumido o dataSource Principal com operador equal
	 * 			infoFilter = [array] Serão customizados as seguintes informações [relationPath, field, operator, dateFunction, defaultValue, parameter, required]
	 * 
	 * @param array $configs ['page'=>1, 'maxRows'=>null, 'sort'=>null, 'requiredFilters'=>null, 'conn'=>\PDO]
	 * 		page 			=> Número da página a carregar os registros
	 * 		maxRows 		=> Quantidade de registros desejadas.
	 * 								"null" Para assumir o definido no DataProvider
	 * 								0 Para trazer todos os registros disponíveis
	 * 								X para X registros
	 * 		sort			=> Opções de ordenação customizadas
	 * 							array[
	 * 								relationPath
	 * 								field
	 * 								order
	 * 							],
	 * 		requiredFilters	=> Marca quais os filtros devem ser tratados como obrigatórios nesse request
	 * 							array[idFilter=>true]
	 *		conn	=> \PDO Conexão com o banco de dados
	 *
	 * @throws \Sh\SheerException
	 * @return array[available, results, total]
	 * 
	 * @deprecated em 15.04.07
	 */
	static function loadContentCustomConfig ($dataProvider, $filters, $configs=array()) {
		
		try {
			list($idModule, $idDataProvider) = explode('/', $dataProvider);
			
			//processando configurações
			$configuration = self::getConfigurationFromPageAndConfigs($configs);
				
			//CARREGANDO MODULO
			$module = \Sh\ModuleFactory::getModuleFull($idModule);
			if( !$module ) {
				throw new \Sh\SheerException(array(
						'message' => 'Erro ao carregar módulo do Data Provider',
						'code' => 'SCP_XXXX'
				));
			}
			//CARREGANDO DATAPROVIDER
			if( !isset($module->dataProviders[$idDataProvider]) || !$module->dataProviders[$idDataProvider] ) {
				throw new \Sh\SheerException(array(
						'message' => 'Erro ao carregar dataProvider do Módulo',
						'code' => 'SCP_XXXX'
				));
			}
			$dataProvider = clone $module->dataProviders[$idDataProvider];
			
			//limpando os filtros do dataProvider
			$dataProvider->clearFilters();
			
			//variavel de controle dos valores a serem utilizados pelos filtros
			$data = array();
			
			//processando novos filtros requeridos
			foreach ( $filters as $idFilter=>$fil ) {
				
				//criando padrão
				$tmpFilter = array(
					'id'			=> $idFilter,
					'relationPath'	=> '/',
					'relationIndex' => 0,
					'field'			=> $idFilter,
					'operator'		=> 'equal',
					'dateFunction'	=> null,
					'defaultValue'	=> null,
					'parameter' 	=> $idFilter,
					'required' 		=> false,
				);
				
				//OBTENDO INFORMAÇÕES ADICIONAIS DA CONFIGURAÇÃO DO FILTRO
				if( is_array($fil) ) {
					
					//relationPath e relationIndex
					if( isset($fil['relationPath']) && $fil['relationPath'] ) {
						//capturando o index do relacionando para determinar se é válido
						$tmpFilter['relationIndex'] = $dataProvider->getRelationIndexFromRelationPath($fil['relationPath']);
						if( is_numeric($tmpFilter['relationIndex']) && $tmpFilter['relationIndex'] >= 0 ) {
							throw new \Sh\SheerException(array(
									'code' => null,
									'message' => 'Filtro customizado não referencia relacionamento válido'
							));
						}
						$tmpFilter['relationPath'] = $fil['relationPath'];
					}
					
					//field
					if( isset($fil['field']) && $fil['field'] ) {
						$tmpFilter['field'] = $fil['field'];
					}
					
					//operador
					if( isset($fil['operator']) && $fil['operator'] ) {
						$tmpFilter['operator'] = $fil['operator'];
					}
					
					//dateFunction
					if( isset($fil['dateFunction']) && $fil['dateFunction'] ) {
						$tmpFilter['dateFunction'] = $fil['dateFunction'];
					}
					
					//defaultValue
					if( isset($fil['defaultValue']) && $fil['defaultValue'] ) {
						$tmpFilter['defaultValue'] = $fil['defaultValue'];
					}
					
					//parameter
					if( isset($fil['parameter']) && $fil['parameter'] ) {
						$tmpFilter['parameter'] = $fil['parameter'];
					}
					
					//required
					if( isset($fil['required']) && $fil['required'] ) {
						$tmpFilter['required'] = $fil['required'];
					}
					
					//capturando valor do filtro
					if( isset($fil['value']) ) {
						$data[$tmpFilter['parameter']] = $fil['value'];
					}
					else {
						$data[$tmpFilter['parameter']] = null;
					}
				}
				else {
					$data[$tmpFilter['parameter']] = $fil;
				}
				
				//ADICIONANDO O FILTRO CUSTOMIZADO NO DATAPROVIDER
				$dataProvider->pushFilter($tmpFilter);
				
			}
			
			//CRIANDO CONTENTPROVIDER
			$contentProvider = self::getContentProviderFromDataProvider($dataProvider);
			//buscando dados
			$data = $contentProvider->getData($data, $configuration);
				
			return $data;
			
			
			
			
		}
		catch ( \Sh\SheerException $e ) {
			return false;
		}
		
	}
	
	/**
	 * Método que recupera o contentProvider a partir de um dataProvider
	 * 
	 * @param \Sh\DataProvider $dataProvider
	 * @return \Sh\ContentProvider
	 */
	static protected function getContentProviderFromDataProvider ( \Sh\DataProvider $dataProvider ) {
		
		$contentProviderClassName = $dataProvider->getContentProvider();
		$contentProvider = new $contentProviderClassName($dataProvider);
		
		return $contentProvider;
	}
	
	/**
	 * Método para processar as configurações antigas para o novo padrão de configurações de carregamento de informações
	 * Este deverá ser removido quando deixarmos de aceitar o padrão antigo de getData
	 * 
	 * @param array $configs
	 * @param array $other //Para que serve este cara?
	 * @return array['page', 'maxRows', 'sort', 'conn', 'requiredFilters']
	 */
	static public function getConfigurationFromPageAndConfigs ($configs=null, $other=null) {
		
		$configuration = ['page'=>1, 'maxRows'=>null, 'sort'=>null, 'conn'=>null, 'requiredFilters'=>null];

		//Determinando a página desejada quando for numérica
		if ( is_numeric($configs) && $configs > 0 ) {
			$configuration['page'] = $configs;
		}
		//Se config for um PDO
		else if ( is_a($configs, '\PDO') ) {
			$configuration['conn'] = $configs;
		}
		//sendo um array irei juntas as configurações com as que tenho
		else if ( is_array($configs) ) {
			$configuration = array_merge($configuration, $configs);
		}
		
		//Tendo configurações extras vou roda-las
		if( isset($other) && is_array($other) ) {
			$configuration = array_merge($configuration, $other);
		}
		
		return $configuration;
		
	}
	
	
	
}