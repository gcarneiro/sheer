<?php

namespace Sh;

/**
 * @author guilherme
 * Classe responsável pelos Renderables do Sheer
 *
 */
class Renderable {
	
	protected $module;
	protected $id;
	
	/**
	 * Array de mapeamento de dataSources.
	 * 	Mapeio os dataSources dentro da casa dataSources para ser possível
	 * 	a expansão com novas informações em outras chaves de primeiro nível
	 * @var array
	 * 		dataSources => array com todos os dataSources
	 */
	protected $dataSources = array(
		'dataSources' => array()
	);
	/**
	 * Array de mapeamento de dataProviders.
	 * 	Mapeio os dataproviders dentro da casa dataProviders para ser possível 
	 * 	a expansão com novas informações em outras chaves de primeiro nível
	 * @var array
	 * 		dataProviders => array com todos os dataProviders
	 */
	protected $dataProviders = array(
		'dataProviders' => array()
	);
	/**
	 * Array de mapeamento de styles.
	 *
	 * @var array
	 * 		default => id do style default
	 * 		styles => array com todos os estilos
	 */
	protected $styles = array(
		'default' => null,
		'styles' => array()
	);
	
	public function __construct($module, $id) {
		
		$this->module = $module;
		$this->id = $id;
		
	}
	
	/*
	 * PROCESSAMENTO
	 */
	
	public function getDataProviders () {
		
		return $this->dataProviders;
		
	}
	
	/**
	 * Método responsável por capturar o estilo requisitado
	 * @param string $idStyle
	 * @throws \Sh\SheerException
	 * @return \Sh\RenderableStyle
	 */
	public function getStyle( $idStyle=null ) {
		
		$style = null;
		
		//verificando existencia de estilo
		if( !$this->styles['styles'] ) {
			throw new \Sh\SheerException(array(
				'message' => 'Estilo para renderização inexistente',
				'code' => 'SR_XXXX'
			));
		}
		
		//determinando id do estilo, se nao passado ou se nao existente
		if( !$idStyle || !isset($this->styles['styles'][$idStyle]) ) {
			//caso não tenha nenhum setado como default, pego o primeiro recuperado
			if( isset($this->styles['default']) ) {
				$idStyle = $this->styles['default'];
			}
			else {
				reset($this->styles['styles']);
				$idStyle = key($this->styles['styles']);
			}
		}
		
		//buscando estilo
		$style = $this->styles['styles'][$idStyle]; 
		if( !$style ) {
			throw new \Sh\SheerException(array(
				'message' => 'Estilo para renderização inválido',
				'code' => 'SR_XXXX'
			));
		}
		
		return $style;
		
	}
	
	/*
	 * INICIALIZACAO
	 */
	public function pushDataSource( \Sh\RenderableDataSource $dataSource ) {
	
		$this->dataSources['dataSources'][$dataSource->getId()] = $dataSource;
	
	}
	
	public function pushDataProvider( \Sh\RenderableDataProvider $dataProvider ) {
		
		$this->dataProviders['dataProviders'][$dataProvider->getId()] = $dataProvider;
		
	}
	
	public function pushStyle ( \Sh\RenderableStyle $style ) {
		
		$this->styles['styles'][$style->getId()] = $style;
		if( $style->getDefault() ) {
			$this->styles['default'] = $style->getId();
		}
	
	}
	
	public function getId() 		{ return $this->id; }
	public function getModuleId()	{ return $this->module; }
	
}