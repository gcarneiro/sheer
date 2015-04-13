<?php
namespace Sh\Modules\clienteSenha;


/**
 * Função para atualizar ou cadastrar senha para cliente
 * @author bgstudiosAsus
 *
 */
class adicionarAtualizarSenha extends \Sh\GenericAction{
	public function doAction($data){
		//VERIFICANDO SE VOU ATUALIZAR OU CADASTRAR UMA NOVA SENHA
		
		//se eu tiver cliente vou adicionar um novo registro.
		if(isset($data['idCliente']) && $data['idCliente']!=null){
			$response = \Sh\ContentActionManager::doAction('clienteSenha/clienteSenha_add', $data, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
			return array (
				'status' => true,
				'code' => null,
				'data' => $response['data']	
			);
		}
		//se eu não tenho cliente eu vou cadastrar um novo registro
		else{
			$response = \Sh\ContentActionManager::doAction('clienteSenha/clienteSenha_update', $data, $this->connection);
			\Sh\Library::actionResponseCheck($response);
				
			return array (
					'status' => true,
					'code' => null,
					'data' => $response['data']
			);
		}
	}
}