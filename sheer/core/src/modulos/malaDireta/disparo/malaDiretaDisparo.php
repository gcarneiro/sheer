<?php
namespace Sh\Modules\malaDiretaDisparo;

class malaDiretaDisparoVisualizacao{
	
	static public $tipoVisualizacao = array(
			
		1 => 'Visualização Web',
		2 => 'Remover',
		3 => 'Visualização Email',
	);
}

class removerEmail extends \Sh\GenericContentProvider {
	
	public function getData( $filters=array(), $configs=array() ) {
		try {
			
			\Sh\ContentActionManager::invokeGreenCard();
			
			//CARREGANDO DISPARO
			$disparo = \Sh\ContentProviderManager::loadContentById('malaDiretaDisparo/malaDiretaDisparo', $filters['d']);
			if( !$disparo ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Disparo não encontrado'
				));
			}
				
			//CRIANDO UMA CONEXAO
			$connection = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
				
			//DETERMINAR SE ESTA É REMOÇÃO É UNICA PARA AQUELE DISPARO
			$remocaoUnica = false;
			//carrego as visualizações do usuário para aquele disparo
			$visualizacoesUsuario = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/malaDiretaDisparoRemocao_lista', array('idDisparo'=>$disparo['id'], 'idUsuario'=>$filters['u']));
			if( $visualizacoesUsuario['total'] == 0 ) {
				$remocaoUnica = true;
			}
		
			//ATUALIZO O DISPARO COM A NOVA INFORMAÇÕES DE VISUALIZACOES
			if( $remocaoUnica ) {
				$queryAtualizacaoDisparo = 'UPDATE sh_ml_disparo SET remocoes=remocoes+1 WHERE id="'.$disparo['id'].'";';
				$respostaQuery = $connection->exec($queryAtualizacaoDisparo);
				if( !$respostaQuery ) {
					throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Erro ao registrar remoção'
					));
				}
			}
			
			//remover aqui o email da lista
			$emailAtualizar = array(
				'id' => $filters['u'],
				'enviar' => 2
			);
			$responseUpdate = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/malaDiretaListaEmail_update', $emailAtualizar, $connection);
			\Sh\Library::actionResponseCheck($responseUpdate);
		
			//INSIRO O MAPEAMENTO DA NOVA REMOCAO
			$remocao = array (
				'idDisparo' 		=> $disparo['id'],
				'idUsuario' 		=> $filters['u'],
				'adicionadoEm' 		=> date('d/m/Y H:i:s')
			);
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparo/malaDiretaDisparoRemocao_add', $remocao, $connection);
			\Sh\Library::actionResponseCheck($response);
				
			$connection->commit();
			
			\Sh\ContentActionManager::removeGreenCard();
				
			return '<h3>Email Removido da Lista com Sucesso</h3>';
		}
		catch (\Sh\SheerException $e) {
			return '<h3>'.$e->getErrorMessage().'</h3>';
		}
	}
	
}

/**
 * @author Guilherme
 * 
 * ContentProvider para exibir o email da newsletter para o usuário em forma alternativa e também marcar a sua visualizacao
 *
 */
class visualizacaoAlternativa extends \Sh\GenericContentProvider {
	
	public function getData( $filters=array(), $configs=array() ) {
		
		try {
			//Invocando greencard
			\Sh\ContentActionManager::invokeGreenCard();
			
			//CARREGANDO DISPARO
			$disparo = \Sh\ContentProviderManager::loadContentById('malaDiretaDisparo/malaDiretaDisparo', $filters['d']);
			if( !$disparo ) {
				throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Disparo não encontrado'
				));
			}
			
