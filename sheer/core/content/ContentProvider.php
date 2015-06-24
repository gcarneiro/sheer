<?php

namespace Sh;

class ContentProvider extends \Sh\GenericContentProvider {
	
	/**
	 * @var \PDO $connection
	 */
	protected $connection;
	
	/**
	 * @var \Sh\QueryGenerator\Select
	 */
	protected $queryGenerator;
	
	/**
	 * @param \Sh\DataProvider $dataProvider
	 * @param \PDO $conn
	 */
	public function __construct( \Sh\DataProvider $dataProvider, \PDO $conn=null ) {
	
		parent::__construct($dataProvider);
		
		$this->queryGenerator 	= new \Sh\QueryGenerator\Select($this->dataProvider);
		$this->connection 		= $conn;
		
	}
	
	
	/**
	 * @date 2015.01.22
	 * @author Guilherme
	 * 		Atualizado este método para colocar no retorno a página atual e o total de páginas
	 * 
	 * 
	 * @param array $filters
	 * @param array $configs ['page'=>1, 'maxRows'=>null, 'sort'=>null, 'requiredFilters'=>null, 'conn'=>\PDO]
	 * 		page 			=> Número da página a carregar os registros
	 * 		maxRows 		=> Quantidade de registros desejadas.
	 * 								"null" Para assumir o definido no DataProvider
	 * 								0 Para trazer todos os registros disponíveis
	 * 								X para X registros
	 * 		sort			=> Opções de ordenação customizadas
	 * 							array[
	 * 								relationPath
	 * 								field
	 * 								order
	 * 							],
	 * 		requiredFilters	=> Marca quais os filtros devem ser tratados como obrigatórios nesse request
	 * 							array[idFilter=>true]
	 *		conn	=> \PDO Conexão com o banco de dados
	 * 
	 * @return array(
	 * 		'available' => integer Determina quantos resultados são possíveis de serem recuperados com esses dados de busca
	 * 		'total' => integer Determina o total de registos que serão retornados nesta busca
	 * 		'results' => array Tras todos os resultados recuperados
	 * 		'navigation' => array Traz informações sobre as páginas.
	 * 			Possue 'current' e 'last'
	 * )
	 */
	public function getData ( $filters=array(), $configs=array(), \PDO $conn=null ) {
		
		//PROCESSANDO CONFIGURAÇÕES
		//Irei assumir que a conexão como quarto parametro é mais importante que a enviada dentro das configs
		if( $conn && is_a($conn, '\PDO') ) {
			$configs['conn'] = $conn;
		}
		$configuration = \Sh\ContentProviderManager::getConfigurationFromPageAndConfigs($configs);
		//Determinando conexão final com a do ContentProvider
		if( $configuration['conn'] && is_a($configuration['conn'], '\PDO') ) {
			$this->connection = $configuration['conn'];
		}
		else {
			$configuration['conn'] = $this->connection;
		}
		
		/**
		 * Verificando controlador de página por parametro
		 * 
		 * TODO DEVO JOGAR ESSE TRECHO DE CÓDIGO LA PARA DENTRO DO PROCESSADOR DO RENDERABKE
		 */
		if(isset($filters['shPage'])){
			//Se vier seto seu valor em idStyle
			$configuration['page'] = (integer) $filters['shPage'];
			if( !$configuration['page'] || $configuration['page'] < 1 ) {
				$configuration['page'] = 1;
			}
		}
		
		try {
			
			//Capturando dataParser
			$dataParser = $this->dataProvider->getDataParser();
			//efetuando parser dos filtros
			if( $dataParser ) {
				$dataParser->setFilters($filters);
				$filters = $dataParser->parseFilters($filters);
			}
			
			//Recupero os registros
			$response = $this->queryGenerator->loadData($filters, $configuration);
			//Caso tenhamos encontrados dados válidos iremos trata-los
			if( $response['total'] > 0 ) {
				$response['results'] = $this->processData($response['results']);
				//efetuando parser dos dados
				if( $dataParser ) {
					$response['results'] = $dataParser->parseData($response['results']);
				}
			}
			//Trato os dados
			return $response;
			
		}
		catch (\Sh\SheerException $e) {
			throw $e;
		}
	}
	
