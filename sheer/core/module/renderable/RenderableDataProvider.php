<?php

namespace Sh;

class RenderableDataProvider {
	
	protected $id;
	
	protected $filterProcessor;
	protected $dataProcessor;
	protected $sort;
	protected $maxRows;
	
	/**
	 * @param string $idDataProvider
	 * @param string $filterProcessor
	 * @param string $dataProcessor
	 */
	public function __construct($idDataProvider, $filterProcessor=null, $dataProcessor=null, $sort=null, $maxRows=null) {
		$this->id 				= $idDataProvider;
		$this->filterProcessor 	= $filterProcessor;
		$this->dataProcessor 	= $dataProcessor;
		$this->sort 			= $sort;
		$this->maxRows 			= $maxRows;
		
	}
	
	public function getId() { return $this->id; }
	
	public function getFilterProcessor() { return $this->filterProcessor; }

	public function getSort() { return $this->sort; }

	public function getMaxRows() { return $this->maxRows; }
	
}