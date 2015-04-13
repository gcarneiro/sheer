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
		$data['id'] = \Sh\Library::getUniqueId();
		$data['total'] = $data['novos'] = $data['atualizados'] = 0;
		$response = \Sh\ContentActionManager::doAction('malaDiretaListaImportacao/malaDiretaListaImportacao_add', $data, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//LENDO O ARQUIVO DE EMAILS
		$file = \Sh\ContentProviderManager::loadContentById('fileDocument/fileDocument', $response['data']['arquivo']);
		$arquivo = file_get_contents($file['path']);
		
		//controles gerais
		$totalEmails = 0;
		$totalNovos = 0;
		$totalAtualizados = 0;
		
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
		
		//CARREGANDO EMAILS JÁ PRESENTES NA LISTA
		//buscando emails previamente cadastrados para fazer tratamento
		$emailsCadastrados = array();
		$listaEmails = \Sh\ContentProviderManager::loadContent('malaDiretaListaEmail/emailsPorLista', array('idLista'=>$data['idLista']));
		if($listaEmails['total'] > 0){
			foreach ($listaEmails['results'] as $id=>$email){
				$emailsCadastrados[ $email['email'] ] = $email;
			}
		}
		
		//CONTROLADOR DAS QUERY
		$queryInsert = array();
		$queryInsertTmp = '';
		$queryUpdate = '';
		
		//PREPARO OS EMAILS DO ARQUIVO PARA CADASTRO
		foreach ($arquivo as $linha){
			//quebro as linhas no tab ou virgula
			//$dado = explode("\t", utf8_encode($linha));
			$dado = preg_split("/[\t,]/", utf8_encode($linha));
			$info = array(
				'email' => trim($dado[0]),
				'nome' => null,
				'habilitado' => 1
			);
			
			//verificando o nome do contato
			if( isset($dado[1]) && strlen($dado[1]) ) {
				$info['nome'] = $dado[1];
			}
			//verificando o habilitado do contato
			if( isset($dado[2]) && strlen($dado[2]) ) {
				$info['habilitado'] = $dado[2];
			}
			
			//TODO VALIDAR EMAIL
			
			//JÁ EXISTE NA BASE - ATUALIZO
			if( isset($emailsCadastrados[$info['email']]) ) {
				
				//verifico se o contato é para habilitar
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
				$novoId = \Sh\Library::getUniqueId(true);
				$tmpNome = ( $info['nome'] ) ? '"'.$info['nome'].'"' : 'NULL';
				//gerando query
				if( $totalNovos % 2 == 0 ) {
					if( $queryInsertTmp ) {
						$queryInsertTmp .= ';';
						$queryInsert[] = $queryInsertTmp;
					}
					$queryInsertTmp = 'INSERT INTO sh_ml_listaEmail VALUES ';
					$queryInsertTmp .= '("'.$novoId.'", "'.$data['idLista'].'", '.$tmpNome.', "'.$info['email'].'", '.$info['habilitado'].', "'.date('Y-m-d H:i:s').'", "'.$response['data']['id'].'")';
				}
				else {
					$queryInsertTmp .= ', ("'.$novoId.'", "'.$data['idLista'].'", '.$tmpNome.', "'.$info['email'].'", '.$info['habilitado'].', "'.date('Y-m-d H:i:s').'", "'.$response['data']['id'].'") ';
				}
				
				++$totalNovos;
			}
			
			//controlador de total
			++$totalEmails;
		}
		
		//finalizando query de insert
		if( $queryInsertTmp ) {
			$queryInsert[] = $queryInsertTmp;
		}
		
		//operando entre todas as querys de inserção e executando-as
		if($queryInsert){
			foreach ($queryInsert as $query) {
				$statusInsert = $this->connection->exec($query);
				if( $statusInsert === false ) {
					throw new \Sh\SheerException(array(
						'message' => 'Erro ao adicionar grupo de novos emails',
						'code' => null
					));
				}
			}
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
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['idLista']);
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
}