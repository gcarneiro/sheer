<?php

namespace Sh;

/**
 * @author guilherme
 * Compilador do Objeto xml Module Info
 */
abstract class ModuleCompiler {
	
	/**
	 * @param SimpleXMLElement $xmlModule
	 * @return \Sh\Module
	 */
	static public function compile($id, \SimpleXMLElement $xmlModule, $context=null) {
		
		$name 			= (string) $xmlModule->name;
		$description 	= (string) $xmlModule->description;

		if( !is_array($context) || !isset($context['sheer']) || !isset($context['project']) ) {
			$context = array('sheer'=>false, 'project'=>false);
		}
		
		$module = new \Sh\Module($id, $name, $description, $context);
		
		return $module;
		
	}
	
	
}