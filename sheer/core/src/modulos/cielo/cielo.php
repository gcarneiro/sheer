<?php

namespace Sh\Modules\cielo;

abstract class cielo {
	
	/*
	 * Status para mapeamento das transações
	 */
	static public $statusTransacao = array(
		1 => 'Aguardando Pagamento',
		2 => 'Aguardando Processamento',
		3 => 'Concretizada',
		4 => 'Negada',
		5 => 'Cancelada',
		6 => 'Aguardando Cancelamento'
	);
	
	/*
	 * Status controlados pela cielo
	 */
	static public $statusCielo = array(
		0 => 'Criada',
		1 => 'Em Andamento',
		2 => 'Autenticada',
		3 => 'Não Autenticada',
		4 => 'Autorizada',
		5 => 'Não autorizada',
		6 => 'Capturada',
		
		9 => 'Cancelada',
		10 => 'Em Autenticação',
		
		12 => 'Em Cancelamento'
	);
	
	static public $modalidade = array(
		1 => 'Débito',
		2 => 'Crédito a Vista',
		3 => 'Crédito a Prazo'
	);
	
	static public $bandeira = array(
		'visa' 			=> 'Visa',
		'mastercard' 	=> 'MasterCard',
		'diners' 		=> 'Dinners',
		'discover' 		=> 'Discover',
		'elo' 			=> 'Elo',
		'amex' 			=> 'American Express',
		'jcb' 			=> 'JCB',
		'aura' 			=> 'Aura'
	);
	
	static public $modalidadeComunicacao = array(
		1 => 'Buy Page Cielo',
		2 => 'Buy Page Loja'
	);
	
	/**
	 * Status da cielo para regularização do token de cartao
	 * @var array
	 */
	static public $tokenStatus = array(
		0 => 'Bloqueado',
		1 => 'Desbloqueado'
	);
	
}

/**
 * @author Guilherme
 *
 * Classe responsável por controlar os eventos advindos da cielo
 * Este irá escutar as mudanças pelo 
 * 		Job\Sincronizar, ActionHandler\Sincronizar
 * 
 * Este irá receber os eventos 
 * 		"novo" => Evento disparado quando um novo pedido tiver sido criado
 * 		"sincronizado" => 
 */
abstract class cieloEventos {
	
	use \Sh\EventDrivenBehavior;
	
	static public function trigger ($event, \PDO $conn = null, $transacao=null) {
		self::executeTrigger($event, $conn, $transacao);
	}
	
}

/**
 * @author Guilherme
 * 
 * Criação de transação financeira com a CIELO, Precisaremos das seguintes informações
 * 		OBRIGATORIOS
 *		-> localizador
 *		-> bandeira
 *		-> valor
 *		-> modalidadeComunicacao 
 *		Se for buyPageCielo
 *			-> linkRetorno
 *		Se for buyPageLoja sem token
 *			-> portadorNumero
 *			-> portadorSeguranca
 *			-> portadorValidade
 *			-> portadorNome
 *		Se for buyPageLoja com token
 *			-> portadorToken
 *
 *		OPCIONAIS
 *		-> loja [assumo default]
 * 		-> parcelas [assumo 1]
 * 		-> modalidade [assumo credito a vista]
 * 		-> dataAuxiliar [aceita string, se enviado array converte para json]
 * 		-> portadorId [caso não seja enviado assumo o id da pessoa logada]
 * 		-> portadorRegistrar [deve ser enviado com valor 1 para ser valido]
 * 				Este irá determinar se devemos armazenar o cartão para futuras transações
 *
 */
