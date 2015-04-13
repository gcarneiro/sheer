<?php

namespace Sh;

abstract class ModuleFactory {
	
	static $initialized = false;
	
	/**
	 * Variavel que guarda os módulos já compilados para reuso
	 * @var array
	 */
	static protected $compiledModules = array();
	
	static protected function init () {
		
		if( self::$initialized ) {
			return true;
		}
		
		self::$initialized = true;
	}
	
	/**
	 * Método para determinar se o módulo é realmente um módulo
	 * @param string $idModule
	 * @return boolean
	 */
	static public function isModule ($idModule) {
		
		return \Sh\ModuleControl::isModule($idModule);
		
	}
	
	/**
	 * Método para recuperar o objeto de um módulo pelo seu id
	 * @param string $idModule
	 * @throws \Sh\SheerException
	 * @return \Sh\Module
	 */
	static public function getModuleFull($idModule) {
		
		try {
			self::init();
			
			//ANTES DE COMPILAR O MÓDULO PRECISO VERIFICAR SE ELE JÁ NÃO ESTÁ EM CACHE
			if( isset(self::$compiledModules[$idModule]) ) {
				return clone self::$compiledModules[$idModule];
			}
			
			$possuiDataSources = false;
			
			//CARREGO O OBJETO DO MODULO
			$moduleConfig = \Sh\ModuleControl::getModuleConfig($idModule);
			$module = \Sh\ModuleCompiler::compile($moduleConfig['id'], $moduleConfig['config']['info'], $moduleConfig['context']);
			
			//CARREGO OS DATASOURCES
			{
				if( isset( $moduleConfig['config']['datasources'] ) && is_array($moduleConfig['config']['datasources']) ) {
					$possuiDataSources = true;
					foreach ( $moduleConfig['config']['datasources'] as $xmlDataSource ) {
						$dataSource = \Sh\DataSourceCompiler::compile($xmlDataSource, $module);
						$module->pushDataSource($dataSource);
					}
				}
				
			}
			
			//CARREGANDO OS DATAPROVIDERS
			{
				if( isset( $moduleConfig['config']['dataProviders'] ) && is_array($moduleConfig['config']['dataProviders']) ) {
					//iterando e processando cada dataProvider
					foreach( $moduleConfig['config']['dataProviders'] as $xmlDataProvider ) {
						$dataProvider = \Sh\DataProviderCompiler::compile($xmlDataProvider, $module);
						$module->pushDataProvider($dataProvider);
					}
				}
				
				//GERANDO OS DATAPROVIDERS PADRAO CASO HAJAM DATASOURCES
				if( $possuiDataSources ) {
					$dataSources = $module->getDataSources(false);
					foreach ($dataSources as $idDataSource=>$dataSource) {
						
						$pk = $dataSource->getPrimaryKey(false);
						$pn = $dataSource->getPrimaryName(false);
						
						//LISTA SIMPLES
						$idDp = $idDataSource.'_listaSimples';
						if( !isset($module->dataProviders[$idDp]) ) {
							$dpListaSimples = new \Sh\DataProvider($idDataSource.'_listaSimples', clone $dataSource);
							$dpListaSimples->setMaxRows(0);
							$module->pushDataProvider($dpListaSimples);
						}
	
						//LISTA
						$idDp = $idDataSource.'_lista';
						if( !isset($module->dataProviders[$idDp]) ) {
							$dpLista = new \Sh\DataProvider($idDataSource.'_lista', clone $dataSource);
							$filtroPrimaryName = array(
									'id'			=> $pn->getId(),
									'relationPath'	=> '/',
									'relationIndex' => 0,
									'field'			=> $pn->getId(),
									'operator'		=> 'likeSplit',
									'dateFunction'	=> null,
									'defaultValue'	=> null,
									'parameter' 	=> $pn->getId(),
									'required' 		=> false,
							);
							$dpLista->pushFilter($filtroPrimaryName);
							$module->pushDataProvider($dpLista);
						}
						
						
						//DETALHES
						$idDp = $idDataSource.'_detalhes';
						if( !isset($module->dataProviders[$idDp]) ) {
							$dpDetalhes = new \Sh\DataProvider($idDataSource.'_detalhes', clone $dataSource);
							$filtroPrimaryKey = array(
									'id'			=> $pk->getId(),
									'relationPath'	=> '/',
									'relationIndex' => 0,
									'field'			=> $pk->getId(),
									'operator'		=> 'equal',
									'dateFunction'	=> null,
									'defaultValue'	=> null,
									'parameter' 	=> $pk->getId(),
									'required' 		=> true,
							);
							$dpDetalhes->pushFilter($filtroPrimaryKey);
							$module->pushDataProvider($dpDetalhes);
						}
						
					}
				}
			}
			
			//CARREGANDO OS RENDERABLES
			{
				if( isset( $moduleConfig['config']['renderables'] ) && is_array($moduleConfig['config']['renderables']) ) {
					foreach ( $moduleConfig['config']['renderables'] as $xmlRenderable ) {
						$renderable = \Sh\RenderableCompiler::compile($xmlRenderable, $module);
						$module->pushRenderable($renderable);
					}
				}
			}
			
			//CARREGANDO OS ACTIONHANDLERS
			{
				if( isset( $moduleConfig['config']['actionHandlers'] ) && is_array($moduleConfig['config']['actionHandlers']) ) {
					foreach ( $moduleConfig['config']['actionHandlers'] as $xmlActionHandler ) {
						$actionHandler = \Sh\ActionHandlerCompiler::compile($xmlActionHandler, $module);
						$module->pushActionHandler($actionHandler);
					}
				}
				
				//GERANDO ACTIONHANDLERS PADRÕES CASO HAJAM DATASOURCES
				if( $possuiDataSources ) {
					$dataSources = $module->getDataSources(false);
					foreach ($dataSources as $idDataSource=>$dataSource) {
						
						//ADD
						$idAdd = $dataSource->getId().'_add';
						if( !isset($module->actionHandlers[$idAdd]) ) {  
							$addActionHandler = \Sh\ActionHandlerCompiler::getDefaultAddActionHandlerFromDataSource(clone $dataSource, $module);
							$module->pushActionHandler($addActionHandler);
						}
						
						//UPDATE
						$idUpdate = $dataSource->getId().'_update';
						if( !isset($module->actionHandlers[$idUpdate]) ) {
							$updateActionHandler = \Sh\ActionHandlerCompiler::getDefaultUpdateActionHandlerFromDataSource(clone $dataSource, $module);
							$module->pushActionHandler($updateActionHandler);
						}
						
						//DELETE
						$idDelete = $dataSource->getId().'_delete';
						if( !isset($module->actionHandlers[$idDelete]) ) {
							$deleteActionHandler = \Sh\ActionHandlerCompiler::getDefaultDeleteActionHandlerFromDataSource(clone $dataSource, $module);
							$module->pushActionHandler($deleteActionHandler);
						}
						
					}
				}
			}
			
			//PROCESSANDO JOBS
			{
				if( isset( $moduleConfig['config']['jobs'] ) && is_array($moduleConfig['config']['jobs']) ) {
					foreach ( $moduleConfig['config']['jobs'] as $job ) {
						$module->pushJob($job);
					}
				}
			}
			
			//Salvando o módulo em cache
			self::$compiledModules[$idModule] = clone $module;
			
		}
		catch (\Sh\SheerException $e) {
			throw $e;
		}
		
		return $module;
		
	}
	
