<?php
namespace Sh\Modules\malaDiretaLista;

class malaDiretaLista {
	
	static public $tipo = array(
		1 => 'Simples',
		2 => 'Complexa'
	);
	
}

/**
 * @author Guilherme
 *
 * ActionHandler para recalcular os totais de emails e habilitados da lista
 */
class recalcularTotalHabilitados extends \Sh\GenericAction {

	public function doAction($data) {

		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['id']);
		if( !$lista ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Lista inválida para recalcular seus totais'
			));
		}

		//RECALCULAR TOTAL DE EMAILS NA LISTA
		$statement = $this->connection->prepare('SELECT count(*) as total FROM sh_ml_listaEmail WHERE idLista="'.$lista['id'].'";');
		$response = $statement->execute();
		if( !$response ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Erro ao tentar obter o total de emails da lista "'.$lista['nome'].'"'
			));
		}
		$row = $statement->fetch(\PDO::FETCH_ASSOC);
		$totalEmails = (integer) $row['total'];
		$totalHabilitados = $totalEmails;
			
		//RECALCULAR TOTAL DE HABILITADOS DA LISTA
		$statement = $this->connection->prepare('SELECT count(*) as total FROM sh_ml_listaEmail WHERE enviar=1 AND idLista="'.$lista['id'].'";');
		$response = $statement->execute();
		if( !$response ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Erro ao tentar obter o total de emails habilitados da lista "'.$lista['nome'].'"'
			));
		}
		$row = $statement->fetch(\PDO::FETCH_ASSOC);
		$totalHabilitados = (integer) $row['total'];
			
		//ATUALIZANDO A LISTA
		$listaAtualizar = [
			'id' => $lista['id'],
			'totalEmails' => $totalEmails,
			'totalHabilitados' => $totalHabilitados
		];
		$response = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaLista_update', $listaAtualizar, $this->connection);
		\SH\Library::actionResponseCheck($response);

		return $response;
	}

}

/**
 * @author Guilherme
 * 
 * ActionHandler para sincronizar uma lista complexa
 *
 */
class sincronizarLista extends \Sh\GenericAction {
	
	protected $baseConexaoComplexa;
	
	/**
	 * @var array que irá ser o content malaDiretaLista
	 */
	protected $lista;
	