			//CARREGANDO CAMPANHA
			$campanha = \Sh\ContentProviderManager::loadContentById('malaDiretaCampanha/malaDiretaCampanha', $disparo['idCampanha']);
			if( !$campanha ) {
				throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Campanha não encontrada'
				));
			}
			
			//CRIANDO UMA CONEXAO
			$connection = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
			
			//DETERMINAR SE ESTA É UMA VISUALIZAÇÃO ÚNICA
			$visualizacaoUnica = false;
			//carrego as visualizações do usuário para aquele disparo
			$visualizacoesUsuario = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/malaDiretaDisparoVisualizacao_lista', array('idDisparo'=>$disparo['id'], 'idUsuario'=>$filters['u']));
			if( $visualizacoesUsuario['total'] == 0 ) {
				$visualizacaoUnica = true;
			}
				
			//ATUALIZO O DISPARO COM A NOVA INFORMAÇÕES DE VISUALIZACOES
			if( $visualizacaoUnica ) {
				$queryAtualizacaoDisparo = 'UPDATE sh_ml_disparo SET totalVisualizacoes=totalVisualizacoes+1, visualizacoesUnicas=visualizacoesUnicas+1 WHERE id="'.$disparo['id'].'";';
			}
			else {
				$queryAtualizacaoDisparo = 'UPDATE sh_ml_disparo SET totalVisualizacoes=totalVisualizacoes+1 WHERE id="'.$disparo['id'].'";';
			}
			$respostaQuery = $connection->exec($queryAtualizacaoDisparo);
			if( !$respostaQuery ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Erro ao computar visualização no disparo'
				));
			}
				
			//INSIRO O MAPEAMENTO DA NOVA VISUALIZAÇÃO
			$visualizacao = array (
				'idDisparo' 		=> $disparo['id'],
				'idUsuario' 		=> $filters['u'],
				'adicionadoEm' 		=> date('d/m/Y H:i:s'),
				'tipoVisualizacao' 	=> 1
			);
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparo/malaDiretaDisparoVisualizacao_add', $visualizacao, $connection);
			\Sh\Library::actionResponseCheck($response);
			
			$connection->commit();
			
			//Revogando greencard
			\Sh\ContentActionManager::removeGreenCard();
			
			return $campanha['html'];
		}
		catch (\Sh\SheerException $e) {
			//Revogando greencard
			\Sh\ContentActionManager::removeGreenCard();
			
			return '<h3>'.$e->getErrorMessage().'</h3>';
		}
	}
}


/**
 * @author Guilherme
 * 
 * ActionHandler que irá marcar a visualização de um disparo pelo o email do usuário
 * Este espera os parametros "u" e "d", sendo usuario e disparo respectivamente
 *
 */
class marcarVisualizacaoNoEmail extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		/*
		 * u = user
		 * d = disparo
		 */
		try {
			//CAPTURANDO DISPARO
			$disparo = \Sh\ContentProviderManager::loadContentById('malaDiretaDisparo/malaDiretaDisparo', $data['d']);
			if( !$disparo ) {
				throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Erro ao carregar disparo'
				));
			}
			
			//DETERMINAR SE ESTA É UMA VISUALIZAÇÃO ÚNICA
			$visualizacaoUnica = false;
			//carrego as visualizações do usuário para aquele disparo
			$visualizacoesUsuario = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/malaDiretaDisparoVisualizacao_lista', array('idDisparo'=>$disparo['id'], 'idUsuario'=>$data['u']));
			if( $visualizacoesUsuario['total'] == 0 ) {
				$visualizacaoUnica = true;
			}
			
			//ATUALIZO O DISPARO COM A NOVA INFORMAÇÕES DE VISUALIZACOES
			if( $visualizacaoUnica ) {
				$queryAtualizacaoDisparo = 'UPDATE sh_ml_disparo SET totalVisualizacoes=totalVisualizacoes+1, visualizacoesUnicas=visualizacoesUnicas+1 WHERE id="'.$disparo['id'].'";';
			}
			else {
				$queryAtualizacaoDisparo = 'UPDATE sh_ml_disparo SET totalVisualizacoes=totalVisualizacoes+1 WHERE id="'.$disparo['id'].'";';
			}
			$respostaQuery = $this->connection->exec($queryAtualizacaoDisparo);
			if( !$respostaQuery ) {
				throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Erro ao computar visualização no disparo'
				));
			}
			
			//INSIRO O MAPEAMENTO DA NOVA VISUALIZAÇÃO
			$visualizacao = array(
					'idDisparo' 		=> $disparo['id'],
					'idUsuario' 		=> $data['u'],
					'adicionadoEm' 		=> date('d/m/Y H:i:s'),
					'tipoVisualizacao' 	=> 3
			);
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparo/malaDiretaDisparoVisualizacao_add', $visualizacao, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
			//TENDO TUDO OCORRIDO CERTO, COMMITO
			$this->connection->commit();
		}
		catch (\Sh\SheerException $e) {
			//LOGAR NA MALADIRETA
			\Sh\LoggerProvider::log('malaDireta', $e->getErrorMessage());
		}
		
		header('Content-Type: image/gif; charset=UTF-8');
		readfile('resources/images/pixel.gif');
		exit;
	}
	
}

