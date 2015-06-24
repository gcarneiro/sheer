<?php

namespace Sh\Modules\modulo;

class listarModulosRegistrados extends \Sh\GenericContentProvider {
	
	public function getData ( $filters=array(), $configs=array() ) {
		
		$listaModulos = array();
		
		$tmp = \Sh\ModuleControl::getAvailableModules();
		foreach ( $tmp as $idModule=>$module ) {
			
			//carregando o módulo e extraindo informações
			$modulo = \Sh\ModuleFactory::getModuleFull($idModule);

			$listaModulos[$idModule] = $modulo;
			
		}
		
		//organizando por nome dos módulos
		uasort($listaModulos, function (\Sh\Module $a, \Sh\Module $b) {
			return strcasecmp($a->id, $b->id);
		});
		
		return $listaModulos;
		
	}
	
}