	public function doAction($data) {
		
		//AUMENTANDO TEMPO DE EXECUCAO
		set_time_limit(360);
		
		//CARREGANDO A LISTA
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['id']);
		if( !$lista ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Lista inválida para recalcular seus totais'
			));
		}
		$this->lista = $lista;
		
		//verificando que é uma lista complexa
		if( $this->lista['tipo'] != 2 ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'A sincronia só é permitida para listas complexas'
			));
		}
		
		//Gerando password para acesso ao banco
		if( $this->lista['password']===NULL ) {
			$this->lista['password'] = '';
		}
		
		//gerando array base para a lista completa
		$this->baseConexaoComplexa = [
			'driver' 	=> 'mysql',
			'host' 		=> $this->lista['host'],
			'username' 	=> $this->lista['username'],
			'password' 	=> $this->lista['password'],
			'database' 	=> $this->lista['databaseName'],
			'table'		=> $this->lista['databaseTable'],
			'fields'	=> [
				'nome'			=> $this->lista['fieldNome'],
				'email'			=> $this->lista['fieldEmail'],
				'enviar'		=> $this->lista['fieldEnviar'],
				'enviarValor'	=> $this->lista['fieldEnviarValor']
			]
		];
		
		//PROCESSANDO AS CONFIGURAÇÕES
		//validando os campos da conexao
		if( !isset( $this->baseConexaoComplexa['host'] ) || !isset( $this->baseConexaoComplexa['username'] ) || !isset( $this->baseConexaoComplexa['password'] ) || !isset( $this->baseConexaoComplexa['database'] ) || !isset( $this->baseConexaoComplexa['table'] ) ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Informações incompletas sobre a base de conexão'
			));
		}
		//validando os campos dos dados
		if( !isset( $this->baseConexaoComplexa['fields'] ) ) {
			$this->baseConexaoComplexa['fields'] = [
				'nome' => 'nome',
				'email' => 'email',
				'enviar' => 'enviar'
			];
		}
		else {
			if( !isset($this->baseConexaoComplexa['fields']['nome']) ) 		{ $this->baseConexaoComplexa['fields']['nome'] = 'nome'; }
			if( !isset($this->baseConexaoComplexa['fields']['email']) ) 		{ $this->baseConexaoComplexa['fields']['email'] = 'email'; }
			if( !isset($this->baseConexaoComplexa['fields']['enviar']) ) 	{ $this->baseConexaoComplexa['fields']['enviar'] = 'enviar'; }
		}
		
		//TESTANDO A BUSCA POR UM REGISTRO
		$conn = \Sh\DatabaseConnectionProvider::newDatabaseConnection($this->baseConexaoComplexa);
		if( !$conn ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Erro ao tentar estabelecer conexao com o banco de dados externo'
			));
		}
		$query = 'SELECT '.$this->baseConexaoComplexa['fields']['nome'].' as nome, '.$this->baseConexaoComplexa['fields']['email'].' as email, '.$this->baseConexaoComplexa['fields']['enviar'].' as enviar FROM '.$this->baseConexaoComplexa['table'].' LIMIT 1;';
		$statement = $conn->prepare($query);
		$response = $statement->execute();
		if( !$response ) {
// 			echo $query;
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Erro ao tentar buscar um registro de exemplo para a base'
			));
		}
		
		// A PARTIR DE AGORA TEMOS UMA CONEXAO FUNCIONAL, PRECISAMOS IMPORTAR OS REGISTROS
		return $this->sincronizar();
	}
	
	/**
	 * Método responsável por iniciar a sincronização dos emails de uma lista
	 * 
	 * Este irá ler todos os emails da lista de referencia e irá cadastrar contatos e vincular a lista
	 * 
	 * @throws \Sh\SheerException
	 * @return boolean|array
	 */
	protected function sincronizar () {
	
		//CAPTURO UMA CONEXAO COM O BANCO ONDE A BASE ORIGINAL ESTA ARMAZENADA
		$connectionExterna = \Sh\DatabaseConnectionProvider::newDatabaseConnection($this->baseConexaoComplexa);

		//BUSCAR OS EMAILS DA LISTA
		//faço n operacoes até se esgotarem os emails
		$esgotado = false;
		$offset = 0; $limit = 500; $page = 1;
		$sincronizacaoInfo = array(
			'total' => 0,
			'novos' => 0,
			'atualizar' => 0
		);

		//VAMOS BUSCAR TODoS OS EMAILS QUE PRECISAMOS IMPORTAR EM LOOPS COM LIMITE POR LOOP DE $limit
		do {
			$query = 'SELECT '.$this->baseConexaoComplexa['fields']['nome'].' as nome, '.$this->baseConexaoComplexa['fields']['email'].' as email, '.$this->baseConexaoComplexa['fields']['enviar'].' as enviar FROM '.$this->baseConexaoComplexa['table'].' LIMIT '.$limit.' OFFSET '.$offset.';';
			$statement = $connectionExterna->prepare($query);
			$response = $statement->execute();

			//caso tenha ocorrido algum erro
			if( !$response ) {
				$pdoError = $statement->errorInfo();
				throw new \Sh\SheerException(array(
					'code' => $pdoError[1],
					'message' => $pdoError[2]
				));
			}
			//esgotaram os emaisl
			else if ( $statement->rowCount() == 0 ) {
				$esgotado = true;
				break;
			}

			//TENDO RESULTADOS, IREI PROCESSA-LOS CADASTRANDO SEUS CONTATOS E OS INSERINDO NA LISTA
			//criando array de retorno
			$emailsAtualizar 	= $statement->fetchAll(\PDO::FETCH_ASSOC);
			/*
			 * PROCESSAR TODoS OS EMAILS ENCONTRADOS E 
			 * 		- registrar um contato para este email
			 * 		- registrar a associação do contato com a lista desejada
			 */
			foreach ( $emailsAtualizar as $k=>&$contato ) {
				
				/*
				 * RECUPERANDO/CADASTRANDO CONTATO
				 */
				//DEVO DETERMINAR SE JÁ POSSUO O EMAIL CADASTRADO
				$statement = $this->connection->prepare('SELECT * FROM sh_ml_contato WHERE email = "'.$contato['email'].'" LIMIT 1');
				$responseSql = $statement->execute();
				if( !$responseSql ) {
					throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Erro na query de buscar contato'
					));
				}
				
				//Se encontrei um registro previamente cadastrado com aquele email
				if( $statement->rowCount() == 1 ) {
					$contato = $statement->fetch(\PDO::FETCH_ASSOC);
					$contato['idContato'] = $contato['id'];
				}
				//se não encontrei nenhum registro irei adicionar um novo contato
				else {
					$contato['idContato'] = \Sh\Library::getUniqueId();
					$nomeStr = (isset($data['nome']) && $data['nome'] ) ? ('"'.$data['nome'].'"') : ('NULL');
					$query = 'INSERT INTO sh_ml_contato VALUES("'.$contato['idContato'].'", '.$nomeStr.', "'.$contato['email'].'" );';
					$responseSql = $this->connection->exec($query);
					if( !$responseSql ) {
						throw new \Sh\SheerException(array(
								'code' => null,
								'message' => 'Erro ao registrar contato'
						));
					}
				}
				
				/*
				 * Vincular o contato a lista de emails
				 */
				//Realizando a importação do contato
				//DEVO DETERMINAR SE JÁ POSSUO O EMAIL CADASTRADO
				$statement = $this->connection->prepare('SELECT * FROM sh_ml_listaEmail WHERE idContato = "'.$contato['idContato'].'" AND idLista="'.$this->lista['id'].'" LIMIT 1');
				$responseSql = $statement->execute();
				if( !$responseSql ) {
					throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Erro na query de buscar vinculo contatoXlista'
					));
				}
				
				//Incrementando o total de contatos operados
				$sincronizacaoInfo['total']++;
				
				//Se encontrei um vinculo previamente cadastrado devo atualiza-lo setando para enviar
				if( $statement->rowCount() == 1 ) {
					$vinculo = $statement->fetch(\PDO::FETCH_ASSOC);
					$query = 'UPDATE sh_ml_listaEmail SET enviar=1 WHERE idContato = "'.$contato['idContato'].'" AND idLista="'.$this->lista['id'].'" ;';
					$responseSql = $this->connection->exec($query);
					if( $responseSql === null ) {
						echo $query;
						throw new \Sh\SheerException(array(
								'code' => null,
								'message' => 'Erro ao atualizar vinculo contatoXlista'
						));
					}
					$sincronizacaoInfo['atualizar']++;
				}
				//se não encontrei nenhum vinculo devo cadastralo
				else {
					$idVinculo = \Sh\Library::getUniqueId();
					$query = 'INSERT INTO sh_ml_listaEmail VALUES("'.$idVinculo.'", "'.$this->lista['id'].'", "'.$contato['idContato'].'", 1, NOW(), NULL);';
					$responseSql = $this->connection->exec($query);
					if( $responseSql === null ) {
						throw new \Sh\SheerException(array(
								'code' => null,
								'message' => 'Erro ao registrar vinculo contatoXlista'
						));
					}
					$sincronizacaoInfo['novos']++;
				}
			}
			
			//calculo novo offset
			$offset = $limit * $page;
			++$page;
				
		} while ( !$esgotado );
		
		//RECALCULAR O TOTAL DE EMAILS E HABILITADOS DA LISTA
		$response = \Sh\ContentActionManager::doAction('malaDiretaLista/recalcularTotalHabilitados', ['id'=>$this->lista['id']], $this->connection);
		\Sh\Library::actionResponseCheck($response);

		//CRIANDO OBJETO DE SINCRONIZAÇÃO
		$listaSincronizacao = array(
			'idLista' 		=> $this->lista['id'],
			'total' 		=> $sincronizacaoInfo['total'],
			'novos' 		=> $sincronizacaoInfo['novos'],
			'atualizados' 	=> $sincronizacaoInfo['atualizar']
		);
		$response = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaListaSincronizacao_add', $listaSincronizacao, $this->connection);
		
		return $response;
	}
	
	/**
	 * 
	 * Método para?
	 * 
	 * @param unknown $listaTmp
	 * @return multitype:number 
	 */
	protected function sincRegistros ( &$listaTmp ) {
	
		//criando array de retorno
		$retorno = array(
			'total' => 0,
			'novos' => 0,
			'atualizar' => 0
		);
	
		//caso de lista vazia
		if( !is_array($listaTmp) ) {
			return $retorno;
		}
		
		//
		//DETERMINAR QUAIS JÁ SÃO CONTATOS REGISTRADOS
		/*
		 * PROCESSAR TODoS OS EMAILS DECLARADOS E 
		 * 		- registrar um contato para este email
		 * 		- registrar a associação do contato com a lista desejada
		 */
		foreach ( $listaTmp as $k=>&$contato ) {
			
			//Realizando a importação do contato
			$responseContato = \Sh\ContentActionManager::doAction('malaDiretaContato/malaDiretaContato_add', $contato, $this->connection);
			\Sh\Library::actionResponseCheck($responseContato);
			
			//Reinserindo informações de contato no array
			$contato['idContato'] = $responseContato['data']['id'];
			
			//VINCULANDO O CONTATO A LISTA DESEJADA
			$vincularLista = array();
			$vincularLista['idLista'] = $this->lista['id'];
			$vincularLista['idContato'] = $contato['idContato'];
			$responseVinculo = \Sh\ContentActionManager::doAction('malaDiretaListaEmail/adicionarContato', $vincularLista, $this->connection);
			\Sh\Library::actionResponseCheck($responseVinculo);
			
			//Incrementando o total de contatos operados
			$retorno['total']++;
			if( $responseVinculo['data']['operacao'] == 'adicionado' ) {
				$retorno['novos']++;
			}
			else if ( $responseVinculo['data']['operacao'] == 'atualizado' ) {
				$retorno['atualizar']++;
			}
		}
		
		//AGORA PRECISO INSERIR O CONTATO NA LISTA
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	
		//PROCESSAR LISTA E DEFINIR REGISTROS PRÉ-EXISTENTES
		//preciso gerar uma query que vá buscar até X emails
		$preExistentes = array();
		$contador = 0; $maximoEmailsPorQuery = 100;
		$sqlWhere = '';
		foreach ( $listaTmp as $k=>$contato ) {
	
			$contador++;
	
			$listaTmp[$k]['email'] = trim($contato['email']);
			$listaTmp[$k]['enviar'] = ($contato['enviar'] == $this->baseConexaoComplexa['fields']['enviarValor']) ? 1 : 2;
	
			//gerando a query
			if( $sqlWhere ) { $sqlWhere .= ', '; }
			$sqlWhere .= '"'.$listaTmp[$k]['email'].'"';
	
			//QUANDO TIVERMOS X EMAILS PROCESSADOS, EFETUAMOS A BUSCA
			if( $contador == $maximoEmailsPorQuery ) {
				//efetuando query de busca
				$query = 'SELECT * FROM sh_ml_listaEmail WHERE email IN ('.$sqlWhere.') AND idLista="'.$this->idLista.'";';
				$st = $this->connection->prepare($query);
				$response = $st->execute();
				if( !$response ) {
					\Sh\LoggerProvider::log('full', 'Erro ao tentar sincronizar lista de emails"');
					continue;
				}
				//gerando a lista de preExistentes
				while ( $row = $st->fetch(\PDO::FETCH_ASSOC) ) {
					$preExistentes[$row['email']] = [
							'email' => $row['email'],
							'nome' => $row['nome'],
							'enviar' => $row['enviar']
					];
				}
	
				//zerando controles
				$contador = 0;
				$sqlWhere = '';
			}
		}
		//EFETUANDO A BUSCA PARA OS EMAILS QUE SOBRARAM ANTES DA ULTIMA CONVERSAO PARA O MÁXIMO
		//efetuando query de busca
		if( $contador > 0 ) {
			$query = 'SELECT * FROM sh_ml_listaEmail WHERE email IN ('.$sqlWhere.') AND idLista="'.$this->idLista.'";';
			$st = $this->connection->prepare($query);
			$response = $st->execute();
			if( !$response ) {
				\Sh\LoggerProvider::log('full', 'Erro ao tentar sincronizar lista de emails"');
				continue;
			}
			//gerando a lista de preExistentes
			while ( $row = $st->fetch(\PDO::FETCH_ASSOC) ) {
				$preExistentes[$row['email']] = [
						'email' => $row['email'],
						'nome' => $row['nome'],
						'enviar' => $row['enviar']
				];
			}
		}
	
	
	
		//JÁ TENDO A LISTA DE PRE-EXISTENTES VAMOS DEFINIR O QUE DEVEMOS FAZER COM OS REGISTROS RECEBIDOS PARA SINC
		$queryAdicionar = '';
		foreach ($listaTmp as $contato) {
	
			//somando no controlador geral
			$retorno['total']++;
	
			//VERIFICANDO SE ELE JÁ ESTÁ ADICIONADO PARA ADICIONAR OU ATUALIZAR
			//nao existe ainda, vou adicionar
			if( !isset( $preExistentes[$contato['email']] ) ){
				$id = \Sh\Library::getUniqueId();
	
				//GERANDO A QUERY
				if( strlen($queryAdicionar) == 0 ) { $queryAdicionar = 'INSERT INTO sh_ml_listaEmail VALUES '; }
				else { $queryAdicionar .= ', '; }
				$queryAdicionar .= '("'.$id.'", "'.$this->idLista.'", '.\Sh\DatabaseLibrary::getStatement($contato['nome']).', "'.$contato['email'].'", "'.$contato['enviar'].'", NOW(), NULL)';
	
				//somando no controlador geral
				$retorno['novos']++;
			}
			//atualizar
			else {
				$tmp = $preExistentes[$contato['email']];
	
				//TROCA DE NOME SE ATUALIZADO
				$nomeSql = '';
				if( $tmp['nome'] == null && $contato['nome'] && strlen($contato['nome']) > 1 ) {
					$nomeSql = ', "'.$contato['nome'].'"';
				}
	
				//se os valores de "enviar" forem iguais, ou o usuário já estiver desmarcado nem opero este cara
				if( $tmp['enviar'] == $contato['enviar'] || $contato['enviar'] == 2 ) {
					continue;
				}
				//determinando o enviar para esse cara já cadastrado
				else if( $tmp['enviar'] == '2' ) {
					$contato['enviar'] = 2;
				}
	
				//TODO existia um post-it falando sobre um problema na casa que estava undefined, mas acredito que estava errado
				$query = 'UPDATE sh_ml_listaEmail SET enviar="'.$contato['enviar'].'" '.$nomeSql.'  WHERE id="'.$tmp['id'].'";';
				$response = $this->connection->exec($query);
				if( !$response ) {
					var_dump('3');
					\Sh\LoggerProvider::log('full', 'Erro ao tentar inserir na lista de emails o registro email="'.$contato['email'].'", lista="'.$this->lista['nome'].'"');
					continue;
				}
				//somando no controlador geral
				$retorno['atualizar']++;
			}
	
		}
	
		//CADASTRANDO TODoS OS USUÁRIOS NOVOS
		if( strlen($queryAdicionar) > 0 ) {
			$response = $this->connection->exec($queryAdicionar);
			if( !$response ) {
				var_dump('4');
				\Sh\LoggerProvider::log('full', 'Erro ao tentar cadastrar a lista de usuários a se adicionar na lista="'.$this->lista['nome'].'"');
			}
		}
	
		return $retorno;
	
	}
	
	
}

/**
 * @author Guilherme
 *
 * Classe de controle para listas complexas
 *
 *
 */
class listaComplexa {

	protected $idLista = null;
	protected $lista = null;
	
	/**
	 * @var \PDO
	 */
	protected $connection = null;

	protected $base = null;

	/**
	 * Método construtor onde iremos validar a lista
	 * @param string $idLista
	 * @param array $baseOriginal
	 * 		driver  	=> (string) Tipo de conexao a ser utilizada [mysql]
	 * 		host		=> (string) endereco do servidor
	 * 		username	=> (string)
	 * 		password	=> (string)
	 * 		database	=> (string)
	 * 		table		=> (string)
	 * 		fields		=> (array)
	 * 			nome		=> (string)
	 * 			enviar		=> (string)
	 * 			email		=> (string)
	 *
	 *
	 * @throws \Sh\SheerException
	 */
	public function __construct( $idLista, $base ) {

		//PROCESSANDO A LISTA
		$this->idLista = $idLista;
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $idLista);
		if( !$lista ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Lista de emails inválida para sincronização'
			));
		}
		$this->lista = &$lista;

		
		//gravando base
		$this->baseConexaoComplexa = $base;

	}

	

	

}