class criarTransacao extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//BUSCANDO INFORMAÇÕES DA LOJA A SE UTILIZAR
		$idLoja = null;
		if( isset($data['loja']) && \Sh\CieloLojas::isLoja($data['loja']) ) {
			$idLoja = $data['loja'];
		}
		$lojaCielo = \Sh\CieloLojas::getLoja($idLoja);
			
		//se eu não tiver enviado linkRetorno, assumo o da loja
		if( !isset($data['linkRetorno']) || !$data['linkRetorno'] && $lojaCielo['linkRetorno'] ) {
			$data['linkRetorno'] = $lojaCielo['linkRetorno'];
		}
			
		//TRATANDO OS DADOS PARA A TRANSACAO
		$transacao = self::getConfiguracaoNovaTransacao($data);
		//limpando dados padrões
		$transacao['id'] 				= \Sh\Library::getUniqueId();
		$transacao['idCliente']			= $transacao['portadorId'];
		$transacao['loja'] 				= $idLoja;
		$transacao['ordem']				= \Sh\Library::getUniqueIntegerCode();
		$transacao['tid']				= null;
		$transacao['linkPagamento'] 	= null;
		$transacao['status']			= null;
		
		//CRIANDO TRANSACAO COM A CIELO
		//caso seja buyPageCielo
		if( $transacao['modalidadeComunicacao'] == 1 ) {
			$transacao = \Sh\Cielo::novaTransacao($transacao, $this->connection);
		}
		//setando por padrao o buyPageLoja
		else {
			$transacao = \Sh\Cielo::novaTransacaoLoja($transacao, $this->connection);
		}
		
		//REGISTRANDO TRANSACAO EM BANCO
		$response = \Sh\ContentActionManager::doAction('cielo/transacao_add', $transacao, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//PROCESSAR TOKEN PARA ARMAZENAMENTO DE CARTAO - Somente se for modalidade buyPageLoja
		if( $transacao['modalidadeComunicacao'] == 2 && $transacao['tokenInfo'] ) {
			
			//Verificando se devemos armazenar o cartão
			if( isset($data['portadorRegistrar']) && $data['portadorRegistrar']==1 ) {
				
				//Buscando token previamente cadastrado
				$tokenAnterior = \Sh\ContentProviderManager::loadContentById('cielo/cartaoToken', $transacao['tokenInfo']['codigo']);
				if( !$tokenAnterior ) {
					$cartaoToken = array(
						'token'				=> $transacao['tokenInfo']['codigo'],
						'idCliente' 		=> $transacao['idCliente'],
						'nomePortador' 		=> (isset($data['portadorNome'])) ? $data['portadorNome'] : null,
						'cartaoTruncado'	=> $transacao['tokenInfo']['cartaoTruncado'],
						'bandeira'			=> $transacao['bandeira'],
						'validade'			=> $transacao['portadorValidade'],
						'status'			=> $transacao['tokenInfo']['status']
					);
					$responseToken = \Sh\ContentActionManager::doAction('cielo/cartaoToken_add', $cartaoToken, $this->connection);
				}
				else {
					$cartaoToken = array(
						'token'				=> $transacao['tokenInfo']['codigo'],
						'status'			=> $transacao['tokenInfo']['status']
					);
					$responseToken = \Sh\ContentActionManager::doAction('cielo/cartaoToken_update', $cartaoToken, $this->connection);
				}
				
			}
			
		}
		
		//DISPARANDO EVENTO DE CRIACAO
		\Sh\Modules\cielo\cieloEventos::trigger('create', null, $transacao);
		
		return $response;
	}
	
	/**
	 * Processa as informações recebidas por parametro para fabricar um objeto cieloTransacao valido
	 * Este irá determinar se utilizaremos o buyPageLoja ou buyPageCielo
	 * 		Também será responsável por validar os dados do portador
	 * 		Processar e recuperar informações do token
	 *
	 * @param array $data
	 * @throws \Sh\SheerException
	 * @return objeto cieloTransacao
	 */
	static protected function getConfiguracaoNovaTransacao ( $data ) {
	
		//PRECISO VALIDAR OS CAMPOS MINIMAMENTE NECESSÁRIOS
		{
			//Verificando se o cliente optou pela utilização de token de pagamento
			$token = null;
			if( isset($data['portadorToken']) ) {
				$token = \Sh\ContentProviderManager::loadContentById('cielo/cartaoToken', $data['portadorToken']);
				if( !$token || $token['status']==0 ) {
					throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Token inválido/bloqueado para efetuar compras'
					));
				}
				$data['idToken'] = $token['token'];
			}
			
			//localizador
			if( !isset( $data['localizador'] ) || strlen( $data['localizador'] ) < 3 ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Localizador do pedido de pagamento é inválido ou inexistente'
				));
			}
	
			//bandeira
			if( !isset($data['bandeira']) || !isset(\Sh\Modules\cielo\cielo::$bandeira[$data['bandeira']]) ) {
				//Verifico se temos token para capturar a informação dele
				if( $token ) {
					$data['bandeira'] = $token['bandeira'];
				}
				else {
					throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Bandeira de pagamento é inválida'
					));
				}
			}
	
			//valor
			if( !isset($data['valor']) ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Valor para pagamento não enviado'
				));
			}
			else {
				$valorFinal = \Sh\FieldDinheiro::formatInputDataToPrimitive($data['valor']);
				if( $valorFinal === null ) {
					throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Valor para pagamento enviado é inválido'
					));
				}
				if( $valorFinal <= 0 ) {
					throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Valor para pagamento deve ser um valor positivo'
					));
				}
			}
			
			//DETERMINANDO COMPRADOR
			if( !isset($data['portadorId']) ) {
				\Sh\LoggerProvider::log('sheer', 'Assumindo usuário logado como dono da transação');
				$data['portadorId'] = \Sh\AuthenticationControl::getAuthenticatedUserInfo('id');
			}
			//Se tivermos token, devemos validar que a mesma pessoa que possui o token está utuilizando o cartao
			//FIXME Devemos verificar aqui caso existam compras automaticas pelo sistema pois esta condição irá falhar
			if( $token && $token['idCliente']!=$data['portadorId']  ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Este cartão não pertence ao usuário que está tentando efetivar a compra.'
				));
			}
			
			//DETERMINANDO QUAL A MODALIDADE DE COMUNICACAO E EFETUANDO CALCULOS A PARTIR DELA
			//sem determinação pelo desenvolvedor
			if( !isset($data['modalidadeComunicacao']) ) {
				$data['modalidadeComunicacao'] = 1;
			}
			else if ( !in_array($data['modalidadeComunicacao'], [1,2]) ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Modalidade de comunicação inválida para esta transação'
				));
			}
			
			//PAGAMENTO PELA CIELO
			if( $data['modalidadeComunicacao'] == 1 ) {
				
				//Validando link retorno
				if( !isset($data['linkRetorno']) || !$data['linkRetorno'] ) {
					throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Link de retorno é inválido'
					));
				}
				
			}
			//PAGAMENTO PELA LOJA, NECESSSÁRIO AS INFORMAÇÕES DO PORTADOR
			else {
				
				//Utilizando token, sem informações do portador
				if( $token ) {
					$data['portadorToken'] = $token['token'];
					$data['portadorValidade'] = $token['validade'];
				}
				//Não possui token, novo cartao
				else {
					//nome do portador
					if( !isset($data['portadorNome']) ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Nome impresso no cartão é obrigatório'
						));
					}
					else if ( strlen($data['portadorNome']) < 5 || strlen($data['portadorNome']) > 50 ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Nome impresso no cartão é inválido'
						));
					}
					
					//número do cartao
					if( !isset($data['portadorNumero']) ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Número do cartão não foi enviado'
						));
					}
					else if ( strlen($data['portadorNumero']) < 13 || strlen($data['portadorNumero']) > 16 ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Número do cartão é inválido'
						));
					}
					
					//Validade do cartao
					if( !isset($data['portadorValidade']) ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Validade do cartão não foi enviada'
						));
					}
					else if ( strlen($data['portadorValidade'])!=7 || strpos($data['portadorValidade'], '/')!=2 ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Validade do cartão é inválida'
						));
					}
					//processando a data
					else {
						$tmp = explode('/', $data['portadorValidade']);
						$data['portadorValidade'] = $tmp[1].$tmp[0];
					}
					
					//Código de segurança
					if( !isset($data['portadorSeguranca']) ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Código de segurança do cartão não foi enviado'
						));
					}
					else if ( strlen($data['portadorSeguranca'])!=3 ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Código de segurança do cartão é inválido'
						));
					}
					
				}
				
				//Gerando link de retorno generico
				if( !isset($data['linkRetorno']) || !$data['linkRetorno'] ) {
					$data['linkRetorno'] = \Sh\RuntimeInfo::getBaseUrl();
				}
				
			}
			
		}
			
		//DETERMINANDO VALORES OPCIONAIS
		//dados auxiliares
		if( isset($data['dadoAuxiliar']) && is_array($data['dadoAuxiliar']) ) {
			$data['dadoAuxiliar'] = json_encode($data['dadoAuxiliar']);
		} 
		else if ( isset($data['dadoAuxiliar']) ) {
			$data['dadoAuxiliar'] = (string) ($data['dadoAuxiliar']);
		}
		
		//parcelas
		if( !isset($data['parcelas']) ) {
			$data['parcelas'] = 1;
		}
		else if( (integer) $data['parcelas'] < 1 ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'O número de parcelas para o pagamento deve ser maior que 1x.'
			));
		}
			
		//modalidade
		//se a modalidade não estiver setada vamos assumir Crédito. E determinaremos se avista ou parcelado conforme o numero de parcelas
		if( !isset($data['modalidade']) ) {
			if( $data['parcelas'] == 1 ) {
				$data['modalidade'] = 2;
			}
			else {
				$data['modalidade'] = 3;
			}
		}
		else {
			$data['modalidade'] = (integer) $data['modalidade'];
			//tendo modalidade cadastrada faremos as travas de seguranca
			//debito com mais de uma parcela não é permitido
			if( $data['modalidade'] == 1 && $data['parcelas'] > 1 ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'A modalidade débito não aceita parcelamento.'
				));
			}
			else if ( $data['modalidade'] == 3 && $data['parcelas'] == 1 || $data['modalidade'] == 2 && $data['parcelas'] > 1 ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Configurações de crédito parcelado e parcelas de pagamento são inválidas'
				));
			}
		}
		
		return $data;
	
	}
	
}

