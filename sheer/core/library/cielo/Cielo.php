<?php

namespace Sh;

abstract class Cielo {
	
	static protected $inicializado = false;
	static protected $cieloWSTeste = 'https://qasecommerce.cielo.com.br/servicos/ecommwsec.do';
	static protected $cieloWSProducao = 'https://ecommerce.cbmp.com.br/servicos/ecommwsec.do';
	static protected $templatesPath = null;
	
	/*
	 * Status para mapeamento das transações
	 * tambem na modulo/cielo
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
	 * Modalidades de pagamento via cartão
	 * tambem na modulo/cielo
	 */
	static public $modalidade = array(
		1 => 'Débito',
		2 => 'Crédito a Vista',
		3 => 'Crédito a Prazo'
	);
	
	/*
	 * Bandeiras aceitas
	 */
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
	
	/**
	 * Método para inicializar as lojas da cielo
	 * @throws \Sh\SheerException
	 */
	static public function init () {
	
		//verificação de inicialização
		if( self::$inicializado ) {
			return;
		}
		self::$inicializado = true;
		
		self::$templatesPath = SH_CORE_PATH.'library/cielo/templates/';
		
		//INICIANDO LOJAS
		\Sh\CieloLojas::init();
		
	}
	
	/**
	 * Método para criar uma nova transação com a cielo
	 * Este modelo utilizará apenas o buyPageCielo
	 * 
	 * @param array[\Sh\cieloTransacao] $transacao 
	 * @param \PDO $connection
	 * @returns  array[\Sh\cieloTransacao]
	 */
	static public function novaTransacao ( $transacao, \PDO $connection=null ) {
		
		//inicializado todo a classe para evitar problemas
		self::init();
		
		try {
			
			//VERIFICANDO A MODALIDADE DE COMUNICACAO
			if( $transacao['modalidadeComunicacao'] != 1 ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Esta função de abertura de transação só aceita comunicação com buyPage Cielo'
				));
			}
			
			//BUSCANDO INFORMAÇÕES DA LOJA
			$idLoja = null;
			if( isset($transacao['loja']) && \Sh\CieloLojas::isLoja($transacao['loja']) ) {
				$idLoja = $transacao['loja'];
			}
			$lojaCielo = \Sh\CieloLojas::getLoja($idLoja);
			
			//VOU GERAR O XML DE REQUISIÇÃO
			$xmlRequisicao = file_get_contents(self::$templatesPath.'requisicao-transacao.xml');
			$xmlRequisicao = self::replaceDataOnTemplate($xmlRequisicao, $transacao);
			
			//ENVIANDO REQUISICAO
			$xmlRetorno = self::enviarRequisicao($xmlRequisicao, $lojaCielo);
			$cieloResposta = simplexml_load_string($xmlRetorno, null, LIBXML_NOERROR);
			//Verificando erro
			self::verificaErroCielo($cieloResposta);
			
