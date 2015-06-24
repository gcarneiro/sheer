<?php

namespace Sh\Modules\malaDiretaContatoPropriedade;


/**
 * @author Lucas
 * Para utilizar este ADD, deve-se passar o ALIAS da propriedade que deseja inserir e seu valor à adicionar
 * @param 	alias
 * 			valor
 */
class malaDiretaContatoPropriedade_add extends \Sh\GenericAction {
	
	public function doAction($data){
		
		//BUSCANDO A PROPRIEDADE
		$propriedade = \Sh\ContentProviderManager::loadContent('malaDiretaPropriedade/malaDiretaPropriedade_lista', array('alias' => $data['alias']), $this->connection);
		
		//VERIFICANDO SE A PROPRIEDADE EXISTE
		if($propriedade['available'] === 0){
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Propriedade não encontrada!',
			));
		}
		
		$propriedade = reset($propriedade['results']);
		
		//BUSCANDO ESTA PROPRIEDADE NESTE CONTATO
		$contatoPropriedade = \Sh\ContentProviderManager::loadContent('malaDiretaContatoPropriedade/malaDiretaContatoPropriedade_lista', array(
			'idPropriedade' => $propriedade['id'],
		), $this->connection);
		
		//SE NÃO EXISTIR EU CRIO UMA PROPRIEDADE PARA ESTE CONTATO
		if($contatoPropriedade['available'] === 0){
			$contatoPropriedadeAux = array(
				'idPropriedade' => $propriedade['id'],
				'valor' => $data['valor'],
			);
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaContatoPropriedade/malaDiretaContatoPropriedade', 'add', $contatoPropriedadeAux, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
		}
		//SE EXISTIR EU ATUALIZO
		else{
			$contatoPropriedade = reset($contatoPropriedade['results']);
			$contatoPropriedadeAux = array(
				'id' => $contatoPropriedade['id'],
				'valor' => $data['valor'],
			);
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaContatoPropriedade/malaDiretaContatoPropriedade', 'update', $contatoPropriedadeAux, $this->connection);
			\Sh\Library::actionResponseCheck($response);
		}
		
		return $response;
		
	}
	
}