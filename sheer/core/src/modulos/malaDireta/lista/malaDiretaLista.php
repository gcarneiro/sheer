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

class sincronizarLista extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//CARREGANDO A LISTA
		$lista = \Sh\ContentProviderManager::loadContentById('malaDiretaLista/malaDiretaLista', $data['id']);
		if( !$lista ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Lista inválida para recalcular seus totais'
			));
		}
		
		//verificando que é uma lista complexa
		if( $lista['tipo'] != 2 ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'A sincronia só é permitida para listas complexas'
			));
		}
		
		if( $lista['password']===NULL ) {
			$lista['password'] = '';
		}
		
		//gerando array base 
		$baseListaComplexa = [
			'driver' 	=> 'mysql',
			'host' 		=> $lista['host'],
			'username' 	=> $lista['username'],
			'password' 	=> $lista['password'],
			'database' 	=> $lista['databaseName'],
			'table'		=> $lista['databaseTable'],
			'fields'	=> [
				'nome'			=> $lista['fieldNome'],
				'email'			=> $lista['fieldEmail'],
				'enviar'		=> $lista['fieldEnviar'],
				'enviarValor'	=> $lista['fieldEnviarValor']
			]
		];
		//SINCRONIZANDO LISTA
		$sincroniaBase = new \Sh\Modules\malaDiretaLista\listaComplexa($lista['id'], $baseListaComplexa);
		$responseSincronia = $sincroniaBase->sincronizar($this->connection);
		
		if( !$responseSincronia ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Erro inesperado ao realizar sincronização da lista'
			));
		}
		
		return [
			'status' => true,
			'message' => null,
			'code' => null,
			'data' => [
				'id' => $lista['id'],
				'total' => $responseSincronia['total'],	
				'novos' => $responseSincronia['novos'],	
				'atualizados' => $responseSincronia['atualizar']
			]
		];
		
	}
	
	
}

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

		//PROCESSANDO AS CONFIGURAÇÕES
		//validando os campos da conexao
		if( !isset( $base['host'] ) || !isset( $base['username'] ) || !isset( $base['password'] ) || !isset( $base['database'] ) || !isset( $base['table'] ) ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Informações incompletas sobre a base de conexão'
			));
		}
		//validando os campos dos dados
		if( !isset( $base['fields'] ) ) {
			$base['fields'] = [
				'nome' => 'nome',
				'email' => 'email',
				'enviar' => 'enviar'
			];
		}
		else {
			if( !isset($base['fields']['nome']) ) 		{ $base['fields']['nome'] = 'nome'; }
			if( !isset($base['fields']['email']) ) 		{ $base['fields']['email'] = 'email'; }
			if( !isset($base['fields']['enviar']) ) 	{ $base['fields']['enviar'] = 'enviar'; }
		}
		//testando a busca por um registro
		$conn = \Sh\DatabaseConnectionProvider::newDatabaseConnection($base);
		if( !$conn ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Erro ao tentar estabelecer conexao com o banco de dados externo'
			));
		}
		$query = 'SELECT '.$base['fields']['nome'].' as nome, '.$base['fields']['email'].' as email, '.$base['fields']['enviar'].' as enviar FROM '.$base['table'].' LIMIT 1;';
		$statement = $conn->prepare($query);
		$response = $statement->execute();
		if( !$response ) {
			echo $query;
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Erro ao tentar buscar um registro de exemplo para a base'
			));
		}
		//gravando base
		$this->base = $base;

	}

	public function sincronizar ( \PDO $connection = null ) {

		set_time_limit(360);

		$ownConnection = false;

		try {
				
			//dou rollback em qualquer operacao realizada até agora e inicio uma nova transacao
			if( !$connection ) {
				$this->connection = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
				$ownConnection = true;
			}
			else {
				$this->connection = $connection;
			}
				
			//CAPTURO UMA CONEXAO COM O BANCO ONDE A BASE ORIGINAL ESTA ARMAZENADA
			$connectionExterna = \Sh\DatabaseConnectionProvider::newDatabaseConnection($this->base);
				
			//BUSCAR OS EMAILS DA LISTA
			//faço n operacoes até se esgotarem os emails
			$esgotado = true;
			$offset = 0; $limit = 500; $page = 1;
			$sincronizacaoInfo = [
				'total' => 0,
				'novos' => 0,
				'atualizar' => 0
			];
				
			do {
				try {
					$query = 'SELECT '.$this->base['fields']['nome'].' as nome, '.$this->base['fields']['email'].' as email, '.$this->base['fields']['enviar'].' as enviar FROM '.$this->base['table'].' LIMIT '.$limit.' OFFSET '.$offset.';';
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
						
					//TENDO RESULTADO, IREI PROCESSA-LOS
					$emailsAtualizar = $statement->fetchAll(\PDO::FETCH_ASSOC);
					$retorno = self::sincRegistros($emailsAtualizar);
						
					$sincronizacaoInfo['total'] += $retorno['total'];
					$sincronizacaoInfo['novos'] += $retorno['novos'];
					$sincronizacaoInfo['atualizar'] += $retorno['atualizar'];
						
					//verificando se podemos ter mais registros e alterando controles para buscar
					$esgotado = true;
					if( $statement->rowCount() == $limit ) {
						//calculo novo offset
						$offset = $limit * $page;
						//somo mais uma pagina
						++$page;
						//mantenho o loop
						$esgotado = false;
					}
						
				}
				catch (\Sh\SheerException $e) {
					var_dump($e);
					$esgotado = true;
				}
					
			} while ( !$esgotado );
				
			//SINCRONIA FINALIZADA
				
			//RECALCULAR O TOTAL DE EMAILS E HABILITADOS DA LISTA
			$response = \Sh\ContentActionManager::doAction('malaDiretaLista/recalcularTotalHabilitados', ['id'=>$this->idLista], $this->connection);
			\Sh\Library::actionResponseCheck($response);
				
			//CRIANDO OBJETO DE SINCRONIZAÇÃO
			$listaSincronizacao = [
				'idLista' 		=> $this->lista['id'],
				'total' 		=> $sincronizacaoInfo['total'],
				'novos' 		=> $sincronizacaoInfo['novos'],
				'atualizados' 	=> $sincronizacaoInfo['atualizar']
			];
			$response = \Sh\ContentActionManager::doAction('malaDiretaLista/malaDiretaListaSincronizacao_add', $listaSincronizacao, $this->connection);
			\SH\Library::actionResponseCheck($response);
				
			//COMMITANDO CONEXAO
			if( $ownConnection ) {
				$this->connection->commit();
			}
			return $sincronizacaoInfo;
				
		}
		catch (\Sh\SheerException $e) {
				
			if( $ownConnection ) {
				$this->connection->rollBack();
			}
			return false;
		}
	}

	protected function sincRegistros ( $listaTmp ) {

		//criando array de retorno
		$retorno = [
			'total' => 0,
			'novos' => 0,
			'atualizar' => 0
		];

		//caso de lista vazia
		if( !is_array($listaTmp) ) {
			return $retorno;
		}


		//PROCESSAR LISTA E DEFINIR REGISTROS PRÉ-EXISTENTES
		//preciso gerar uma query que vá buscar até X emails
		$preExistentes = array();
		$contador = 0; $maximoEmailsPorQuery = 100;
		$sqlWhere = '';
		foreach ( $listaTmp as $k=>$contato ) {
				
			$contador++;
				
			$listaTmp[$k]['email'] = trim($contato['email']);
			$listaTmp[$k]['enviar'] = ($contato['enviar'] == $this->base['fields']['enviarValor']) ? 1 : 2;
				
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