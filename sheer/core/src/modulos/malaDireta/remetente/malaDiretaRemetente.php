<?php
namespace Sh\Modules\malaDiretaRemetente;

/**
 * Função para remover remetente que faz apenas tratamento de erro
 * @author Patrick
 */
class removerRemetente extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		$response = \Sh\ContentActionManager::doAction('malaDiretaRemetente/malaDiretaRemetente_delete', $data, $this->connection);
		
		if(!$response['status']){
			
			throw new \Sh\ActionException(array(
				'code'=> null,
				'message'=>'Este remetente está em uso e não pode ser removido'
			));
		}
		
		return array(
			'status' => true,
			'code' => null,
			'data' => null,
		);
	}
}