/*
 * ActionHandler para sincronizar a transacao Sheer com as informações da CIELO
 */
class sincronizarTransacao extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//capturando a transação
		$transacao = \Sh\ContentProviderManager::loadContentById('cielo/transacao', $data['id'], $this->connection);
		if( !$transacao ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Transação não encontrada'
			));
		}
		
		//efetuando chamada para a cielo
		$respostaCielo = \Sh\Cielo::sincronizarTransacao($transacao['tid']);
		
		//verifico se houve alteração do status, se não acontecer passo para outro
		if( $respostaCielo['status'] == $transacao['status'] ) {
			//FIXME devo verificar como devo proceder
			return array(
				'status' => true,
				'code' => null,
				'message' => null,
				'data' => $transacao
			);
		}
		
		//GUARDANDO INFORMAÇÃO PARA O TRANSAÇÃOSINCRONIZAÇÃO
		$transacaoSincronizacaoLog = array(
			'idTransacao' => $transacao['id'],
			'statusAnterior' => $transacao['status'],
			'statusAtual' => $respostaCielo['status']
		);
		
		
		//ATUALIZO A TRANSAÇÃO E DOU O TRIGGER NELA
		$transacaoAtualizar = array();
		$transacaoAtualizar['id'] = $transacao['id'];
		$transacaoAtualizar['status'] = $respostaCielo['status'];
		$transacaoAtualizar['statusCielo'] = $respostaCielo['statusCielo'];
		$response = \Sh\ContentActionManager::doAction('cielo/transacao_update', $transacaoAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($response);
			
		//ATUALIZANDO OBJETO ORIGINAL DA TRANSACAO
		$transacao['status'] = $transacaoAtualizar['status'];
		//TODO ARRANCAR ISSO DAQUI E PASSAR A UTILIZAR O DADOAUXILIAR
		if(isset($data['tipo'])){
			$transacao['tipo'] = $data['tipo'];
		}
		
		//INSERINDO A ATUALIZAÇÃO DA TRANSAÇÃO NO TRANSAÇÃOSINCRONIZAÇÃO
		$responseCieloSincronizacao = \Sh\ContentActionManager::doAction('cielo/transacaoSincronizacao_add', $transacaoSincronizacaoLog, $this->connection);
		\Sh\Library::actionResponseCheck($responseCieloSincronizacao);
		
		$transacao['sincronia'] = $responseCieloSincronizacao['data'];
		
		\Sh\Modules\cielo\cieloEventos::trigger('sincronizado', $this->connection, $transacao);
		
		//retorno
		return $response;
		
	}
	
}

