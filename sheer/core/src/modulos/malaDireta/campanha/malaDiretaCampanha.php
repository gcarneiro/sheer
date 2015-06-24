<?php

namespace Sh\Modules\malaDiretaCampanha;

/**
 * @author Guilherme
 *
 * Interface para criação de listas de emails.
 * Essas listas devem ser descritas programaticamente e utilizadas pelo módulo de envio.
 *
 */
interface iMalaDiretaListaDeEmails {

	/**
	 * Método para obter a lista de emails para onde devem ser enviados os emails
	 * Dentro deste método o desenvolvedor pode executar qualquer operação e deve retornar um array contendo os emails da lista
	 *
	 * @return multitype: 'id' => array( 'id', 'email', 'nome', 'emailData' )
	 *
	 * 		Id Identificador único do email
	 * 		email Endereço de email a ser submetido a mensagem
	 * 		nome Nome do destinatário
	 * 		emailData Dados extras que deseja que sejam enviados o email. Sera utilizado na substituição no corpo da mensagem
	 */
	public function loadEmailsList ();

	/**
	 * Método para remover um email da lista.
	 *
	 * @param string $idEmail Identificador único do email a ser removido da lista
	 *
	 * @return boolean
	*/
	public function disableEmailEntry ( $idEmail );

}


/**
 * Função para exibir o conteudo da campanha para o publico, realizar contagem de visualização e remoção da lista de inscrição
 * 
 * A Visualização Email já foi transferida para action/malaDiretaDisparo/visualizacaoEmail
 * 
 * @author Patrick
 */