	/**
	 * Método responsável por obter um DataSource específico a partir do id do módulo e do id do DataSource
	 * 
	 * @param string $idModule
	 * @param string $idDataSource
	 * @throws \Sh\SheerException
	 * @return \Sh\DataSource
	 */
	static public function getModuleDataSource ($idModule, $idDataSource) {
		
		$dataSource = null;
		//VERIFICO SE POSSUO O MÓDULO COMPILADO
		if( !isset(self::$compiledModules[$idModule]) ) {
			//CASO NÃO POSSUA O MÓDULO PREVIAMENTE COMPILADO IREI COMPILAR APENAS O DATASOURCE
			//carrego configurações do módulo
			$moduleConfig = \Sh\ModuleControl::getModuleConfig($idModule);
			//verifico a existencia do módulo
			if( !$moduleConfig ) {
				throw new \Sh\FatalErrorException(array(
					'message'=>'Módulo "'.$idModule.'" não está registrado',
					'code' => 'SMF_XXXX'
				));
			}
			$module = \Sh\ModuleCompiler::compile($moduleConfig['id'], $moduleConfig['config']['info'], $moduleConfig['context']);
				
			//verificando a existência de DataSources
			if( !isset( $moduleConfig['config']['datasources'] ) || !is_array($moduleConfig['config']['datasources']) ) {
				throw new \Sh\SheerException(array(
					'message'=>'DataSources não definidos para Módulo',
					'code' => 'SMF_XXXX'
				));
			}
			//Buscando o DataSource referenciado
			$xmlDataSource = $moduleConfig['config']['datasources'][$idDataSource];
			$dataSource = \Sh\DataSourceCompiler::compile($xmlDataSource, $module);
			
		}
		else {
			//CASO O MODULO JÁ TENHA SIDO COMPILADO
			//Capturo o módulo
			$module = self::$compiledModules[$idModule];
			//Capturo o DataSource
			$dataSource = $module->getDataSource($idDataSource);
		}
		
		if( !$dataSource ) {
			throw new \Sh\SheerException(array(
				'message'=>'DataSource "'.$idDataSource.'" não definido para o Módulo',
				'code' => 'SMF_XXXX'
			));
		}
	
		return $dataSource;
	}
	
