<?php
namespace Sh\Modules\malaDiretaListaEmail;

class malaDiretaListaEmail {
	
	static public $enviar = array(
		1=>'Sim',
		2=>'Não',
	);
}


/**
 * @author Guilherme
 * 
 * ACTIONHANDLER para cadastrar um novo e-mail em uma lista
 * 		Este irá criar o contato
 * 		Registrar o contato em uma lista
 * 		Atualizar a lista com informações de quantidade
 */
class adicionarEmail extends \Sh\GenericAction {
	
	public function doAction($data){
		
		//ADICIONAR O NOVO EMAIL PARA OS CONTATOS
		$responseContato = \Sh\ContentActionManager::doAction('malaDiretaContato/malaDiretaContato_add', $data, $this->connection);
		\Sh\Library::actionResponseCheck($responseContato);
		
		//RELACIONANDO A LISTA COM O EMAIL QUE ACABOU DE SER CADASTRADO
		$malaDiretaListaEmail = array(
			'idLista' => $data['idLista'],
			'idContato' => $responseContato['data']['id'],
		);
		$responseAdicionar = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_add', $malaDiretaListaEmail, $this->connection);
		\Sh\Library::actionResponseCheck($responseAdicionar);
		
		//ATUALIZANDO INFORMAÇÕES DE CONTAGEM DA LISTA
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
 * @author Guilherme
 * 
 * Método para vincular um contato a partir do seu id
 *
 */
class adicionarContato extends \Sh\GenericAction {
	
	
	public function doAction($data) {
		
		$contato = null;
		$operacaoRealizada = null;
		
		//VERIFICAR SE O LINK ENTRE O CONTATO E A LISTA JÁ ESTÁ PREENCHIDO
		$link = \Sh\ContentProviderManager::loadContent('malaDiretaListaEmail/malaDiretaListaEmail_lista', $data, null, $this->connection);
		//Se não encontrou devo cadastrar
		if( $link['total'] == 0 ) {
			
			//Criando objeto de contato
			$contato = array(
				'idLista' => $data['idLista'],
				'idContato' => $data['idContato'],
				'enviar' => 1,
				'adicionadoEm' => date('d/m/Y H:i:s')
			);
			$response = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_add', $contato, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
			//marcando que foi adicionado
			$response['data']['operacao'] = 'adicionado';
		}
		//Se encontrou devo atualizar marcando para receber os emails
		else {
			$contato = reset($link['results']);
			$contatoAtualizar = array();
			$contatoAtualizar['id'] 		= $contato['id'];
			$contatoAtualizar['enviar'] 	= 1;
			
			$response = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_update', $contatoAtualizar, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
			//marcando que foi atualizado
			$response['data']['operacao'] = 'atualizado';
		}
		
		return $response;
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
		
		//Determinando emails que devemos desabilitar/habilitar
		$idEmails = array();
		if( isset($data['shId']) && $data['shId'] ) {
			$idEmails = $data['shId'];
		}
		if( isset($data['id']) && $data['id'] ) {
			$idEmails[] = $data['id'];
		}
		
		//REALIZANDO A OPERACAO PARA CADA EMAIL
		foreach ( $idEmails as $id ){
			
			//verifico se já está habilitado antes de habilitar
			$email = \Sh\ContentProviderManager::loadContentById('malaDiretaListaEmail/malaDiretaListaEmail', $id);
			if( !$email || $email['enviar']==$data['enviar'] ){
				continue;
			}
			
			//monto array para habilitar
			$habilitarEmail = array(
				'id' => $id,
				'enviar' => $data['enviar']
			);
			
			//carrego a lista para adicionar mais 1 ao total de habilitados
			$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $email['idLista']);
			
			//troco a habilitacao do e-mail
			$responseEmail = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_update', $habilitarEmail, $this->connection);
			\Sh\Library::actionResponseCheck($responseEmail);
			$response['data']['email'][] = $responseEmail['data'];
			
			//recalculo os valores de total da lista
			if( $data['enviar']==1 ) {
				++$lista['totalHabilitados'];
			}
			else {
				--$lista['totalHabilitados'];
			}
			$listaAtualizar = array(
				'id' => $lista['id'],
				'totalHabilitados' => $lista['totalHabilitados']
			);
			
			$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $listaAtualizar, $this->connection);
			\Sh\Library::actionResponseCheck($responseLista);
		}
		
		//RETORNANDO PARA FINALIZAR
		return array(
			'status' => true,
			'code' => null,
			'data' => $response['data']
		);
	}
}