class malaDiretaActionPublico extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		$response['data'] = null;
		
		/*
		 * $tipo
		 * 1 => visualizacaoWeb
		 * 2 => remocao
		 * 3 => visualizacao
		 */
		
		//verifico se recebi o parametro de tipo correto
		if( !in_array($data['tipo'], array(1,2,3) ) ){
			echo 'Erro ao carregar o conteúdo';
			return array(
				'status' => false,
				'code' => null,
				'data' => null,
			);
		}
		//carrego o disparo para pegar o id da campanha
		$disparo = \Sh\ContentProviderManager::loadItem('malaDiretaDisparo/malaDiretaDisparo', $data['idDisparo']);
		
		//verifico se tenho o disparo
		if(!$disparo){
			echo 'Erro ao carregar o conteúdo';
			return array(
				'status' => false,
				'code' => null,
				'data' => null,
			);
		}
		
		//carregando a campanha para exibir o html
		$campanha = \Sh\ContentProviderManager::loadItem('malaDiretaCampanha/malaDiretaCampanha', $disparo['idCampanha']);
		
		//preparo controle para adicionar visualização no disparo
		$updateDisparo = array(
			'id' => $disparo['id']
		);
		
		//caso o tipo seja visualização(1 ou 3) eu adiciono uma visualização para o usuario
		if($data['tipo'] == 1 || $data['tipo'] == 3){
			
			$updateDisparo['visualizacoesUnicas'] = 0;
			$updateDisparo['totalVisualizacoes'] = 0;
			
			if($data['tipo'] == 3){
				header('Content-Type: image/gif; charset=UTF-8');
				readfile('resources/images/pixel.gif');
				$updateDisparo['totalVisualizacoes'] = ++$disparo['totalVisualizacoes'];
			}
			else{
				echo '<title>'.$campanha['assunto'].'</title>';
				echo $campanha['html'];
			}
			
			$adicionarVisualizacao = array(
				'idDisparo' => $data['idDisparo'],
				'idUsuario' => $data['idUser'],
				'adicionadoEm' => date('d/m/Y H:i:s'),
				'tipoVisualizacao' => $data['tipo']
			);
			
			
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparo/malaDiretaDisparoVisualizacao_add', $adicionarVisualizacao, $this->connection);
			$visualizacoesDisparo = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/malaDiretaDisparoVisualizacao_lista',array('idDisparo'=>$disparo['id']));
			$updateDisparo['totalVisualizacoes'] = $visualizacoesDisparo['total'];
			
			//contando as visualizações unicas	
			$arrayEmails = null;
			foreach ($visualizacoesDisparo['results'] as $id=>$visualizacao){
				
				//verifico se ja contei a visualização desse usuario
				if(isset($arrayEmails[$visualizacao['idUsuario']])){
					continue;
				}
				
				//adiciono ao array de controle uma posicao com o id do usuario atual para não contar mais de uma vez
				$arrayEmails[$visualizacao['idUsuario']] = true;
				
				//adiciono +1 ao total de visualizações unicas
				++$updateDisparo['visualizacoesUnicas'];
			}
			
		}
		//estou removendo inscricao
		else if ($data['tipo'] == 2){
			
			//VERIFICO SE A INSCRIÇÃO JÁ FOI REMOVIDA NESTE DISPARO
			$inscricaoStatus = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/disparoRemocaoPorUsuario', array('idUsuario'=>$data['idUser']));
			if( $inscricaoStatus['total'] > 0 ){
				echo '<h1>Inscrição Removida</h1>';
				
				return array(
					'status' => false,
					'code' => null,
					'data' => null,
				);
			}

			//REMOVO A INSCRIÇÃO
			//busco a inscrição
			$email = \Sh\ContentProviderManager::loadContentById('malaDiretaListaEmail/malaDiretaListaEmail', $data['idUser']);
			//se já estiver habilitado simplesmente respondo ao usuário
			if( $email['enviar'] == 2 ){
				echo '<h1>Inscrição Removida</h1>';
				return array(
					'status' => false,
					'code' => null,
					'data' => null,
				);
			}
			//desabiliando o envio
			$desabiltiarEnvio = array(
				'id' => $email['id'],
				'enviar' => 2
			);
			$responseDesabilitar = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/habilitarDesabilitarEmail', $desabiltiarEnvio, $this->connection);
			//adiciono a remoção
			$removerInscricao = array(
				'idDisparo' => $data['idDisparo'],
				'idUsuario' => $data['idUser'],
				'adicionadoEm' => date('d/m/Y H:i:s')
			);
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparo/malaDiretaDisparoRemocao_add', $removerInscricao, $this->connection);
			
			//adicionando +1 no total de remocoes do disparo
			$updateDisparo['remocoes'] = ++$disparo['remocoes'];
			
			//RECALCULAR O TOTAL DE EMAILS E HABILITADOS DA LISTA
			$response = \Sh\ContentActionManager::doAction('malaDiretaLista/recalcularTotalHabilitados', ['id'=>$disparo['idLista']], $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
			echo '<h1>Inscrição Removida</h1>';
			
		}
		
		//ATUALIZO O DISPARO COM A NOVA QUANTIDADE VISUALIZAÇÃO OU REMOÇÃO	
		$responseDisparo = \Sh\ContentActionManager::doAction('malaDiretaDisparo/malaDiretaDisparo_update', $updateDisparo, $this->connection);
		
		return array(
			'status' => true,
			'code' => null,
			'data' => array('remocaoVisualizacao'=> $response['data'], 'disparo'=>$responseDisparo['data']),
		);
	}
}


/**
 * Função para editar uma campanha. Este verifica se a campanha possui agendamento ou disparo.
 * Tendo um dos dois ele não atualiza o HTML
 * @author Patrick
 */
class editarCampanha extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		$agendamentos = \Sh\ContentProviderManager::loadContent('malaDiretaAgendamento/malaDiretaAgendamento_lista', array('idCampanha'=>$data['id']));
		$disparos = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/malaDiretaDisparo_lista', array('idCampanha'=> $data['id']));
		
		if($agendamentos['total'] > 0 || $disparos['total'] > 0){
			unset($data['html']);
		}
		//datetime.now no xml não funcionou
		$data['atualizadoEm'] = date('d/m/Y H:i:s');
		
		$response = \Sh\ContentActionManager::doAction('malaDiretaCampanha/malaDiretaCampanha_update', $data, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return array(
			'status'=>true,
			'code' => null,
			'data'=> $response['data']
		);
	}
	
}