/**
 * ACTIONHANDLER para disparar uma mala direta. Este recebe o id da campanha e id do agendamento caso haja
 * 
 * @param idCampanha
 * @param idAgendamento
 * @author Guilherme
 */
class dispararMalaDireta extends \Sh\GenericAction{
	
	public function doAction($data){
		
		//BUSCO A LISTA DE EMAILS ESCOLHIDA
		$listas = \Sh\ContentProviderManager::loadContent('malaDiretaLista/malaDiretaLista_lista', array(
			'id' => $data['idLista']
		), $this->connection);
		
		if( $listas['available'] == 0 ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Erro ao carregar a lista de emails'
			));
		}
		
		$totalHabilitados = 0;
		foreach($listas['results'] as $lista){
			$totalHabilitados += $lista['totalHabilitados'];
		}
		
		//FIXME verificar essa funcionalidade, deveria só receber o id do agendamento
		//se tenho id de agendamento, pego os dados passado para gerar o disparo
		if( isset($data['idAgendamento']) && $data['idAgendamento'] ){
			$adicionarDisparo = $data;
		}
		else{
			$campanha = \Sh\ContentProviderManager::loadContentById('malaDiretaCampanha/malaDiretaCampanha', $data['idCampanha']);
			//montando array de inserção
			$adicionarDisparo['idCampanha']		= $campanha['id'];
			$adicionarDisparo['idRemetente']	= $campanha['idRemetente'];
			$adicionarDisparo['assunto']		= $campanha['assunto'];
			$adicionarDisparo['html']			= $campanha['html'];
		}
		$adicionarDisparo['enviados'] = 0;
		$adicionarDisparo['total'] = $totalHabilitados;
		$adicionarDisparo['id'] = \Sh\Library::getUniqueId(true);
		
		//CAPTURO TODOS OS HREF
		//JÁ SUBSTITUO CRIANDO UM LINKTOKEN PARA ELES
		$adicionarDisparo['html'] = preg_replace_callback('/href="(.*?)"/', function ($m){
			
			//CAPTURANDO AS INFORMAÇÕES DO SERVIDOR
			//pego as configurações do projeto para gerar as urls
			$projectConfig = \Sh\ProjectConfig::getProjectConfiguration();
			$urlWebsite = $projectConfig['domain'].$projectConfig['domainPath'];
			$link = $m[1];
			
			$novoToken = array(
				'id' => \Sh\Library::getUniqueId(),
				'link' => $link,
			);
			
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparoLink/malaDiretaDisparoLink_add', $novoToken, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
			return 'href="'.str_replace('//', '/', $urlWebsite.'/action.php?ah=malaDiretaDisparoLink/verificarLink&id='.$novoToken['id']).'"';
			
		}, $adicionarDisparo['html']);
		
		//CADASTRO O DISPARO
		$response = \Sh\ContentActionManager::doAction('malaDiretaDisparo/malaDiretaDisparo_add', $adicionarDisparo, $this->connection);
		\SH\Library::actionResponseCheck($response);
		
		foreach($listas['results'] as $lista){
			
			if( $lista['totalHabilitados'] == 0 ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'A lista de emails: "'.$lista['nome'].'" não possui emails habilitados para envio'
				));
			}
			
			$disparoLista = array(
				'idLista' => $lista['id'],
				'idDisparo' => $adicionarDisparo['id'],
			);
			
			$responseDisparoLista = \Sh\ContentActionManager::doAction('malaDiretaDisparoLista/malaDiretaDisparoLista_add', $disparoLista, $this->connection);
			\Sh\Library::actionResponseCheck($responseDisparoLista);
			
		}
		
		//PRECISO INSERIR OS EMAILS NA FILA DE ENVIO
		$this->inserirEmailsDisparadosFila($adicionarDisparo, $listas['results']);
		
		return array (
			'status' => $response,
			'code' => null,
			'data' => null
		);
		
	}
	
	/**
	 * Método utilizado para cadastrar os emails que serão enviados pelo disparo. Este irá inserir diretamente na fila
	 * 
	 * @param array $disparo Dados do disparo recem adicionado
	 * @param array $lista Dados da lista de emails obtidas do ContentProvider
	 * @return boolean
	 */
	protected function inserirEmailsDisparadosFila ($disparo, $lista){
		
		//CAPTURANDO OS EMAILS DA LISTA
		$idListas = array();
		foreach($lista as $id=>$element){
			$idListas[$id] = $id;
		}
		
		$emailsHabilitados = \Sh\ContentProviderManager::loadContent('malaDiretaListaEmail/emailsPorLista_ilimitado', array('idLista'=>$idListas, 'enviar'=>1));
		if( $emailsHabilitados['total'] == 0 ) {
			throw new \Sh\ActionException(array(
					'code' => null,
					'message' => 'A lista de emails não possui emails habilitados para envio'
			));
		}
		
		$novoArrayEmailsHabilitados = array();
		foreach($emailsHabilitados['results'] as $email){
			$emailAtual = &$novoArrayEmailsHabilitados[$email['idContato']];
			if(!isset($emailAtual)){
				$emailAtual = $email;
			}
		}
		
		$dateTimeDisparo = date('Y-m-d H:i:s');
		
		//GERANDO QUERY DE INSERÇÃO DOS EMAILS
		$querysArray = array();
		$queryTmp = '';
		$total = 0;
		
		foreach ($novoArrayEmailsHabilitados as $email){
			
			$controlador = array(
				'id' => $email['id'],
				'nome' => $email['contato']['nome'],
				'email' => $email['contato']['email'],
				'enviar' => $email['enviar']
			);
			$contatoInfo = json_encode($controlador);
			
			//gerando a query do elemento
			if( !$queryTmp ) {
				$queryTmp .= 'INSERT INTO sh_ml_disparoEmail VALUES ';
			}
			else {
				$queryTmp .= ', ';
			}
			$queryTmp .= '("'.\Sh\Library::getUniqueId().'", "'.$email['idContato'].'", "'.$disparo['id'].'", "'.$dateTimeDisparo.'", "'.addslashes($contatoInfo).'") ';
			//tratando o maximo de insercoes por query
			++$total;
			if( $total == 300 ) {
				$queryTmp .= ';';
				$querysArray[] = $queryTmp;
				$queryTmp = '';
			}
		}
		//inserindo ultima no array query
		if( $queryTmp ) {
			$queryTmp .= ';';
			$querysArray[] = $queryTmp;
			$queryTmp = '';
		}
		
		//INSERINDO OS DADOS NO BANCO
		foreach ( $querysArray as $sqlQuery ) {
			$status = $this->connection->exec($sqlQuery);
			if( $status === false ) {
				throw new \Sh\ActionException(array(
					'code' => null,
					'message' => 'Erro ao inserir emails na fila'
				));
			}
		}
		
		return true;
	}
}

