<?php

namespace Sh\Project;

/**
 * Classe extendido pelo projeto para tratar variaveis runtimes e customizadas
 * Esta classe deve ser obrigatoriamente implementada em todo o projeto e customizada a seu gosto.
 * Esta possui como método obrigatório "static public function getAliasValue"
 */
abstract class RuntimeVariables {
	
	/**
	 * Método para buscar o valor do aliasValue, 
	 * 
	 * @param string $alias
	 * @return undefined 
	 * 		Caso o alias seja um alias e for encontrado retornamos o seu valor
	 * 		Caso o alias não seja um alias iremos retornar false
	 */
	static public function getAliasValue ( $alias ) {
		
		/*
		 * Area para codificação do desenvolvedor
		 */
		
		
		//retorno padrão
		return false;
		
		
		
	}
	
}