<?php

namespace Sh;

abstract class GenericAction {
	
	/**
	 * @var \Sh\ActionHandler
	 */
	protected $actionHandler;
	
	/**
	 * @var \PDO
	 */
	protected $connection;
	
	/**
	 * Variável responsável por armazenar o primary key que sofreu a operação para podermos mapear em seus addons
	 * @var string
	 */
	protected $contentPrimaryKey;
	
	/**
	 * Esta propriedade vai controlar se o Action deve calcular os Addons do conteúdo. 
	 * Ela é marcada como default sendo false pois somente os métodos mais primários "Add", "Update" e "Delete" vão operar por registro no banco. Neste métodos que devemos guardar os addons e não em qualquer.
	 * Isso pois todo Action que será extendido, ele será feito para efetuar operações em mais de um módulo por vez, e deverá chamar um dos primários, os que já irão calcular os addons para cada conteudo.
	 * @var boolean
	 */
	protected $processAddons = false;
	
	/**
	 * Marcador se devemos gerar valor para o primaryKey ou não
	 * Caso não gere e o primaryKey não seja enviado haverá erro de validação
	 * @var boolean
	 */
	protected $generatePkValue = false;
	
	/**
	 * Marcador se devemos obrigar a verificação do PrimaryKey na ação
	 * Caso seja false ele irá ignorar o controle de PrimaryKey
	 * @var boolean
	 */
	protected $ignorePkValidation = false;
	
	/**
	 * Marcador se devemos efetuar o parser e as verificações dos dados
	 * Caso seja false ele irá ignorar todo o processo de parseData
	 * @var boolean
	 */
	protected $parseData = false;
	
	/**
	 * Este marcador serve para determinar se este foi o ActionHandler que invocou o greenCard.
	 * Este é importante para que nenhum outro actionHandler que invoque o greenCard revogue o greenCard que este AH solicitou
	 * 
	 * @var boolean
	 */
	protected $greenCardOwner = false;
	
	
	public final function __construct(\Sh\ActionHandler $actionHandler) {
		
		$this->actionHandler = $actionHandler;
		
	}
	
	/**
	 * Método para execução de ação. Este será chamado pelo Sheer e por qualquer funcionalidade que queira executar uma ação.
	 * A partir deste que iremos chamar todos os métodos de controle e tratamento de conteudo
	 * 
	 * Este sabe controlar o greencard setando para o actionHandler
	 * 
	 * @param \PDO $connection
	 * @param array $data
	 * @throws \Sh\ActionException
	 * @return multitype:boolean NULL unknown Ambigous <> array 
	 */
	public final function exec( \PDO $connection=null, $data=null ) {
		
		$response = array(
			'status' => false,
			'code' => null,
			'message' => null,
			'data' => null
		);
		
		//VERIFICANDO CONEXÃO COM O BANCO DE DADOS
		if( !$connection ) {
			$this->connection = \Sh\DatabaseConnectionProvider::getDatabaseConnection();
		}
		else {
			$this->connection = $connection;
		}
		
		//Variavel para determinar greenCard anterior
		$greenCardPrevious = \Sh\ContentActionManager::hasGreenCard();
		
		try {
			
			//VERIFICANDO PERMISSÃO
			if( !$this->actionHandler->hasPermission() ) {
				throw new \Sh\ActionException(array(
					'code' => null,
					'message' => 'Você não possui permissão para executar esta ação'
				));
			}
			//Assumindo greencard se actionHandler requisitar e ainda não tivermos
			if( !$greenCardPrevious && $this->actionHandler->getGreenCard() ) {
				\Sh\ContentActionManager::invokeGreenCard();
				$this->greenCardOwner = true;
			}

			//CAPTURANDO O ARRAY DE PARAMETROS A SEREM UTILIZADOS COMO DADOS
			if( !$data ) { $data = $this->loadDataFromRequest(); }
			
			//PROCESSANDO CAMPOS DE ARQUIVO/IMAGEM
			$data = $this->processDataFiles($data);
			
			//EXECUTANDO O PREPARE DO DESENVOLVEDOR
			$data = $this->prepare($data);
			
			//EFETUANDO PROCESSAMENTO DOS DADOS
			$dataParsed = $data;
			if( $this->parseData ) {
				$dataParsed = $this->parseData($data);
			}
			
			//EXECUTO A AÇÃO DO ACTION
			$result = $this->doAction($dataParsed);
			if( !$this->validateResponse($result) ) {
				throw new \Sh\ActionException(array(
					'code' => 'SA_XXXX',
					'message' => 'Resposta do ActionHandler é inválida'
				));
			}
			
			//EXECUTO OS ADDONS
			$this->execAddons($result['data']);
			
			//FINALIZO A OPERAÇÃO EM BANCO COMMITANDO AS OPERAÇÕES
			if( !$connection ) {
				$this->connection->commit();
			}
			
			//Revogando greencard se actionHandler requisitar
			if( !$greenCardPrevious && $this->actionHandler->getGreenCard() && $this->greenCardOwner ) {
				\Sh\ContentActionManager::removeGreenCard();
			}
			
			//preciso gerar a resposta
			$response['status'] = $result['status'];
			$response['code'] = $result['code'];
			$response['data'] = $result['data'];
			if( isset($result['message']) ) { $response['message'] = $result['message']; }
		}
		catch (\Sh\SheerException $e) {
			//FINALIZO A OPERAÇÃO EM BANCO REMOVENDO AS OPERAÇÕES
			if( !$connection ) {
				$this->connection->rollBack();
			}
			//Revogando greencard se actionHandler requisitar
			if( $this->actionHandler->getGreenCard() ) {
				\Sh\ContentActionManager::removeGreenCard();
			}
			
			$info = $e->getInfo();
			$response['status'] = false;
			$response['code'] = $info['code'];
			$response['message'] = $info['message'];
		}
		
		return $response;
	}
	
