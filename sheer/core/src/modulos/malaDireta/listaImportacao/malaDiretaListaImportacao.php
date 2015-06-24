<?php
namespace Sh\Modules\malaDiretaListaImportacao;

class malaDiretaListaImportacao {
	
	static public $ativar = array(
		1 => 'Sim',
		2 => 'Não'
	);
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler que irá processar o arquivo da base de dados e irá cadastrar todos os emails sabendo resolver duplicatas
 *
 */
class adicionarVariosEmails extends \Sh\GenericAction {
	
	public function doAction($data){
		
		//CADASTRANDO A IMPORTACAO
		$data['id'] 		= \Sh\Library::getUniqueId();
		$data['total'] 		= $data['novos'] = $data['atualizados'] = 0;
		$response = \Sh\ContentActionManager::doAction('malaDiretaListaImportacao/malaDiretaListaImportacao_add', $data, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//controles gerais
		$totalEmails = 0;
		$totalNovos = 0;
		$totalAtualizados = 0;
		
		//Capturando os contatos do arquivo
		$contatos = $this->getContatosFromFile($response['data']['arquivo']);
		
		//Preciso registrar todos os contatos no módulo de contato
		//Registrando todos os contatos
		foreach ( $contatos as $email=>&$info ) {
			$responseContato = \Sh\ContentActionManager::doAction('malaDiretaContato/malaDiretaContato_add', $info, $this->connection);
			\Sh\Library::actionResponseCheck($responseContato);
			//gravando o id do contato
			$info['id'] = $responseContato['data']['id'];
		}
		
		//CARREGANDO EMAILS JÁ PRESENTES NA LISTA
		//buscando emails previamente cadastrados para fazer tratamento
		$emailsCadastrados = array();
		$listaEmails = \Sh\ContentProviderManager::loadContent('malaDiretaListaEmail/malaDiretaListaEmail_lista', array('idLista'=>$data['idLista']), array('maxRows'=>0), $this->connection);
		if($listaEmails['total'] > 0){
			foreach ($listaEmails['results'] as $id=>$email){
				$email['contato']['email'] 							= strtolower($email['contato']['email']);
				$emailsCadastrados[ $email['contato']['email'] ] 	= $email;
			}
		}
		
		//CONTROLADOR DAS QUERY
		$queryInsert = array();
		$queryInsertTmp = '';
		$queryUpdate = '';
		
		//ITERO POR TODoS OS EMAILS A SEREM CADASTRADOS E INSIRO/ATUALIZO
		foreach ( $contatos as &$info){
			
			//teoricamente não preciso validar o email já que o contato já deveria ter validado
			
			//JÁ EXISTE NA BASE - ATUALIZO
			if( isset($emailsCadastrados[$info['email']]) ) {
				
				//verifico se o contato é para habilitar de modo forçado, se não for não atualizo nada
				if( $info['habilitado'] == 1 && $data['ativar'] == 1 && $emailsCadastrados[$info['email']]['enviar'] == 2 ) {
					//executando
					$statusUpdate = $this->connection->exec('UPDATE sh_ml_listaEmail SET enviar=1 WHERE id="'.$emailsCadastrados[$info['email']]['id'].'";');
					if( $statusUpdate === false ) {
						throw new \Sh\SheerException(array(
							'message' => 'Erro ao atualizar registro de email "'.$info['email'].'"',
							'code' => null
						));
					}
					++$totalAtualizados;
				}
				
			}
			//ADICIONO NA BASE
			else {
				
				//gerando informações para auxilio
				$tmpId = \Sh\Library::getUniqueId(true);
				
				//ADICIONANDO ELEMENTO NA LISTA 
				$queryAdicionar = 'INSERT INTO sh_ml_listaEmail VALUES ("'.$tmpId.'", "'.$data['idLista'].'", "'.$info['id'].'", '.$info['habilitado'].', "'.date('Y-m-d H:i:s').'", "'.$response['data']['id'].'");';
				$statusInsert = $this->connection->exec($queryAdicionar);
				if( $statusInsert === false ) {
					continue;
					throw new \Sh\SheerException(array(
						'message' => 'Erro ao adicionar email "'.$info['email'].'"',
						'code' => null
					));
				}
				
				++$totalNovos;
			}
			
			//controlador de total
			++$totalEmails;
		}
		
		//ATUALIZAO A IMPORTACAO COM OS DADOS GERADOS A PARTIR DA LISTA
		$importacaoAtualizar = array(
			'id' 			=> $data['id'],
			'total'	 		=> $totalEmails,
			'novos' 		=> $totalNovos,
			'atualizados' 	=> $totalAtualizados
		);
		$atualizarImportacao = \Sh\ContentActionManager::doAction('malaDiretaListaImportacao/malaDiretaListaImportacao_update', $importacaoAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($atualizarImportacao);
		
		//ATUALIZANDO A LISTA COM AS NOVAS CONTAGENS
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['idLista'], $this->connection);
		if( !$lista ) {
			throw new \Sh\SheerException(array(
				'message' => 'Erro ao carregar informações da lista de emails que receberá a importação',
				'code' => null
			));
		}
		$listaAtualizar = array(
			'id' => $lista['id']
		);
		
		//calculando o novo total de emails = totalAnterior + totalNovos
		$listaAtualizar['totalEmails'] = $lista['totalEmails'] + $totalNovos;
		//calculando o novo total de emails habilitados = totalHabilitados + totalNovos + totalAtualizados
		$listaAtualizar['totalHabilitados'] = $lista['totalHabilitados'] + $totalNovos + $totalAtualizados;
		//atualizando a lista
		$responseLista = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $listaAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($responseLista);
		
		
		return array(
			'status'=>true,
			'code'=>null,
			'data'=>$response['data']
		);
	}
	
	/**
	 * Método para gerar um array com todos os possíveis contatos da importação
	 * 
	 * @param string $idDocumento
	 * @throws \Sh\ActionException
	 * @return array  
	 */
	protected function getContatosFromFile ( $idDocumento ) {
		
		//LENDO O ARQUIVO DE EMAILS
		$file = \Sh\ContentProviderManager::loadContentById('fileDocument/fileDocument', $idDocumento, $this->connection);
		$arquivo = file_get_contents($file['path']);
		
		//PROCESSANDO ARQUIVO
		//Separo todas as entradas quebrando o arquivo em suas linhas
		$arquivo = explode(PHP_EOL, $arquivo);
		//verifico se existem emails no arquivo
		if( !$arquivo ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Arquivo de importação não contém registros'
			));
		}
		
		//PRECISO CADASTRAR DETERMINAR TODoS OS CONTATOS DESEJADOS E SEUS EMAILS
		$contatos = array();
		//Iterando por cada linha
		foreach ($arquivo as $linha){
			
			//quebro as linhas no tab ou virgula
			//$dado = explode("\t", utf8_encode($linha));
			$dado = preg_split("/[\t,]/", utf8_encode($linha));
			$info = array(
				'email' => trim(strtolower($dado[0])),
				'nome' => null,
				'habilitado' => 1
			);
			
			//verificando o nome do contato
// 			if( isset($dado[1]) && strlen($dado[1]) ) {
// 				$info['nome'] = $dado[1];
// 			}
			//verificando o habilitado do contato
// 			if( isset($dado[2]) && strlen($dado[2]) ) {
// 				$info['habilitado'] = $dado[2];
// 			}
			
			//PRECISO VALIDAR O EMAIL E PULAR CASO NÃO SEJA VÁLIDO
			$emailValidado = \Sh\LibraryValidation::validateEmail($info['email']);
			if( !$emailValidado ) {
				continue;
			}
				
			//Inserindo no controle dos contatos
			$contatos[$info['email']] = $info;
		}
		
		return $contatos;
	}
}



