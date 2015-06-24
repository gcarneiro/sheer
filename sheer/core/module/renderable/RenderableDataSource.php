<?php

namespace Sh;

class RenderableDataSource {
	
	protected $id;
	
	public function __construct($idDataSource) {
		$this->id = $idDataSource;
	}
	
	public function getId() { return $this->id; }
	
}