	/**
	 * 
	 * Método para processar todos os campos que são de arquivos. Este irá receber os parametros do action e deverá devolver um novo array com os parametros tratados
	 * 
	 * @param array $data
	 * @return array
	 */
	protected function processDataFiles ( $data ) {
		
		//CAPTURANDO TODOS OS CAMPOS
		$fields = $this->actionHandler->getDataSource()->getFields(false);
		
		//ITERANDO PELOS CAMPOS DO SOURCE PARA BUSCAR OS DE ARQUIVO
		foreach( $fields as $idField=>$field ) {
			
			//escapando campos que não são de arquivo
			if( !in_array($field->getDataType(), array('file', 'image')) ) {
				continue;
			}
			
			//determinando se o campo de arquivo já foi processado ou se devemos processa-lo
			//XXX possível causa de erro, verifico apenas se é um array
			if( !isset($data[$field->getId()]) || !is_array($data[$field->getId()]) ) {
				continue;
			}
			
			//TYPE FILE
			if( $field->getDataType() == 'file' ) {
				
				//DETERMINANDO CONTROLADORES INICIAIS
				//determinando valor antigo
				$possuiValorAntigo = (isset($data[$field->getId().'_current']) && $data[$field->getId().'_current']);
				//determinando se devemos remover o anterior
				$removerAnterior = isset($data[$field->getId().'_remove']) && (boolean) $data[$field->getId().'_remove'];
				//determinando cadastro de novo arquivo
				$novoArquivo = ( $data[$field->getId()]['error'] != UPLOAD_ERR_NO_FILE );
				
				//CADASTRANDO NOVO ARQUIVO
				if( $novoArquivo ) {
					//inserindo novo arquivo
					$responseDocument = \Sh\ContentActionManager::doAction('fileDocument/fileDocument_add', $data[$field->getId()], $this->connection);
					\Sh\Library::actionResponseCheck($responseDocument);
					//marcando para remover antigo se existir
					$removerAnterior = $possuiValorAntigo;
				}
				
				//REMOVENDO VALOR ANTIGO
				if( $possuiValorAntigo && $removerAnterior ) {
					$fileDocument = array(
						'id' => $data[$field->getId().'_current'],
						'remove' => 1
					);
					$responseDocumentRemover = \Sh\ContentActionManager::doAction('fileDocument/fileDocument_update', $fileDocument, $this->connection);
					\Sh\Library::actionResponseCheck($responseDocumentRemover);
				}
				
				//TRATANDO O DADO A SER UTILIZADO PELA AÇÃO
				//se inserimos novo dado
				if( $novoArquivo ) {
					$data[$field->getId()] = $responseDocument['data']['id'];
				}
				//se removemos dado anterior ou não possuimos dado anterior
				else if ( $removerAnterior || !$possuiValorAntigo ) {
					$data[$field->getId()] = null;
				}
				//se temos valor antigo e permanecemos com ele
				else {
					$data[$field->getId()] = $data[$field->getId().'_current'];
				}
			}
			//TYPE FILE
			else if( $field->getDataType() == 'image' ) {
				
				//DETERMINANDO CONTROLADORES INICIAIS
				//determinando valor antigo
				$possuiValorAntigo = (isset($data[$field->getId().'_current']) && $data[$field->getId().'_current']);
				//determinando se devemos remover o anterior
				$removerAnterior = isset($data[$field->getId().'_remove']) && (boolean) $data[$field->getId().'_remove'];
				//determinando cadastro de novo arquivo
				$novoArquivo = ( $data[$field->getId()]['error'] != UPLOAD_ERR_NO_FILE );
			
				//CADASTRANDO NOVO ARQUIVO
				if( $novoArquivo ) {
					//inserindo novo arquivo
					$imageData = array(
						'image'=>$data[$field->getId()], 
						'maps'=>$field->getPicturesMap()
					);
					$responseDocument = \Sh\ContentActionManager::doAction('filePicture/filePicture_add', $imageData, $this->connection);
					\Sh\Library::actionResponseCheck($responseDocument);
					//marcando para remover antigo se existir
					$removerAnterior = $possuiValorAntigo;
				}
			
				//REMOVENDO VALOR ANTIGO
				if( $possuiValorAntigo && $removerAnterior ) {
					$fileDocument = array(
							'id' => $data[$field->getId().'_current'],
							'remove' => 1
					);
					$responseDocumentRemover = \Sh\ContentActionManager::doAction('filePicture/filePicture_update', $fileDocument, $this->connection);
					\Sh\Library::actionResponseCheck($responseDocumentRemover);
				}
			
				//TRATANDO O DADO A SER UTILIZADO PELA AÇÃO
				//se inserimos novo dado
				if( $novoArquivo ) {
					$data[$field->getId()] = $responseDocument['data']['id'];
				}
				//se removemos dado anterior ou não possuimos dado anterior
				else if ( $removerAnterior || !$possuiValorAntigo ) {
					$data[$field->getId()] = null;
				}
				//se temos valor antigo e permanecemos com ele
				else {
					$data[$field->getId()] = $data[$field->getId().'_current'];
				}
			}
			
		}
		
		return $data;
		
	}
	