/**
 * JOB que irá de irá enviar os emails, e deletando cada email enviado da tabela sh_ml_disparoEmail
 * @author Patrick
 *
 */
class dispararEmails extends \Sh\GenericJob {
	
	public function run(){
		
		//CARREGANDO A LISTA DE EMAILS A SEREM ENVIADOS
		$listaEmails = \Sh\ContentProviderManager::loadContent('malaDiretaDisparo/emailsParaDisparo');
		if( $listaEmails['total'] == 0 ) {
			return true;
		}
		
		//REMOVENDO OS EMAILS CARREGADOS DA FILA DE ENVIO
		//TODO devo somente remover os que foram enviados
		//TODO devo marcar os emails não enviados como não enviados
		$queryRemocao = 'DELETE FROM sh_ml_disparoEmail WHERE id IN (';
		$queryRemocaoIn = '';
		foreach ( $listaEmails['results'] as $id=>&$disparar ) {
			if( strlen($queryRemocaoIn) > 0 ) {
				$queryRemocaoIn .= ', ';
			}
			$queryRemocaoIn .= '"'.$disparar['id'].'"';
		}
		$queryRemocao = $queryRemocao.$queryRemocaoIn.')';
		//Removendo do banco
		$connection = \Sh\DatabaseConnectionProvider::getDatabaseConnection();
		$st = $connection->prepare($queryRemocao);
		$response = $st->execute();
		$connection->commit();
		
		//RECOMEÇANDO NOVA TRANSACAO
		$connection->beginTransaction();
		
		//GERANDO A ESTRUTURA DO HTML
		$html = '';
		$html .= '<div style="text-align:center;">';
			$html .= '<small>Caso não consiga visualizar a mensagem clique <a target="_blank" href="${{email.linkAlternativo}}">aqui</a></small>';
		$html .= '</div>';
		
		$html .= '<div>';
			$html .= '${{email.html}}';
		$html .= '</div>';
		
		$html .= '<div style="text-align:center;">';
			$html .= '<small>Caso não queira receber esse tipo de e-mail clique <a target="_blank" href="${{email.linkRemocao}}">aqui</a></small>';
		$html .= '</div>';
		$html .= '<img src="${{email.contagemVisualizacao}}">';
		
		//CAPTURANDO AS INFORMAÇÕES DO SERVIDOR
		//pego as configurações do projeto para gerar as urls
		$projectConfig = \Sh\ProjectConfig::getProjectConfiguration();
		$urlWebsite = $projectConfig['domain'].$projectConfig['domainPath'];
		
		//EFETUANDO DISPAROS
		$malaDiretaDisparoInfo = array();
		foreach ($listaEmails['results'] as $id=>$email) {
			
			try {
				//BUSCO O MALADIRETADISPARO PARA CONTAGEM DAS INFORMAÇÕES
				$disparo = null;
				//verifico no cache primeiro
				if( !isset($malaDiretaDisparoInfo[$email['idDisparo']]) ) {
					$disparo = \Sh\ContentProviderManager::loadContentById('malaDiretaDisparo/malaDiretaDisparo', $email['idDisparo']);
					if( !$disparo ) {
						throw new \Sh\SheerException([
							'code' => 'SM_LIST-LOAD',
							'message' => 'Erro ao recuperar informações do disparo.'
						]);
					}
					$malaDiretaDisparoInfo[$disparo['id']] = array(
							'id'		=> $disparo['id'],
							'enviados'	=> 0,
							'falhas'	=> 0,
							'disparo' => $disparo
					);
				}
				else {
					$disparo = $malaDiretaDisparoInfo[$email['idDisparo']]['disparo'];
				}
				if( !$disparo ) {
					throw new \Sh\SheerException([
						'code' => 'SM_LIST-LOAD',
						'message' => 'Erro ao recuperar informações do disparo.'
					]);
				}
					
				//capturando informações do contato que estão em json e transformo em array
				$contato = json_decode($email['contatoInfo'], true);
				if( !$contato ) {
					throw new \Sh\SheerException([
						'code' => 'SM_CONTATO-INFO',
						'message' => 'Erro ao parsear informações de contato a partir do json.'
					]);
				}
					
				//INICIANDO DISPARO CONTROLE
				$emailDisparoControle = array();
				$emailDisparoControle['user.id'] 		= $contato['id'];
				$emailDisparoControle['user.nome'] 		= $contato['nome'];
				$emailDisparoControle['user.email'] 	= $contato['email'];
				$emailDisparoControle['email.html'] 	= $disparo['html'];
				
				//gero as urls de  remoção, visualização e contagem de visualizacao ao abrir email
				$emailDisparoControle['email.linkRemocao'] = 'http://'.str_replace('//', '/', $urlWebsite.'/renderer.php?rd=malaDiretaDisparo/removerEmail&htmlResponse=1&u='.$contato['id'].'&d='.$disparo['id'].'&shcli=1');
				$emailDisparoControle['email.linkAlternativo'] = 'http://'.str_replace('//', '/', $urlWebsite.'/renderer.php?rd=malaDiretaDisparo/visualizacaoAlternativa&htmlResponse=1&u='.$contato['id'].'&d='.$disparo['id'].'&shcli=1');
				$emailDisparoControle['email.contagemVisualizacao'] = 'http://'.str_replace('//', '/', $urlWebsite.'/action.php?ah=malaDiretaDisparo/visualizacaoEmail&u='.$contato['id'].'&d='.$disparo['id'].'&shcli=1');
					
				//faço as substituições necessarias
				$mensagem = $html;
				foreach ($emailDisparoControle as $key=>$val){
// 					$emailDisparoControle['email.html'] = preg_replace('/\$\{{'.$key.'\}}/', $val, $emailDisparoControle['email.html']);
					$mensagem = preg_replace('/\$\{{'.$key.'\}}/', $val, $mensagem);
				}
					
				//monto o array para enviar o email
				$dadosEmail = array(
						'address' => array('name'=>$contato['nome'], 'email'=>$contato['email']),
						'from' => array('name'=>$disparo['remetente']['nomeEnvio'], 'email'=>$disparo['remetente']['emailEnvio']),
						'subject' => $disparo['assunto'],
						'body' => $mensagem
				);
					
				//envio o email
				$response = \Sh\MailerProvider::sendMail($dadosEmail);
				if( !$response['status'] ) {
					throw new \Sh\SheerException(array(
						'code' => 'SM_MAIL-SENDING',
						'message' => $response['message']
					));
					$malaDiretaDisparoInfo[$disparo['id']]['falhas']++;
					
				}
				//SUCESSO NO ENVIO
				else {
					$malaDiretaDisparoInfo[$disparo['id']]['enviados']++;
				}
					
				
			}
			catch (\Sh\SheerException $e) {
				//TODO POR ENQUANTO FAÇO NADA, MAS DEVO TRATAR OS ERROS DE ALGUMA FORMA
				var_dump($e);
				
			}
			
		}
		
		//ATUALIZO TODoS OS DISPAROS COM AS NOVAS INFORMAÇÕES
		foreach ( $malaDiretaDisparoInfo as $idDisparo => $info ) {
			$queryAtualizarDisparo 	= 'UPDATE sh_ml_disparo SET enviados=enviados+'.$info['enviados'].' WHERE id="'.$idDisparo.'"';
			$responseAtualizar 		= $connection->exec($queryAtualizarDisparo);
		}
		
		//COMMITANDO TODAS AS TRANSAÇÕES
		$connection->commit();
	}
	
} 