			//VERIFICANDO STATUS DA CIELO TRANSACAO, ESTE TEM QUE SER 0 POIS AINDA NÃO TIVEMOS PAGAMENTO
			if( (integer) $cieloResposta->status != 0 ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Resposta de Transação inesperado'
				));
			}
			
			//ATUALIZANDO TRANSACAO
			$strLinkPagamento 				= 'url-autenticacao';
			$transacao['tid']	 			= (string) $cieloResposta->tid;
			$transacao['statusCielo'] 		= (integer) $cieloResposta->status;
			$transacao['status'] 			= self::conversaoStatusCielo( $transacao['statusCielo'] );
			$transacao['linkPagamento'] 	= (string) $cieloResposta->$strLinkPagamento;
			
			//DEVOLVENDO A TRANSACAO CRIADA
			return $transacao;
			
		}
		catch ( \Sh\SheerException $e ) {
			throw $e;
		}
	
	}
	
	/**
	 * Método para criar uma nova transação com a cielo
	 * Este modelo utilizará apenas o buyPageCielo
	 * 
	 * @param array[\Sh\cieloTransacao] $transacao 
	 * @param \PDO $connection
	 * @throws \Sh\SheerException
	 * @returns  array[\Sh\cieloTransacao]
	 */
	static public function novaTransacaoLoja ( $transacao, \PDO $connection=null ) {
		
		//inicializado todo a classe para evitar problemas
		self::init();
		
		try {
			
			//VERIFICANDO A MODALIDADE DE COMUNICACAO
			if( $transacao['modalidadeComunicacao'] != 2 ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Esta função de abertura de transação só aceita comunicação com buyPage Cielo'
				));
			}
			
			//BUSCANDO INFORMAÇÕES DA LOJA
			$idLoja = null;
			if( isset($transacao['loja']) && \Sh\CieloLojas::isLoja($transacao['loja']) ) {
				$idLoja = $transacao['loja'];
			}
			$lojaCielo = \Sh\CieloLojas::getLoja($idLoja);
			
			//VOU GERAR O XML DE REQUISIÇÃO
			//TODO DETERMINAR COM PORTADOR-DADOS E TOKEN
			if( isset($transacao['portadorToken']) && $transacao['portadorToken'] ) {
				$xmlRequisicao = file_get_contents(self::$templatesPath.'requisicao-transacao-buyPageLoja-token.xml');
			}
			else {
				$xmlRequisicao = file_get_contents(self::$templatesPath.'requisicao-transacao-buyPageLoja.xml');
			}
			$xmlRequisicao = self::replaceDataOnTemplate($xmlRequisicao, $transacao);
			
			//ENVIANDO REQUISICAO
			$xmlRetorno = self::enviarRequisicao($xmlRequisicao, $lojaCielo);
			$cieloResposta = simplexml_load_string($xmlRetorno, null, LIBXML_NOERROR);
			
			//VERIFICANDO SE TIVEMOS ERRO
			self::verificaErroCielo($cieloResposta);
			
			//PROCESSANDO TOKEN PARA DEVOLVER JUNTO A TRANSACAO
			$strDadosToken = 'dados-token';
			$strCodigoToken = 'codigo-token';
			$strCartaoTruncado = 'numero-cartao-truncado';
			$tokenInfo = false;
			if( isset($cieloResposta->token) && isset($cieloResposta->token->$strDadosToken) ) {
				$tokenInfo = array();
				$tokenInfo['codigo'] = (string) $cieloResposta->token->$strDadosToken->$strCodigoToken;
				$tokenInfo['status'] = (integer) $cieloResposta->token->$strDadosToken->status;
				$tokenInfo['cartaoTruncado'] = (string) $cieloResposta->token->$strDadosToken->$strCartaoTruncado;
			}
			
			//ATUALIZANDO TRANSACAO
			$transacao['tid'] 			= (string) $cieloResposta->tid;
			$transacao['statusCielo'] 	= (integer) $cieloResposta->status;
			$transacao['status'] 		= self::conversaoStatusCielo( $transacao['statusCielo'] );
			$transacao['tokenInfo']		= $tokenInfo;
			
			//DEVOLVENDO A TRANSACAO CRIADA
			return $transacao;
			
		}
		catch ( \Sh\SheerException $e ) {
			throw $e;
		}
	
	}
	
	/**
	 * Método que irá receber o template de requisição a CIELO e os dados para substituição e irá deveolver o xml final para envio da requisicao
	 * 
	 * @param string $template
	 * @param array $transacao
	 * @return string
	 */
	static protected function replaceDataOnTemplate ($template, $transacao) {
		
		//variaveis de controle
		$moeda = 986;
		$data = date("Y-m-d") . "T" . date("H:i:s");
		$idioma = 'PT';
		
		//capturando o valor
		$valor = \Sh\FieldDinheiro::formatInputDataToPrimitive($transacao['valor']) * 100;
		
		//SUBSTITUINDO CABECALHO
		$template = str_replace(
			array('{{id}}'), 
			array(\Sh\Library::getUniqueId()), 
			$template
		);
		
		//SUBSTITUINDO DADOS DO PEDIDO
		$template = str_replace(
			array('{{pedido.ordem}}', '{{pedido.valor}}', '{{pedido.moeda}}', '{{pedido.data}}', '{{pedido.descricao}}', '{{pedido.idioma}}', '{{pedido.soft-descriptor}}'), 
			array($transacao['ordem'], $valor, $moeda, $data, '', $idioma, ''), 
			$template
		);
		
		//SUBSTITUINDO FORMA DE PAGAMENTO
		$template = str_replace(
			array('{{pagamento.bandeira}}', '{{pagamento.produto}}', '{{pagamento.parcelas}}'), 
			array($transacao['bandeira'], self::conversaoModalidadeProduto($transacao['modalidade']), $transacao['parcelas']), 
			$template
		);
		
		//SUBSTITUINDO DADOS DO PORTADOR
		$transacao['portadorNome'] 			= (isset($transacao['portadorNome'])) ? $transacao['portadorNome'] : null;
		$transacao['portadorNumero'] 		= (isset($transacao['portadorNumero'])) ? $transacao['portadorNumero'] : null;
		$transacao['portadorValidade'] 		= (isset($transacao['portadorValidade'])) ? $transacao['portadorValidade'] : null;
		$transacao['portadorSeguranca'] 	= (isset($transacao['portadorSeguranca'])) ? $transacao['portadorSeguranca'] : null;
		$transacao['portadorToken'] 		= (isset($transacao['portadorToken'])) ? $transacao['portadorToken'] : null;
		$template = str_replace(
			array('{{portador.nome}}', '{{portador.numero}}', '{{portador.validade}}', '{{portador.indicador}}', '{{portador.seguranca}}', '{{portador.token}}'), 
			array($transacao['portadorNome'], $transacao['portadorNumero'], $transacao['portadorValidade'], 1, $transacao['portadorSeguranca'], $transacao['portadorToken']), 
			$template
		);
		
		//SUBSTITUINDO GERAR-TOKEN
		$template = str_replace(
			array('{{transacao.gerarToken}}'), 
			array('<gerar-token>true</gerar-token>'), 
			$template
		);
		
		//SUBSTITUINDO INFORMAÇÕES ADICIONAIS
		//TODO rever o "autorizar" e "capturar"
		$template = str_replace(
			array('{{transacao.urlRetorno}}', '{{transacao.autorizar}}', '{{transacao.capturar}}'), 
			array(urlencode($transacao['linkRetorno']), 3, true), 
			$template
		);
		
		return $template;
		
	}
	
	/**
	 * Método para atualizar o status da transacao a partir do seu tid
	 * 
	 * @param string $tid TID da cielo
	 * @param array $idLoja [identificador da loja no sistema]
	 * @return array [ tid, ordem, status ]
	 */
	static public function sincronizarTransacao ( $tid, $idLoja=null ) {
		
		//capturando a loja
		$lojaCielo = \Sh\CieloLojas::getLoja($idLoja);
		
		//VOU GERAR O XML DE REQUISIÇÃO
		$xmlRequisicao = file_get_contents(self::$templatesPath.'requisicao-consulta.xml');
		
		//SUBSTITUINDO CABECALHO
		$xmlRequisicao = str_replace(
			array('{{id}}'), 
			array(\Sh\Library::getUniqueId()), 
			$xmlRequisicao
		);
		
		//SUBSTITUINDO INFORMAÇÕES ADICIONAIS
		$xmlRequisicao = str_replace(
			array('{{transacao.tid}}'), 
			array($tid), 
			$xmlRequisicao
		);
		
		//ENVIANDO REQUISICAO
		$xmlRetorno = self::enviarRequisicao($xmlRequisicao, $lojaCielo);
		$cieloResposta = simplexml_load_string($xmlRetorno, null, LIBXML_NOERROR);
		self::verificaErroCielo($cieloResposta);
		
		$strDadosPedido = 'dados-pedidos';
		
		//MONTANDO ARRAY DE RESPOSTA
		$resposta = array();
		$resposta['tid'] = $tid;
		$resposta['ordem'] = (string) $cieloResposta->$strDadosPedido->numero;
		$resposta['statusCielo'] = (integer) $cieloResposta->status;
		$resposta['status'] = self::conversaoStatusCielo($resposta['statusCielo']);
		
		return $resposta;
	}
	
	/**
	 * Método para cancelar uma transação com a CIELO
	 * 
	 * @param string $tid TID da cielo
	 * @param array $idLoja [identificador da loja no sistema]
	 * @return array [ tid, ordem, status ]
	 */
	static public function cancelarTransacao ( $tid, $idLoja=null ) {
		
		//capturando a loja
		$lojaCielo = \Sh\CieloLojas::getLoja($idLoja);
		
		//VOU GERAR O XML DE REQUISIÇÃO
		$xmlRequisicao = file_get_contents(self::$templatesPath.'requisicao-cancelamento.xml');
		
		//SUBSTITUINDO CABECALHO
		$xmlRequisicao = str_replace(
			array('{{id}}'), 
			array(\Sh\Library::getUniqueId()), 
			$xmlRequisicao
		);
		
		//SUBSTITUINDO INFORMAÇÕES ADICIONAIS
		$xmlRequisicao = str_replace(
			array('{{transacao.tid}}'), 
			array($tid), 
			$xmlRequisicao
		);
		
		//ENVIANDO REQUISICAO
		$xmlRetorno = self::enviarRequisicao($xmlRequisicao, $lojaCielo);
		$cieloResposta = simplexml_load_string($xmlRetorno, null, LIBXML_NOERROR);
		self::verificaErroCielo($cieloResposta);
		
		$strDadosPedido = 'dados-pedidos';
		
		//MONTANDO ARRAY DE RESPOSTA
		$resposta = array();
		$resposta['tid'] = $tid;
		$resposta['ordem'] = (string) $cieloResposta->$strDadosPedido->numero;
		$resposta['status'] = self::conversaoStatusCielo((integer) $cieloResposta->status);
		
		return $resposta;
	}
	
	/**
	 * Método para verificar se ocorreu algum erro na requisicao
	 * @param \SimpleXMLElement $xml
	 * @throws \Sh\SheerException
	 */
	static protected function verificaErroCielo ( \SimpleXMLElement $xml) {

		//FIXME COMO DEVO TRATAR O ERRO VINDO DA CIELO?
		if( $xml->getName() == "erro" ) {
			throw new \Sh\SheerException(array(
				'code' => (string) $xml->codigo,
				'message' => (string) ($xml->mensagem)
			));
		}
		
		return;
		
	}
	
	/**
	 * Método que irá enviar a chamada WebService a partir de um xml para a cielo
	 * Em caso de algum problema irá disparar uma Exception
	 * 
	 * @param string $xml
	 * @throws \Sh\SheerException
	 * @return string
	 */
	static protected function enviarRequisicao ($xml, $loja) {
		
		$curlConnection = curl_init();
		
		//SUBSTITUINDO DADOS DA LOJA
		$xml = str_replace(
			array('{{loja.numero}}', '{{loja.chave}}'), 
			array($loja['numero'], $loja['chave']), 
			$xml
		);
		//Definindo URL da Loja
		$urlLoja = self::$cieloWSTeste;
		if( $loja['ambiente'] == 'producao' ) {
			$urlLoja = self::$cieloWSProducao;
		}
		
		curl_setopt($curlConnection, CURLOPT_URL, $urlLoja);
		curl_setopt($curlConnection, CURLOPT_FAILONERROR, true);
		//FIXME VALIDAR AS CONFIGURAÇÕES ABAIXO DE SSL
		curl_setopt($curlConnection, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlConnection, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curlConnection, CURLOPT_CAINFO, getcwd()."/ssl/VeriSignClass3PublicPrimaryCertificationAuthority-G5.crt");
		curl_setopt($curlConnection, CURLOPT_SSLVERSION, 4);

		
		curl_setopt($curlConnection, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curlConnection, CURLOPT_TIMEOUT, 40);
		curl_setopt($curlConnection, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlConnection, CURLOPT_POST, true);
		curl_setopt($curlConnection, CURLOPT_POSTFIELDS, 'mensagem='.$xml );
		
		//recuperando a resposta
		$response = curl_exec($curlConnection);
		
		//capturando erro e encerrando a conexao
		if( !$response ) { $error = curl_error($curlConnection); }
		curl_close($curlConnection);
		
		//verificando erro
		if( !$response ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => $error
			));
		}
		
		//LOGANDO REQUISICAO E RESPOSTA
		//requisicao
		\Sh\LoggerProvider::log('cielo', $xml, true);
		//resposta
		\Sh\LoggerProvider::log('cielo', $response, true);
		
		return $response;
	}
	
	/**
	 * Método que irá converter a modalidade entendida pelo Sheer para o produto utilizado pela CIELO
	 * @param unknown $modalidade
	 * @return string|number
	 */
	static protected function conversaoModalidadeProduto ( $modalidade ) {
	
		if( $modalidade == 1 ) {
			return 'A';
		}
		else if ( $modalidade == 2 ) {
			return 1;
		}
		else {
			return 2;
		}
	
	}
	
	/**
	 * Método para converter o status Sheer->Cielo ou Cielo->Sheer
	 * 
	 * @param int $status Status da Cielo a ser convertido
	 * @return int
	 */
	static public function conversaoStatusCielo ($status) {
		
		$statusFinal = null;

		switch ($status) {
			//Aguardando Pagamento
			case 0:
				$statusFinal = 1;
				break;
			//Aguardando Processamento
			case 1: case 2: case 3: case 4: case 10:
				$statusFinal = 2;
				break;
			//Negada
			case 5:
				$statusFinal = 4;
				break;
			//Cancelada
			case 9:
				$statusFinal = 5;
				break;
			//Concretizada
			case 6:
				$statusFinal = 3;
				break;
			//Aguardando Cancelamento
			case 12:
				$statusFinal = 6;
				break;
			
		}
		
		return $statusFinal;
		
	}
	
}

