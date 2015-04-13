<?php

namespace Sh;

/**
 * 
 * @author Guilherme
 *
 * Classe responsável por resolver o path onde buscar arquivos.
 * Sejam eles
 * 		- Páginas		TODO
 * 		- Templates		TODO
 * 		- Estilos		TODO
 *
 */
abstract class PathResolver {
	
	static public function resolveTemplate ( $templateFile, $userRenderPath=null ) {
		
		//DETERMINANDO RENDERPATH CASO NÃO ENVIADO
		if ( $userRenderPath == null ) {
			$userRenderPath = \Sh\AuthenticationControl::getAuthenticatedUserInfo('renderPath');
		}
		
		//RESOLVO O PATH DE FORMA GENERICA
		$resolvedPath = self::findPathFromGenericFile($userRenderPath, $templateFile, function ($context, $file) {
				
			$path = null;
				
			switch( $context ) {
				case 'project':
					$path = SH_PROJECT_TEMPLATES_PATH.'/'.$file;
					break;
				case 'sheer':
					$path = SH_TEMPLATES_PATH.'/'.$file;
					break;
			}
			
			return $path;
				
		});
		
		
		return $resolvedPath;
	}
	
	/**
	 * Método para buscar o path de um arquivo de Página (Navigation)
	 * 
	 * @param string $pageFile
	 * @param string $userRenderPath
	 * @return Ambigous <NULL, string>
	 */
	static public function resolvePage ($pageFile, $userRenderPath=null) {
		
		//DETERMINANDO RENDERPATH CASO NÃO ENVIADO
		if ( $userRenderPath == null ) {
			$userRenderPath = \Sh\AuthenticationControl::getAuthenticatedUserInfo('renderPath');
		}
		
		//RESOLVO O PATH DE FORMA GENERICA
		$resolvedPath = self::findPathFromGenericFile($userRenderPath, $pageFile, function ($context, $file) {
			
			$path = null;
			
			switch( $context ) {
				case 'project':
					$path = SH_PROJECT_NAVIGATION_PATH.'/'.$file.'.json';
					break;
				case 'sheer':
					$path = SH_NAVIGATION_PATH.'/'.$file.'.json';
					break;
			}
			
			return $path;
			
		});
		
		//RETORNO O PATH ENCONTRADO
		return $resolvedPath;
		
	}
	
	/**
	 * Este método vai receber a string de renderPath (Pode conter os renderPaths separados por ;), o nome do arquivo que deseja carregar e uma função para resolver o fullPath dependendo do contexto [project, sheer]
	 * Ele irá retornar um único path (completo) onde será possível encontrar o arquivo em procurado ou nulo se esse arquivo não existir
	 * 
	 * Sobre a closure
	 * 		Esta closure deve ser uma função que vai receber 2 parametros [$context, $filePath] onde o contexto é "project" ou "sheer" e o $filePath é o [renderPath + fileName]
	 * 		Ela deverá retornar o fullPath para esta ocorrência de busca
	 * 
	 * Criei este método para poder utilizar a mesma lógica de busca em qualquer lugar, só alterando o final (".json" no caso das páginas e nada no caso dos templates) e o inicio que muda conforma sheer/project e tipo de arquivo
	 * 
	 * @param string $renderPath
	 * @param string $filePath
	 * @param \Closure $fnGetFilePath($context, $fileName)
	 * @return Ambigous <NULL, string>
	 */
	static protected function findPathFromGenericFile ($renderPath, $fileName, \Closure $fnGetFilePath) {

		//CONTROLES
		$path = null;
		//este será o array que irá mapear os renderPaths da forma primitiva, recebendo diretamente o caminho após o "navigation", não importa se do "project" ou do "sheer"
		$renderPathsControl = [];
		//este vai mapear as possibilidades de paths finais, a partir do "www", este será consumido em ordem 0->n
		$renderPathsMap = [];
		
		//DETERMINO QUAIS SÃO OS PATHS A SEREM CONSIDERADOS PARA O CONTROLE
		$userRenderPathTmp = explode(';', $renderPath);
		foreach ( $userRenderPathTmp as $string ) {
			//removo paths invalidos
			$string = trim($string);
			if( !$string || strlen($string) < 1 ) {
				continue;
			}
			//insiro no controle de paths
			$renderPathsControl[$string] = true;
		}
		
		//A PARTIR DO CONTROLE, DETERMINO EM ORDEM, TODoS OS PATHS COMPLETOS QUE DEVEM BUSCAR O ARQUIVO DA PÁGINA
		//foreach para gerar o renderPath completo para o projeto
		foreach ($renderPathsControl as $renderPath=>$null) {
			$closurePath = $fnGetFilePath('project', $renderPath.'/'.$fileName);
			$closurePath = str_replace('//', '/', $closurePath);
			$renderPathsMap[] = $closurePath;
		}
		//foreach para gerar o renderPath completo para o sheer
		foreach ($renderPathsControl as $renderPath=>$null) {
			$closurePath = $fnGetFilePath('sheer', $renderPath.'/'.$fileName);
			$closurePath = str_replace('//', '/', $closurePath);
			$renderPathsMap[] = $closurePath;
		}
		//AGORA INSIRO NO MAPA O PATHS DO SHARED
		//project
		$closurePath = $fnGetFilePath('project', 'shared/'.$fileName);
		$closurePath = str_replace('//', '/', $closurePath);
		$renderPathsMap[] = $closurePath;
		//sheer
		$closurePath = $fnGetFilePath('sheer', 'shared/'.$fileName);
		$closurePath = str_replace('//', '/', $closurePath);
		$renderPathsMap[] = $closurePath;
		
		//TENDO AGORA O MAPA DE PATHS COMPLETOS POSSÍVEIS PARA A PÁGINA COMECO A BUSCA
		$path = null;
		foreach ( $renderPathsMap as $filePath ) {
			if( is_file($filePath) ) {
				$path = $filePath;
				break;
			}
		}
		
		return $path;
		
	}
	
}