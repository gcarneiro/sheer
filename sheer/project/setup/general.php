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

/*
 * Registrando conversor padrão de url para parametro "p" especializado para o campestre
 *
 * Qualquer Url buscada iremos trocar a página para a index. Pois este projeto é de uma página só
 */
\Sh\PageRequest::registerConverter(function ($url) {

	//Só devo converter a página se formos usuários do tipo registrado ou visitante do noc
	$profile = \Sh\AuthenticationControl::getAuthenticatedUserInfo('profile');
	if( !$profile || in_array($profile['alias'], ['noc.guest', 'noc.registrado']) ) {

		$page = 'index';

		//Verificando contra expressão regular de evento
		$matchesAlbumFoto = preg_match('/album\/([\w\_\-]+)\/foto/', $url);
		$matches = preg_match('/evento\/([\w\_\-]+)/', $url);
		$matchesAlbum = preg_match('/album\/([\w\_\-]+)/', $url);

		if( $matchesAlbumFoto && count($matchesAlbumFoto) ) {
			$permaLink = explode('/',$url);
			$idFoto = $permaLink[3];
			$permaLink = $permaLink[1];
				
			$evento = \Sh\ContentProviderManager::loadContent('evento/evento_lista',array('permalink'=>$permaLink));
				
			if($evento['total'] == 1){
				$evento = reset($evento['results']);
				\Sh\PageRuntimeConfiguration::setTitle($evento['nome'].' - ABLounge');
				$_GET['id'] = $evento['id'];
				$_GET['idFoto'] = $idFoto;
				return 'album/foto';
			}
			else{
				return null;
			}
		}
		else if( $matches && count($matches) ) {
			$permaLink = explode('/',$url);
			$permaLink = $permaLink[1];
				
			$evento = \Sh\ContentProviderManager::loadContent('evento/evento_lista',array('permalink'=>$permaLink));
				
			if($evento['total'] == 1){
				$evento = reset($evento['results']);
				\Sh\PageRuntimeConfiguration::setTitle($evento['nome'].' - ABLounge');
				$_GET['id'] = $evento['id'];
				return 'evento/detalhes';
			}
			else{
				return null;
			}
				
		}
		else if ($matchesAlbum && count($matchesAlbum)){
			$permaLink = explode('/',$url);
			$permaLink = $permaLink[1];
				
			$evento = \Sh\ContentProviderManager::loadContent('evento/evento_lista',array('permalink'=>$permaLink));
				
			if($evento['total'] == 1){
				$evento = reset($evento['results']);
				\Sh\PageRuntimeConfiguration::setTitle('Album do evento: '.$evento['nome'].' - ABLounge');
				$_GET['id'] = $evento['id'];
				return 'album/integra';
			}
			else{
				return null;
			}
		}
		else{
			return null;
		}
	}
	return null;

});