/**
 * @author Guilherme
 * 
 * Classe Responsável por controlar as lojas registradas para a cielo
 * 
 */
abstract class CieloLojas {
	
	static protected $inicializado = false;
	
	/*
	 * Array com as lojas
	 */
	static protected $lojas = array();
	
	/*
	 * Identificador da loja padrao
	 */
	static protected $lojaPadrao = null;
	
	/**
	 * Captura as informações de uma loja cadastrada
	 * 
	 * @param string $idLoja
	 * @return array [
	 * 		codigo => string,
	 * 		chave => string,
	 *		linkRetorno => string | null
	 * ]
	 */
	static public function getLoja ($idLoja=null) {
		
		$loja = null;
		
		if( $idLoja == null || !isset(self::$lojas[$idLoja]) ) {
			$idLoja = self::$lojaPadrao;
		}
		$loja = self::$lojas[self::$lojaPadrao];
		
		return $loja;
		
	}
	
	/**
	 * Método que irá determinar se o identificador aponta para uma loja valida
	 * @param string $idLoja
	 * @return boolean
	 */
	static public function isLoja( $idLoja ) {
		return isset(self::$lojas[$idLoja]);
	}
	
	/**
	 * Método para inicializar as lojas da cielo
	 * @throws \Sh\SheerException
	 */
	static public function init () {
		
		//verificação de inicialização
		if( self::$inicializado ) {
			return;
		}
		self::$inicializado = true;
		
		//verificando existencia do arquivo de configuração
		$filePath = SH_PROJECT_SETUP_PATH.'cielo_lojas.json';
		if( !is_file($filePath) ) {
			return;
		}
		$jsonString = file_get_contents($filePath);
		
		//processando json
		$config = json_decode($jsonString, true);
		if( $config == null ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Configurações inválidas para lojas Cielo'
			));
		}
		
		//CAPTURANDO AS LOJAS
		if( !isset($config['lojas']) || !is_array($config['lojas']) ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Não existem lojas válidas para Cielo'
			));
		}
		//todas as lojas com configurações inválidas serão descartadas
		foreach ( $config['lojas'] as $idLoja=>$loja ) {
			//ambiente
			if( !isset($loja['ambiente']) || !$loja['ambiente'] || !in_array($loja['ambiente'], ['teste', 'producao']) ) {
				continue;
			}
			//codigo da loja
			if( !isset($loja['numero']) || !$loja['numero'] ) {
				continue;
			}
			//chave da loja
			if( !isset($loja['chave']) || !$loja['chave'] ) {
				continue;
			}
			$tmp = array(
				'ambiente' 		=> $loja['ambiente'],
				'numero' 		=> $loja['numero'],
				'chave' 		=> $loja['chave'],
				'linkRetorno'	=> null
			);
			
			//verificando url de retorno
			if( isset($loja['linkRetorno']) && $loja['linkRetorno'] ) {
				$tmp['linkRetorno'] = $loja['linkRetorno'];
			}
			else {
				$tmp['linkRetorno'] = null;
			}
			
			//inserindo ele no mapeamento de lojas
			self::$lojas[$idLoja] = $tmp;
			
		}
		
		//DETERMINANDO LOJA PADRAO
		//buscando configuração padrao
		if( isset($config['default']) && $config['default'] ) {
			//verificando existencia da página
			if( isset(self::$lojas[$config['default']]) ) {
				self::$lojaPadrao = $config['default'];
			}
		}
		//nao tendo loja padrao setada assumimos qualquer uma
		if( !self::$lojaPadrao ) {
			reset(self::$lojas);
			self::$lojaPadrao = key(self::$lojas);
		}
		
	}
	
};

