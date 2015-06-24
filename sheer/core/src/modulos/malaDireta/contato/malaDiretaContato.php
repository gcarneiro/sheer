<?php
namespace Sh\Modules\malaDiretaContato;

/**
 * @author Guilherme
 * 
 * ActionHandler para registrar um novo contato
 * Este irá verificar se o contato já existe e saberá registra-lo/atualiza-lo se necessário
 *
 */
class malaDiretaContato_add extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		$emailCadastrado = null;
		
		//VERIFICAR ID
		if( isset($data['id']) && $data['id'] ) {
			$emailCadastrado = \Sh\ContentProviderManager::loadContentById('malaDiretaContato/malaDiretaContato', $data['id'], $this->connection);
		}
		
		//DEVO DETERMINAR SE JÁ POSSUO O EMAIL CADASTRADO
		if( !$emailCadastrado ) {
			$tmp = \Sh\ContentProviderManager::loadContent('malaDiretaContato/malaDiretaContato_lista', array('email'=>$data['email']), null, $this->connection);
			if( $tmp['total'] == 1 ) {
				$emailCadastrado = reset($tmp['results']);
			}
		}
		
		//Gerando marcador para novo contato
		$emailNovo = array();
		
		//Se não temos o email cadastrado realizo o cadastro
		if( !$emailCadastrado ) {
			//Cadastrando o elemento diretamente
			$responseAdicionar = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaContato/malaDiretaContato', 'add', $data, $this->connection);
			\Sh\Library::actionResponseCheck($responseAdicionar);
			//Guardando dado
			$emailNovo = $responseAdicionar['data'];
			
		}
		//TENDO O EMAIL CADASTRADO DEVO ATUALIZA-LO
		else {
			//Atualizando o elemento anterior
			$emailNovo = $emailCadastrado;
			
			//Verificando se possuimos nome agora
			if( isset($data['nome']) && strlen($data['nome']) ) {
				$emailNovo['nome'] = $data['nome'];
			}
			
			//Atualizando elemento
			$responseAtualizar = \Sh\ContentActionManager::doAction('malaDiretaContato/malaDiretaContato_update', $emailNovo, $this->connection);
			\Sh\Library::actionResponseCheck($responseAtualizar);
		}
		
		//Finalizando retornando os dados corretamente
		return array(
			'status' => true,
			'code' => null,
			'message' => null,
			'data' => $emailNovo
		);
		
		
	}
	
	
}