	/**
	 * Método responsável por realizar as operações de addons posteriormente ao registro ter sido operado
	 * Tomar muito cuidado com a utilização deste método de forma customizado para não perder propriedades do Sheer
	 * @param array $result
	 */
	protected function execAddons($data) {
		
		if( !$this->processAddons ) {
			return;
		}
		
		//PUBLICATION HISTORY
		if( $this->actionHandler->getDataSource()->hasPublicationHistory() ) {
			$this->addonPublicationHistory($data);
		}
		
		//PUBLICATION METADATA
		if( $this->actionHandler->getDataSource()->hasPublicationHistory() ) {
			$this->addonPublicationMetadata($data);
		}
		
		//IMAGE REPOSITORY
		//este não fará nada pois o seu controlador vem depois
		
	}
	
	/**
	 * Método para processar o addon Publication History
	 * Este ADDON irá guardar todo o histórico de mudanças de um pedido.
	 * Ele irá gravar esse log identificando um registro por idDataSource-PrimaryKey e utilizará um arquivo em disco para traduzi-lo.
	 * 		Iremos guardar nesse arquivo os jsons das modificações realizadas
	 * @param array $data
	 * 		Será todo o dado alterado que deverá ser serializado
	 */
	protected function addonPublicationHistory ( $data ) {
		
		//Capturando o primaryKey do conteúdo a ser alterado
		$contentId 				= $this->contentPrimaryKey;
		//Capturando a identificação completa do dataSource
		$aliasDataSource 		= $this->actionHandler->getModuleId().'_'.$this->actionHandler->getDataSource()->getId();
		//Capturando identificador do usuário que efetua a operação
		$idUser 				= \Sh\AuthenticationControl::getAuthenticatedUserInfo('id');
		$opDate 				= date('Y-m-d H:i:s');
		
		//GERANDO ARQUIVO PARA PUBLICATIONHISTORY
		//Determinando path do log para publicationHistory
		$logFilePath = SH_LOG_PATH.'pubHist/';
		//Criando pasta caso não exista
		if( !is_dir($logFilePath) ) {
			if( !mkdir($logFilePath) ) {
				throw new \Sh\FatalErrorException(array(
					'code' => null,
					'message' => 'Erro ao criar pasta para log de publication History'
				));
			}
		}
		//Abrindo arquivo com ponteiro no final - se não existir cria
		$logFilePath 		.= $aliasDataSource.'_'.$contentId.'.log';
		$arquivoExistente 	= is_file($logFilePath);
		//chmod($logFilePath, '777');
		//$fileResource 		= fopen($logFilePath, 'a');
		//Caso o arquivo não exista ainda, vamos setar o seu chmod para 777
		//if( !$arquivoExistente ) {
			//chmod($logFilePath, '777');
		//}
		
		//Criando objeto que será logado
		$logObject = array(
			'idUser' 		=> $idUser,
			'date'			=> $opDate,
			'idDataSource' 	=> $aliasDataSource,
			'idContent'		=> $contentId,
			'data'			=> &$data
		);
		//$logObjectSerialized = serialize($logObject);
		
		//Inserinado objeto serializado no arquivo
		//fwrite($fileResource, $logObjectSerialized);
		
		//Retornando sucesso
		return true;
		
	}
	
	
	protected function addonPublicationMetadata ( $data ) {}
	