/*
class CieloPedido {
	
	/**
	 * @var array
	 * 	numero
	 * 	chave
	 * 	linkRetorno
	protected $loja = array(
		'numero' => null,
		'chave' => null,
		'linkRetorno' => null
	);
	
	/**
	 * @var array
	 * 	numero
	 * 	valor
	 * 	moeda
	 * 	
	protected $dadosPedido = array(
		'numero' 			=> null,
		'valor' 			=> null,
		'moeda' 			=> null,
		'data-hora' 		=> null,
		'descricao' 		=> null,
		'idioma' 			=> 'PT',
		'soft-descriptor' 	=> null
	);
	
	protected $formaPagamento = array(
		'bandeira'			=> null,
		'produto'			=> null,
		'parcelas'			=> null
	);
	
	protected $urlRetorno = null;
	//FIXME VERIFICAR ESTE ITEM AUTORIZAR DE ACORDO COM MANUAL POIS PODE POSSUIR DIVERGENCIA EM DEBITO, VISA E MASTER
	protected $autorizar = 3;
	
	protected $capturar = true;
	
	protected $tid				= null;
	protected $status			= null;
	protected $urlAutenticacao	= null;
	
	
	/**
	 * @param array $loja [ "numero", "chave" ]
	 * @param array $pedido ["numero", "valor", "descricao", "soft-descriptor"]
	 * @param array $formaPagamento ["bandeira", "produto", "parcelas"]
	static public function novaTransacao( $loja, $pedido, $formaPagamento ) {
		
		$this->loja = array_merge($this->loja, $loja);
		
		$this->dadosPedido = array_merge($this->dadosPedido, $pedido);
		
		$this->formaPagamento = array_merge($this->formaPagamento, $formaPagamento);
		
	}
	
	protected function conversaoModalidadeProduto ( $modalidade ) {
		
		if( $modalidade == 1 ) {
			return 'A';
		}
		else if ( $modalidade == 2 ) {
			return 1;
		}
		else {
			return 2;
		}
		
	}
	
}*/
/*
abstract class CieloPedidoAuxiliar {
	
	static public function XMLHeader( \Sh\CieloPedido $cieloPedido ) {
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
		return $xml;
	}
	
	 * Método para gerar a cabeça com os dados do estabelecimento comercial
	 * @return string
	private function XMLDadosEc( \Sh\CieloPedido $cieloPedido ) {
		
		$xml = '<dados-ec>';
			$xml .= '<numero>'.$cieloPedido->dadosEcNumero.'</numero>';
			$xml .= '<chave>'.$cieloPedido->dadosEcChave.'</chave>';
		$xml .= '</dados-ec>';
	
		return $xml;
	}
	
	/
	private function XMLDadosPortador() {
		
		$xml = '<dados-ec>';
			$xml .= '<numero>'.$cieloPedido->dadosEcNumero.'</numero>';
			$xml .= '<chave>'.$cieloPedido->dadosEcChave.'</chave>';
		$xml .= '</dados-ec>';
		
		
		$msg = '<dados-portador>' . "\n      " .
				'<numero>'
				. $cieloPedido->dadosPortadorNumero .
				'</numero>' . "\n      " .
				'<validade>'
						. $cieloPedido->dadosPortadorVal .
						'</validade>' . "\n      " .
						'<indicador>'
								. $cieloPedido->dadosPortadorInd .
								'</indicador>' . "\n      " .
								'<codigo-seguranca>'
										. $cieloPedido->dadosPortadorCodSeg .
										'</codigo-seguranca>' . "\n   ";
	
		// Verifica se Nome do Portador foi informado
		if($cieloPedido->dadosPortadorNome != null && $cieloPedido->dadosPortadorNome != "")
		{
			$msg .= '   <nome-portador>'
					. $cieloPedido->dadosPortadorNome .
					'</nome-portador>' . "\n   " ;
		}
	
		$msg .= '</dados-portador>';
	
		return $msg;
	}
	/
	
	/*private function XMLDadosCartao() {
		$msg = '<dados-cartao>' . "\n      " .
				'<numero>'
				. $cieloPedido->dadosPortadorNumero .
				'</numero>' . "\n      " .
				'<validade>'
						. $cieloPedido->dadosPortadorVal .
						'</validade>' . "\n      " .
						'<indicador>'
								. $cieloPedido->dadosPortadorInd .
								'</indicador>' . "\n      " .
								'<codigo-seguranca>'
										. $cieloPedido->dadosPortadorCodSeg .
										'</codigo-seguranca>' . "\n   ";
	
		// Verifica se Nome do Portador foi informado
		if($cieloPedido->dadosPortadorNome != null && $cieloPedido->dadosPortadorNome != "")
		{
			$msg .= '   <nome-portador>'
					. $cieloPedido->dadosPortadorNome .
					'</nome-portador>' . "\n   " ;
		}
	
		$msg .= '</dados-cartao>';
	
		return $msg;
	}*/
	
	/**
	 * Método que monta o corpo das informações do pedido para efetuar uma nova requisição
	 * @return string
	private function XMLDadosPedido( \Sh\CieloPedido $cieloPedido ) {
		
		$xml = '<dados-pedido>';
			$xml .= '<numero>'.$cieloPedido->dadosPedidoNumero.'</numero>';
			$xml .= '<valor>'.$cieloPedido->dadosPedidoValor.'</valor>';
			$xml .= '<moeda>'.$cieloPedido->dadosPedidoMoeda.'</moeda>';
			$xml .= '<data-hora>'.$cieloPedido->dadosPedidoData.'</data-hora>';
		
			if( $cieloPedido->dadosPedidoDescricao != null && strlen($cieloPedido->dadosPedidoDescricao) > 0 ) {
				$xml .= '<descricao>'.$cieloPedido->dadosPedidoDescricao.'</descricao>';
			}
			$xml .= '<idioma>'.$cieloPedido->dadosPedidoIdioma.'</idioma>';
		$xml .= '</dados-pedido>';
	
		return $xml;
	}
	
	/**
	 * Método que monta o corpo das informações da forma de pagamento para efetuar uma nova requisição
	 * @return string
	private function XMLFormaPagamento( \Sh\CieloPedido $cieloPedido ) {
		
		$xml = '<forma-pagamento>';
			$xml .= '<bandeira>'.$cieloPedido->formaPagamentoBandeira.'</bandeira>';
			$xml .= '<produto>'.$cieloPedido->formaPagamentoProduto.'</produto>';
			$xml .= '<parcelas>'.$cieloPedido->formaPagamentoParcelas.'</parcelas>';
		$xml .= '</forma-pagamento>';
		
		return $xml;
	}
	
	private function XMLUrlRetorno( \Sh\CieloPedido $cieloPedido ) {
		$xml = '<url-retorno>' . $cieloPedido->urlRetorno . '</url-retorno>';
		return $xml;
	}
	
	private function XMLAutorizar( \Sh\CieloPedido $cieloPedido ) {
		$xml = '<autorizar>' . $cieloPedido->autorizar . '</autorizar>';
		return $xml;
	}
	
	private function XMLCapturar( \Sh\CieloPedido $cieloPedido ) {
		$xml = '<capturar>' . $cieloPedido->capturar . '</capturar>';
		return $xml;
	}
	
	
}
*/