	/**
	 * Método responsável por tratar os dados recuperados. Processa os dados primarios e trata os seus relacionamentos
	 * @param array $data
	 * @return array
	 */
	protected function processData ($data) {
		
		if( !$data ) {
			return array();
		}
		
		//variavel de mapeando final de conteudo
		$content = array();
		
		//capturo os relacionamentos do dataProvider
		$relations = $this->dataProvider->getRelations();
		
		//capturo o primaryKey do modulo
		$fieldPrimaryKey = $this->dataProvider->getDataSource()->getPrimaryKey();
		
		//itero entre todos os resultados para gerar o array de resposta final
		foreach ( $data as &$primitiveData) {
				
			$element = array();
				
			//capturando identificador unico
			$primaryKeyValue = $primitiveData[$fieldPrimaryKey->getId()];
				
			//BUSCANDO/GERANDO CONTEÚDO PRINCIPAL
			
			//Quando já processamos o conteúdo, apenas o recuperamos
			if( isset($content[$primaryKeyValue]) ) {
				$element = $content[$primaryKeyValue];
			}
			//Caso seja a primeira vez vamos processar os seus dados contra os descritos no dataSource
			else {
				$element = $this->loadDataFromToDataSource($this->dataProvider->getDataSource(), $primitiveData);
			}
			
			//Para capturar os relacionamentos do conteudo principal crio um relacionamento abstrato que contenha os relacionamentos diretos do dataProvider
			$relationAbstract = array();
			$relationAbstract['relations'] = $relations;
				
			//CARREGANDO CONTEUDOS RELACIONADOS
			$element = $this->processRelatedContentData($element, $primitiveData, $relationAbstract);
				
			$content[$primaryKeyValue] = $element;
				
		}
		
		return $content;
	}
	
	/**
	 * Método para processar os conteudos relacionados criando o array final de resposta
	 * 
	 * @param array $content Conteudo principal no contexto do relacionamento
	 * @param array $primitiveData Conteudo recuperado do banco de dados
	 * @param array $relation Configuração do relacionamento em questão
	 * @return array
	 */
	protected function processRelatedContentData ( $content, &$primitiveData, $relation ) {
		
		//verificando existencia de relacionamentos
		if( isset($relation['relations']) && $relation['relations'] ) {
			
			foreach ( $relation['relations'] as $idSubRelation=>$subrelation ) {
				
				$relationFieldsAlias = 't'.$subrelation['index'].'_';
				
				//capturando o primaryKey do DataSource relacionado
				$relatedPrimaryKey 		= $subrelation['dataSource']->getPrimaryKey();
				$relatedPrimaryKeyAlias = $relationFieldsAlias.$relatedPrimaryKey->getId();
				$relatedPrimaryKeyValue = $primitiveData[$relatedPrimaryKeyAlias];
				
				//iniciando relacionamentos 
				$relatedContent = null;

				//CASO SEJA UM RELACIONAMENTO COM VALOR DEVEMOS VERIFICAR OS SEUS SUBRELACIONAMENTOS
				if( $relatedPrimaryKeyValue ) {
					//BUSCANDO CONTEUDO RELACIONADO PRINCIPAL
					//pego os relacionados já computados para verificar se já não considerei essa relação para esse cara
					$relatedContent = $this->loadRelatedContentFromContent($subrelation, $content, $relatedPrimaryKeyValue);
					//caso ainda não exista, devemos processa-lo
					if( !$relatedContent ) {
						$relatedContent = $this->loadDataFromToDataSource($subrelation['dataSource'], $primitiveData, $relationFieldsAlias);
					}
					
					//VAMOS AVALIAR AGORA OS SUBRELACIONAMENTOS
					$relatedContent = $this->processRelatedContentData($relatedContent, $primitiveData, $subrelation);
				}
				
				//VAMOS ATRIBUIR OS DADOS AO DADO PRIMÁRIO
				$content = $this->pushRelatedContentToContent($subrelation, $content, $relatedContent, $relatedPrimaryKeyValue);
			}
			
		}
		return $content;
	}
	
	/**
	 * Método para recuperar os dados do conteudo para processamento de conteudos relacionados
	 * Este irá capturar os dados do conteudo relacionado para não ter que registra-los novamente e para buscar os subrelacionados
	 * 
	 * @param array $relation
	 * @param array $content
	 * @param string $relatedContentId
	 * @return array
	 */
	protected function loadRelatedContentFromContent ($relation, $content, $relatedContentId) {
		
		$relatedContent = null;
		switch ( $relation['dataFormatter'] ) {
			case 'inlineContentPrefix':
				$relatedContent = null;
				break;
			case 'fieldContentMultiple':
				if ( isset($content[$relation['alias']][$relatedContentId]) ) {
					$relatedContent = $content[$relation['alias']][$relatedContentId];
				}
				break;
			case 'relatedContent':
				if ( isset($content['relatedContent'][$relation['alias']]) ) {
					$relatedContent = $content['relatedContent'][$relation['alias']];
				}
				break;
			case 'relatedContentMultiple':
				if ( isset($content['relatedContent'][$relation['alias']][$relatedContentId]) ) {
					$relatedContent = $content['relatedContent'][$relation['alias']][$relatedContentId];
				}
				break;
			case 'fieldContent':
			default:
				if ( isset($content[$relation['alias']]) ) {
					$relatedContent = $content[$relation['alias']];
				}
				break;
		}
		return $relatedContent;
	}
	