	/**
	 * Método que realiza a ação do ActionHandler
	 * Este deve ser extendido em cada classe de ação para customizar o que o mesmo fará.
	 * Este deve ter padrões de respostas bem definidos que são especificados neste comentário.
	 * 		Deve retornar um resumo das informações adicionadas para o dado em questão seguindo o seguinte padrão.
	 * 			status => boolean
	 * 			code => código específico [opcional]
	 * 			data => array contendo as informações adicionadas neste dado
	 * 		Em caso de insucesso deve soltar uma exceção do tipo \Sh\ActionException com um código e uma mensagem que traduzam o erro.
	 * 
	 * @param array $data
	 * @return array [
	 * 		status
	 * 		code
	 * 		data
	 * ]
	 */
	abstract protected function doAction ($data);
	
	/**
	 * Método que pode ser utilizado pelo desenvolvedor para efetuar quaisquer operações para preparar o inicio da ação
	 * Ele será executado anteriormente ao parseamento dos dados
	 * Ele irá receber os dados enviados e deverá retorna-los
	 * 
	 * @param $data array com os parametros enviados
	 * @return $data array com os parametros a serem utilizados
	 */
	protected function prepare($data) {
		return $data;
	}
	
	/**
	 * Método para extrair somente os dados necessários para a operação em banco do array de dados
	 * @param array $data
	 * @return multitype:NULL 
	 */
	protected function extractPrimitiveData($data) {
		
		$fields = $this->actionHandler->getDataSource()->getFields(false);
		$extractData = array();
		foreach( $fields as $idField=>$field ) {
			$extractData[$field->getId()] = null;
			if( isset($data[$field->getId()]) ) {
				//Volto a fazer a conversão input=>primitive aqui para pode adicionar arquivos
				$extractData[$field->getId()] = $field->formatInputDataToPrimitive($data[$field->getId()]);
// 				$extractData[$field->getId()] = $data[$field->getId()];
			}
		}
		return $extractData;
		
	}
	