	/**
	 * Método responsável por obter um DataSource específico a partir seu alias idModule/idDataSource
	 * 
	 * @param string $idDataSource idModule/idDataSource
	 * @return \Sh\DataSource
	 */
	static public function getModuleDataSourceByAlias( $idDataSource ) {
		list($idModule, $idDataSource) = explode('/', $idDataSource);
		return self::getModuleDataSource($idModule, $idDataSource);
	}
	
	/**
	 * Método responsável por obter um DataProvider específico a partir do id do módulo e do id do DataSource
	 * @param string $idModule
	 * @param string $idDataProvider
	 * @throws \Sh\SheerException
	 * @return \Sh\DataProvider
	 */
	static public function getModuleDataProvider ($idModule, $idDataProvider) {
	
		$dataProvider = null;
		//VERIFICO SE POSSUO O MÓDULO COMPILADO
		if( !isset(self::$compiledModules[$idModule]) ) {
			//CASO NÃO POSSUA O MÓDULO PREVIAMENTE COMPILADO IREI COMPILA-LO
			$module = self::getModuleFull($idModule);
		}
		else {
			//CASO O MODULO JÁ TENHA SIDO COMPILADO EU O CAPTURO
			$module = self::$compiledModules[$idModule];
		}
		
		if( !$module ) {
			throw new \Sh\SheerException(array(
				'message'=>'Erro ao tentar obter Módulo "'.$idModule.'" para DataProvider "'.$idDataProvider.'".',
				'code' => 'SMF_XXXX'
			));
		}
		
		//capturando o DataProvider
		$dataProvider = $module->getDataProvider($idDataProvider);
		if( !$dataProvider ) {
			throw new \Sh\SheerException(array(
					'message'=>'DataProvider "'.$idDataProvider.'" não definido para o Módulo',
					'code' => 'SMF_XXXX'
			));
		}
	
		return $dataProvider;
	}
	
	/**
	 * Método para recuperar todos os jobs do modulo
	 * @throws \Sh\SheerException
	 * @return array()
	 */
	static public function getModuleJobs ($idModule) {

		//VERIFICO SE POSSUO O MÓDULO COMPILADO
		if( !isset(self::$compiledModules[$idModule]) ) {
			//CASO NÃO POSSUA O MÓDULO PREVIAMENTE COMPILADO IREI COMPILA-LO
			$module = self::getModuleFull($idModule);
		}
		else {
			//CASO O MODULO JÁ TENHA SIDO COMPILADO EU O CAPTURO
			$module = self::$compiledModules[$idModule];
		}
		
		if( !$module ) {
			throw new \Sh\SheerException(array(
				'message'=>'Erro ao tentar obter Módulo "'.$idModule.'" para DataProvider "'.$idDataProvider.'".',
				'code' => 'SMF_XXXX'
			));
		}
		
		return $module->getJobs();
	}
	
	
	
}