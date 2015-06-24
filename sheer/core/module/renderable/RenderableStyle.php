<?php

namespace Sh;

class RenderableStyle {
	
	protected $id;
	protected $path;
	protected $default = false;
	
	public function __construct($id, $path, $default = false) {
		$this->id = $id;
		$this->path = (string) $path;
		$this->default = (boolean) $default;
	}
	
	public function getId() { return $this->id; }
	public function getPath() { return $this->path; }
	
	/**
	 * Determina se o estilo é considerado um estilo padrão.
	 * @return boolean
	 */
	public function getDefault() { return $this->default; }
	
}