	/**
	 * Método responsável por analisar e validar os dados, gerar o PrimaryKey caso necessário e preparar os dados para inserção
	 * @param array $data
	 * @throws \Sh\SheerException
	 */
	protected final function parseData ($data) {
		
		if( $data ) {
			$fields = $this->actionHandler->getDataSource()->getFields(false);
			
			foreach ($fields as $idField => $field) {
				
				//VERIFICO SE O DADO FOI ENVIADO
				$sentData = false;
				if( isset($data[$idField]) ) {
					$sentData = ($data[$idField] || strlen($data[$idField]));
					
				}
				
				//SE O DADO FOI ENVIADO LIMPO OS ESPACOS DO FINAL E INICIO
				if( $sentData && !is_array($data[$idField]) ) {
					$data[$idField] = trim($data[$idField]);
				}
				
				//SE O DADO FOI ENVIADO, VERIFICO SE ELE DEVE SER UPPERCASE OR LOWERCASE
				if( $sentData && $field->getUpperCase() ) {
					$data[$idField] = strtoupper($data[$idField]);
				}
				else if ( $sentData && $field->getLowerCase() ) {
					$data[$idField] = strtolower($data[$idField]);
				}
				
				//CONTROLES DE PRIMARY KEY
				//preciso verificar se devo ignorar a validação do primaryKey
				if( $field->isPrimaryKey() && $this->ignorePkValidation ) {
					continue;
				}
				//sendo primaryKey devo verificar se existe valor, se não existir devo considerar gerar um valor para ele dependendo do Action
				if( $field->isPrimaryKey() && !$sentData && $this->generatePkValue ) {
					$data[$idField] = \Sh\Library::getUniqueId(true);
					//digo que o dado é enviado pois o PrimaryKey foi gerado
					$sentData = true;
				}
				
				//VERIFICAMOS SE O CAMPO É OBRIGATÓRIO E EFETUAMOS SUA VALIDAÇÃO
				//iniciando validacao
				$validated = false;
				if( $sentData ) {
					$validated = $field->validateValueInput($data[$idField]);
				}
				//VERIFICANDO OBRIGATORIEDADE E VALIDACAO
				if ( $field->getRequired() ) {
					if( !$validated ) {
						throw new \Sh\ActionException(array(
							'message' => 'Campo "'.$idField.'" não é válido',
							'code' => 'SA_XXXX'
						));
					}
				}
				//não sendo obrigatório
				else {
					//caso o campo não seja obrigatório precisamos verificar o seu valor apenas se ele for enviado
					if( $sentData && !$validated ) {
						throw new \Sh\ActionException(array(
							'message' => 'Campo "'.$idField.'" não é válido',
							'code' => 'SA_XXXX'
						));
					}
					//caso o campo não tenha sido enviado e possua defaultValue, irei assumir o valor default
					else if ( !$sentData && ( $field->getDefaultValue() !== null && strlen($field->getDefaultValue()) > 0)  ) {
						$defaultValue = $field->getDefaultValue();
						//verifico se o valor é um alias
						if( \Sh\RuntimeVariables::isAliasValue($defaultValue) ) {
							$data[$idField] = \Sh\RuntimeVariables::getAliasValue($defaultValue);
						}
						else {
							$data[$idField] = $defaultValue;
						}
					}
				}
				
				//VERIFICANDO SE É PRIMARYKEY PARA ARMAZENAR O SEU VALOR
				if( $field->isPrimaryKey() ) {
					$this->contentPrimaryKey = $data[$idField];
				}
			}
			
		}
		return $data;
	}
	
	/**
	 * Método para validar a resposta emitida pelo doAction do Desenvolvedor.
	 * @param array $response
	 * @return boolean
	 */
	protected final function validateResponse ($response) {
		
		if( !array_key_exists('status', $response) || !array_key_exists('data', $response) || !array_key_exists('code', $response) ) {
			return false;
		}
		return true;
		
	}
	
	/**
	 * Método para recuperar os dados da requisição atual
	 * @return array
	 */
	protected function loadDataFromRequest () {
		$data = array_merge($_GET, $_POST, $_FILES);
		return $data;
	}
	
	/**
	 * Método para soltar uma exceção a partir de um erro PDO.
	 * @param string $message
	 * @throws \Sh\ActionException
	 */
	protected function throwPDOError ($message=null) {
		$errorInfo = $this->connection->errorInfo();
		if ( !$message ) {
			$message = $errorInfo[2];
		}
		throw new \Sh\ActionException(array(
			'message' => $message,
			'code' => 'SA_XX'
		));
	}
	
}