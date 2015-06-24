<?php

namespace Sh;

class Module {
	
	public $id;
	public $name;
	public $description;
	public $context = array('sheer'=>false, 'project'=>false);
	
	public $dataSources = array();
	
	public $dataProviders = array();

	public $renderables = array();

	public $actionHandlers = array();
	
	public $jobs = array();
	
	public function __construct ( $id, $name, $description, $context ) {
		
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->context = $context;
		
	}
	
	/**
	 * Recupera um dataSource do módulo pelo seu id
	 * @param string $idDataSource
	 * @param boolean $clone determina se o dataSource deverá ser clonado ou retornado a referencia
	 * @return \Sh\DataSource
	 */
	public function getDataSource ($idDataSource, $clone=true) {
		
		$dataSource = null;
		if( isset($this->dataSources[$idDataSource]) ) {
			if($clone) {
				$dataSource = clone $this->dataSources[$idDataSource];
			}
			else {
				$dataSource = $this->dataSources[$idDataSource];
			}
			
		}
		return $dataSource;
		
	}
	
	/**
	 * Recupera a lista de todos os dataSources do módulo
	 * @param boolean $clone determina se o dataSource deverá ser clonado ou retornado a referencia
	 * @return multitype:unknown 
	 */
	public function getDataSources ($clone=true) {
		$dataSources = array();
		foreach ($this->dataSources as $idDs=>$ds) {
			if($clone) {
				$dataSources[$idDs] = clone $ds;
			}
			else {
				$dataSources[$idDs] = $ds;
			}
		}
		return $dataSources;
	}
	
	/**
	 * Recupera um dataProvider do módulo pelo seu id
	 * @param string $idDataProvider
	 * @return \Sh\DataProvider
	 */
	public function getDataProvider ($idDataProvider) {
		
		$dataProvider = null;
		if ( isset($this->dataProviders[$idDataProvider]) ) {
			$dataProvider = clone $this->dataProviders[$idDataProvider];
		}
		return $dataProvider;
	}
	
	/**
	 * Recupera um Renderable do módulo pelo seu id
	 * @param string $idRenderable
	 * @return \Sh\Renderable
	 */
	public function getRenderable ($idRenderable) {
	
		$renderable = null;
		if ( isset($this->renderables[$idRenderable]) ) {
			$renderable = clone $this->renderables[$idRenderable];
		}
		return $renderable;
	}
	
	/**
	 * Recupera um ActionHandler do módulo pelo seu id
	 * @param string $idActionHandler
	 * @return \Sh\ActionHandler
	 */
	public function getActionHandler ($idActionHandler) {
	
		$actionHandler = null;
		if ( isset($this->actionHandlers[$idActionHandler]) ) {
			$actionHandler = clone $this->actionHandlers[$idActionHandler];
		}
		return $actionHandler;
	}
	
	/**
	 * @return array( id=>array(id, excludeFromCron) )
	 */
	public function getJobs () {
		return $this->jobs;
	}
	
	/**
	 * Método para inclusão de um DataSource ao módulo
	 * @param \Sh\DataSource $dataSource
	 */
	public function pushDataSource (\Sh\DataSource $dataSource) {
		
		if( isset($this->dataSources[$dataSource->getId()]) ) {
			\Sh\LoggerProvider::log('warning', 'DataSource já adicionado ao Módulo. Sobrescrevendo anterior. [Module: "'.$this->id.'", DS:"'.$dataSource->getId().'"]');
		}
		
		$this->dataSources[$dataSource->getId()] = $dataSource;
		
	}
	
	/**
	 * Método para inclusão de um DataProvider ao módulo
	 * @param \Sh\DataProvider $dataProvider
	 */
	public function pushDataProvider (\Sh\DataProvider $dataProvider) {
	
		if( isset($this->dataProviders[$dataProvider->getId()]) ) {
			\Sh\LoggerProvider::log('warning', 'DataProvider já adicionado ao Módulo. Sobrescrevendo anterior. [Module: "'.$this->id.'", DP:"'.$dataProvider->getId().'"]');
		}
	
		$this->dataProviders[$dataProvider->getId()] = $dataProvider;
	
	}
	
	/**
	 * Método para inclusão de um Renderable ao módulo
	 * @param \Sh\Renderable $renderable
	 */
	public function pushRenderable (\Sh\Renderable $renderable) {
	
		if( isset($this->renderables[$renderable->getId()]) ) {
			\Sh\LoggerProvider::log('warning', 'Renderable já adicionado ao Módulo. Sobrescrevendo anterior. [Module: "'.$this->id.'", Renderable:"'.$renderable->getId().'"]');
		}
	
		$this->renderables[$renderable->getId()] = $renderable;
	
	}
	
	public function pushActionHandler (\Sh\ActionHandler $actionHandler) {
	
		if( isset($this->actionHandlers[$actionHandler->getId()]) ) {
			\Sh\LoggerProvider::log('warning', 'ActionHandler já adicionado ao Módulo. Sobrescrevendo anterior. [Module: "'.$this->id.'", Renderable:"'.$actionHandler->getId().'"]');
		}
	
		$this->actionHandlers[$actionHandler->getId()] = $actionHandler;
	
	}
	
	/**
	 * @param array $job [id, excludeFromCron]
	 */
	public function pushJob( $job ) {
		$this->jobs[$job['id']] = $job;
	}
	
	public function __clone() {
		
		//EFETUANDO O CLONE DOS DATASOURCES
		$ds = array();
		if( $this->dataSources ) {
			foreach ($this->dataSources as $k=>$v) {
				$ds[$k] = clone $v;
			}
		}
		
		//EFETUANDO O CLONE DOS DATAPROVIDERS
		$dp = array();
		if( $this->dataProviders ) {
			foreach ($this->dataProviders as $k=>$v) {
				$dp[$k] = clone $v;
			}
		}
		
		//EFETUANDO O CLONE DOS RENDERABLES
		$rn = array();
		if( $this->renderables ) {
			foreach ($this->renderables as $k=>$v) {
				$rn[$k] = clone $v;
			}
		}
		
		//EFETUANDO O CLONE DOS ACTIONHANDLERS
		$ah = array();
		if( $this->actionHandlers ) {
			foreach ($this->actionHandlers as $k=>$v) {
				$ah[$k] = clone $v;
			}
		}
		
		$this->dataSources = $ds;
		$this->dataProviders = $dp;
		$this->renderables = $rn;
		$this->actionHandlers = $ah;
		
	}
	
}