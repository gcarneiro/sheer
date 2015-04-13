<?php
namespace Sh\Modules\malaDiretaListaEmail;

class malaDiretaListaEmail {
	
	static public $enviar = array(
		1=>'Sim',
		2=>'Não',
	);
}


/**
 * ACTIONHANDLER para cadastrar um novo e-mail na lista e atualizar o total de e-mails e total habilitados da lista 
 * @author Patrick
 */
class adicionarEmail extends \Sh\GenericAction {
	
	public function doAction($data){
		
		//vou adicionar o novo email
		$responseAdicionar = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_add', $data, $this->connection);
		\Sh\Library::actionResponseCheck($responseAdicionar);
		
		//carrego a lista para somar a nova quantidade de total e habilitados
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['idLista']);
		++$lista['totalEmails'];
		++$lista['totalHabilitados'];
		unset($lista['criadoEm']);
		
		//atualizando a lista
		$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $lista, $this->connection);
		\Sh\Library::actionResponseCheck($responseLista);
		
		return array(
			'status'=>true,
			'code'=>null,
			'data' => array('adicionar'=>$responseAdicionar, 'lista'=>$responseLista)
		);
	}
}

/**
 * ACTIONHANDLER para editar um e-mail. Este apenas atualiza o total de habilitados da lista
 * @author Patrick
 */
class editarEmail extends \Sh\GenericAction {
	
	public function doAction($data){
		
		//atualizo o email sem fazer nenhuma alteração
		$responseEmail = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_update', $data, $this->connection);
		\Sh\Library::actionResponseCheck($responseEmail);
		
		//carrego a lista para atualizar o contador de totalhabilitados
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['idLista']);
		if($data['enviar'] == 1){
			++$lista['totalHabilitados'];
		}
		else{
			--$lista['totalHabilitados'];
		}
		unset($lista['criadoEm']);
		
		//atualizo a lista 
		$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $lista, $this->connection);
		\Sh\Library::actionResponseCheck($responseLista);
		
		return array(
			'status'=>true,
			'code'=>null,
			'data'=> array('email'=>$responseEmail['data'], 'lista'=>$responseLista['data'])	
		);
		
	}
}

/**
 * ACTIONHANDLER para remover um e-mail da lista e atualizar os contadores da lista
 * @author Patrick
 */
class removerEmail extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//carrego o item que vou remover para verificar se ele está ou não habilitado
		$email = \Sh\ContentProviderManager::loadContentById('malaDiretaListaEmail/malaDiretaListaEmail', $data['id']);
		
		//removo o email da lista
		$responseEmail = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_delete', $data, $this->connection);
		\Sh\Library::actionResponseCheck($responseEmail);
		
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $email['idLista']);

		//removo um do total de e-mails
		--$lista['totalEmails'];
		
		//envio habilitado eu removo 1 do total de habilitados da lista de email
		if($email['enviar']==1){
			--$lista['totalHabilitados'];
		}
		
		unset($lista['criadoEm']);
		
		//atualizo a lista
		$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $lista, $this->connection);
		\Sh\Library::actionResponseCheck($responseLista);
		
		return array(
			'status' => true,
			'code' => null,
			'data'=> array('deleteEmail' => $responseEmail['data'], 'updateLista' => $responseLista['data'])
		);
		
	}
}

class habilitarDesabilitarEmail extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//estou recebendo pelo action multiplo e então posso ter mais de 1 item
		if(isset($data['shId']) && $data['shId']){
			
			$response = array(
				'status'=>true,
				'data'=>null
			);
			
			foreach ($data['shId'] as $id){
				
				if($data['enviar']==1){
					
					//verifico se já está habilitado antes de habilitar
					$email = \Sh\ContentProviderManager::loadContentById('malaDiretaListaEmail/malaDiretaListaEmail', $id);
					if($email['enviar']==1){
						continue;
					}
					
					//monto array para habilitar
					$habilitarEmail = array(
						'id' => $id,
						'enviar' => 1
					);
					
					//carrego a lista para adicionar mais 1 ao total de habilitados
					$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['idLista']);
					
					//habilito o e-mail
					$responseEmail = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_update', $habilitarEmail, $this->connection);
					\Sh\Library::actionResponseCheck($responseEmail);
					$response['data']['email'][] = $responseEmail['data'];
					
					//atualizo a lista com mais 1 item habilitado
					++$lista['totalHabilitados'];
					unset($lista['criadoEm']);
					
					$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $lista, $this->connection);
					\Sh\Library::actionResponseCheck($responseLista);
				}
				else{
					
					//verifico se já está desabilitado antes de habilitar
					$email = \Sh\ContentProviderManager::loadContentById('malaDiretaListaEmail/malaDiretaListaEmail', $id);
					if($email['enviar']==2){
						continue;
					}
					
					//monto array para desabilitar
					$desabilitarEmail = array(
						'id' => $id,
						'enviar' => 2
					);
					
					//carrego a lista para poder remover 1 do total de habilitado
					$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['idLista']);
					
					//desabilito o e-mail
					$responseEmail = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_update', $desabilitarEmail, $this->connection);
					\Sh\Library::actionResponseCheck($responseEmail);
					$response['data']['email'][] = $responseEmail['data'];
					
					//atualizo a lista com menos 1 habilitado
					--$lista['totalHabilitados'];
					unset($lista['criadoEm']);
					
					$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $lista, $this->connection);
					\Sh\Library::actionResponseCheck($responseLista);
					$response['data']['lista'] = $responseLista['data'];
					
				}
			}
			return array(
				'status' => true,
				'code' => null,
				'data' => $response['data']
			);
		}	
		//recebi apenas um item
		else{
			
			$email = \Sh\ContentProviderManager::loadContentById('malaDiretaListaEmail/malaDiretaListaEmail', $data['id']);
			$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $email['idLista']);
			
			if($data['enviar']==1){
				++$lista['totalHabilitados'];
			}
			else{
				--$lista['totalHabilitados'];
			}
			
			unset($data['idLista']);
			$response = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_update', $data, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			$response['data']['email'][] = $response['data'];
			
			unset($lista['criadoEm']);
			$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $lista, $this->connection);
			\Sh\Library::actionResponseCheck($responseLista);
			$response['data']['lista'] = $responseLista['data'];
			
			
			
			return array(
				'status' => true,
				'code' => null,
				'data' => $response['data']
			);
		}
	}
}