/*
 * Método para sincronizar as transações em aberto
 */
class sincronizarTransacoes extends \Sh\GenericJob {
	
	//Importando trait de orientacao a eventos
	use \Sh\EventDrivenBehavior;
	
	public function run () {
		
		//BUSCO AS TRANSACOES QUE ESTÃO AGUARDANDO
		$transacoesStatus = array(1, 2, 6);
		$transacoes = \Sh\ContentProviderManager::loadContent('cielo/transacao_lista', array('status'=>$transacoesStatus));
		
		if( $transacoes['total'] > 0 ) {
			
			//CONEXAO COM O BANCO
			$connection = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
			
			//SINCRONIZO TODAS AS TRANSACOES
			foreach ( $transacoes['results'] as $idTransacao=>$transacao ) {
				
				//buscar informações com a cielo
				$respostaCielo = \Sh\Cielo::sincronizarTransacao($transacao['tid'], $transacao['loja']);
				
				//verifico se houve alteração do status, se não acontecer passo para outro
				if( $respostaCielo['status'] == $transacao['status'] ) {
					//FIXME devo verificar como devo proceder
					continue;
				}
				
				try {
					
					//GUARDANDO INFORMAÇÃO PARA O TRANSAÇÃOSINCRONIZAÇÃO
					$transacaoSincronizacaoLog = array(
						'idTransacao' => $transacao['id'],
						'statusAnterior' => $transacao['status'],
						'statusAtual' => $respostaCielo['status']
					);
					
					//ATUALIZO A TRANSAÇÃO E DOU O TRIGGER NELA
					$transacaoAtualizar = array();
					$transacaoAtualizar['id'] = $transacao['id'];
					$transacaoAtualizar['status'] = $respostaCielo['status'];
					$response = \Sh\ContentActionManager::doAction('cielo/transacao_update', $transacaoAtualizar, $connection);
					\Sh\Library::actionResponseCheck($response);
					
					//INSERINDO A ATUALIZAÇÃO DA TRANSAÇÃO NO TRANSAÇÃOSINCRONIZAÇÃO
					$responseCieloSincronizacao = \Sh\ContentActionManager::doAction('cielo/transacaoSincronizacao_add', $transacaoSincronizacaoLog, $connection);
					\Sh\Library::actionResponseCheck($responseCieloSincronizacao);
					
					//OCORRENDO CERTO COMMITO A TRANSACAO
					$connection->commit();
					$connection->beginTransaction();
					
					//ATUALIZANDO OBJETO ORIGINAL DA TRANSACAO
					$transacao['status'] = $transacaoAtualizar['status'];
					\Sh\Modules\cielo\cieloEventos::trigger('sincronizado', null, $transacao);
					
				}
				catch (\Sh\SheerException $e) {
					//TODO O QUE DEVO FAZER QUANDO ESTE FALHAR?
					$connection->rollBack();
					$connection->beginTransaction();
				}
				
			}
			
			//SEMPRE FINALIZO COMMITANDO A ULTIMA TRANSACAO VIGENTE
			$connection->commit();
		}
		
		
	}
	
}





