	/**
	 * Método para inserir o conteudo relacionado no conteudo principal
	 * Este método pode ser customizado de acordo com o desenvolvedor para inserirmos os dados no local desejado
	 * 
	 * @param array $relation Informações do relacionamento
	 * @param array $content Conteudo principal
	 * @param array $relatedContent Conteudos relacionados
	 * @param string $relatedContentId Identificador do conteudo relacionado
	 * @return array
	 */
	protected function pushRelatedContentToContent ($relation, $content, $relatedContent, $relatedContentId) {
		
		//se nao tem ou nao tem
		$relacaoNula = ( !$relatedContent || !$relatedContentId );
		
		switch ( $relation['dataFormatter'] ) {
			/*
			 * inlineContentPrefix
			 * 
			 * Este iremos inserir diretamente no conteudo principal dado por dado, recebendo um novo id sendo idRelacionamento_idCampo
			 * 
			 */
			case 'inlineContentPrefix':
				
				//verificando nulabilidade
				if( $relacaoNula && ( !isset($content[$relation['alias']]) || !$content[$relation['alias']]) ) {
					$content[$relation['alias']] = null;
				}
				else {
					foreach ($relatedContent as $idData=>$data) {
						$content[$relation['alias'].'_'.$idData] = $data;
					}
				}
				break;
				/*
				 * fieldContentMultiple
				 * 
				 * Este iremos inserir o o conteudo em uma chave do conteudo principal. Esta chave será um array que suportará N relacionados
				 */
			case 'fieldContentMultiple':
				//verificando nulabilidade
				if( $relacaoNula && ( !isset($content[$relation['alias']]) || !$content[$relation['alias']]) ) {
					$content[$relation['alias']] = null;
				}
				else {
					$content[$relation['alias']][$relatedContentId] = $relatedContent;
				}
				break;
			/*
			 * relatedContent
			*
			* Os dados do relacionado serão armazenados dentro de uma chave para todos os conteudos relacionados "relatedContent" e iremos inserir o conteúdo relacionado direto em uma chave propria "idRelation"
			*/
			case 'relatedContent':
				//verificando nulabilidade
				if( $relacaoNula && ( !isset($content['relatedContent'][$relation['alias']]) || !$content['relatedContent'][$relation['alias']]) ) {
					$content['relatedContent'][$relation['alias']] = null;
				}
				else {
					$content['relatedContent'][$relation['alias']] = $relatedContent;
				}
				break;
			/*
			 * relatedContentMultiple
			 * 
			 * Os dados do relacionado serão armazenados dentro de uma chave para todos os conteudos relacionados "relatedContent" e dentro dela teremos uma posicao para cada conteudo dentro do seu idRelation
			 */
			case 'relatedContentMultiple':
				//verificando nulabilidade
				if( $relacaoNula && ( !isset($content['relatedContent'][$relation['alias']]) || !$content['relatedContent'][$relation['alias']]) ) {
					$content['relatedContent'][$relation['alias']] = null;
				}
				else {
					$content['relatedContent'][$relation['alias']][$relatedContentId] = $relatedContent;
				}
				break;
			/*
			 * fieldContent
			 */
			case 'fieldContent':
			default:
				//verificando nulabilidade
				if( $relacaoNula && ( !isset($content[$relation['alias']]) || !$content[$relation['alias']]) ) {
					$content[$relation['alias']] = null;
				}
				else {
					$content[$relation['alias']] = $relatedContent;
				}
				break;
		}
		return $content;
	}
	
	
	/**
	 * Método para tratar os dados de um array considerando como guia um dataSource
	 * Aceita também um alias para busca dos dados no array
	 * 
	 * Este também irá processar o parser configurado na tag do xml.
	 * 	Esta terá o melhor desempenho em processar os dados para um retorno de uma busca
	 * 
	 * @param \Sh\DataSource $dataSource
	 * @param array $data
	 * @param string $dataAlias
	 * @return array
	 */
	protected function loadDataFromToDataSource ( \Sh\DataSource $dataSource, $data, $dataAlias = '' ) {
		
		$response = array();
		
		/*
		 * Processando dados contra o dataSource
		 */
		
		$fields = $dataSource->getFields();
		foreach ( $fields as $idField=>$field ) {
			//determinando valor principal
			$idFieldAlias = $dataAlias.$idField;
			$value = $field::formatPrimitiveDataToSheer($data[$idFieldAlias]);
			$response[$idField] = $value;
			
			//VAMOS EFETUAR A BUSCA DOS LOOKUPS
			if( $field->getLookup() && $field->hasOptions() ) {
				//Em caso de options via variavel
				if ( $field->hasOptionsFromVariable() ) {
					//buscando os valores
					$optConfig = $field->getOptions();
					$tmpOptions = $field::getOptionsDataFromConfig($optConfig);
					//capturando o lookup
					if( $value && isset($tmpOptions[ $value ]) ) {
						$response[$idField.'_lookup'] = $tmpOptions[ $value ];
					}
					else {
						$response[$idField.'_lookup'] = null;
					}
				}
				//Em caso de options via DataProvider
				else if ( $field->hasOptionsFromDataProvider() ) {
					//devo verificar se esse campo é leftKey de algum relacionamento filho
				}
			}
			
		}
		
		/*
		 * Processando os dados contra o parser
		 */
		$parser = $dataSource->getParser();
		if( $parser ) {
			$response = $parser->parseContent($response);
		}
		
		return $response;
	}
	
}