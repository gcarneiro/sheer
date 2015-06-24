<?php

namespace Sh;

abstract class PageRequest {
	
	static protected $converters = [];
	
	/**
	 * Método para efetuar o processamento da página desejada. Este já irá processar e imprimir na tela.
	 * @param string $page
	 */
	static public function process( $page=null ) {
		
		//DEFININDO A PÁGINA DESEJADA
		if( !$page ) {
			$page = self::getPageFromParameter();
		}
		
		//TODO INSERIR A INTELIGENCIA DE ROTAS AQUI
		
		//VAMOS PROCESSAR O PAGE PELOS CONVERSORES PARA OBTER O PAGEFILE
		$pageFile = self::convertPageParameter($page);
		
		//BUSCO AS CONFIGURAÇÕES DA PÁGINA
		$pageConfig = self::getPageConfig($pageFile);
		
		//PRECISO PROCESSAR A PÁGINA
		$pageProcessor = new \Sh\PageProcessor($pageConfig, $pageFile);
		$html = $pageProcessor->render();
		
		echo $html;
	}
	
	/**
	 * Método para efetuar o processamento da página desejada. 
	 * Este irá processar somentes os holder e irá devolver um json.
	 * @param string $page
	 */
	static public function processHolders( $page=null ) {
		
		//DEFININDO A PÁGINA DESEJADA
		if( !$page ) {
			$page = self::getPageFromParameter();
		}
		
		//VAMOS PROCESSAR O PAGE PELOS CONVERSORES PARA OBTER O PAGEFILE
		$pageFile = self::convertPageParameter($page);
		
		//BUSCO AS CONFIGURAÇÕES DA PÁGINA
		$pageConfig = self::getPageConfig($pageFile);
		
		//PRECISO PROCESSAR A PÁGINA
		$pageHolderProcessor = new \Sh\PageHolderProcessor($pageConfig, $pageFile);
		$html = $pageHolderProcessor->render();
		
		echo json_encode($html);
	}
	
	/**
	 * Método para registrar um novo conversor de páginas.
	 * Estas funções são responsáveis por visualizar um parametro p e converte-lo para o parametro "p" correto para busca nos "navigations"
	 * 
	 * Esta closure deverá receber um parametro, o qual será o "page" buscado pelo navegador. Ele deve processa-lo e saber definir se deve reescreve-lo ou não.
	 * 		Caso tenha uma reescrita a ser realizada deve retornar uma "string" sendo o pagefile reescrito
	 * 		Caso não tenha nada a declarar, deve retornar null
	 * 		Caso seja um permalink para algum caso seu e algum erro ocorra retorne false
	 * 
	 * @param \Closure $fn
	 */
	static public function registerConverter ( \Closure $fn ) {
		
		self::$converters[] = $fn;
		
	}
	
	static public function addRoute($classname) {
		
	}
	
	/**
	 * Método para executar os converters em busca de traduzir o parametro atual
	 * 
	 * @param string $page
	 * @return string
	 */
	static protected function convertPageParameter ( $page ) {
		
		$pageFile = null;
		if( self::$converters ) {
			foreach ( self::$converters as $closure ) {
				$closureResponse = $closure($page);
				//A closure não deu resposta válida
				if( $closureResponse === null ) {
					continue;
				}
				//A closure respondeu com um valor correto e definitivo
				else if ( is_string($closureResponse) && strlen($closureResponse) > 0 ) {
					$pageFile = $closureResponse;
					break;
				}
				//A closure respondeu algo que não entendo
				else {
					continue;
				}
			}
		}
		//verificando o pageFile
		if( $pageFile === null ) {
			$pageFile = $page;
		}
		return $pageFile;
	}
	
	/**
	 * Método para recuperar as configurações da página desejada
	 * Este irá buscar o arquivo de configuração do navigation primeiramente dentro do projeto, depois dentro do Sheer.
	 * Irá pegar o renderPath da sessão atual e irá quebra-lo em ";", procurando em cada um deles o arquivo navigation
	 *
	 * @param string $pageFile nome da página desejada
	 * @throws \Sh\FatalErrorException
	 * @return array
	 */
	static protected function getPageConfig ($pageFile) {
	
		//RESOLVO O PATH FINAL PARA A PÁGINA
		$jsonPath = \Sh\PathResolver::resolvePage($pageFile);
	
		//CASO NÃO TENHAMOS ENCONTRADO AS CONFIGURAÇÕES DA PÁGINA ENCERRAMOS A EXECUÇÃO
		if( !$jsonPath ) {
			throw new \Sh\FatalErrorException(array(
					'message' => 'Página inexistente',
					'code' => 'SPR_0001'
			));
		}
	
		//LENDO CONFIGURAÇÕES
		$jsonContents = file_get_contents($jsonPath);
		if( !$jsonContents ) {
			throw new \Sh\FatalErrorException(array(
					'message' => 'Erro ao processar arquivo da página "'.$pageFile.'"',
					'code' => 'SPR_XXXX'
			));
		}
	
		//PROCESSANDO JSON
		$config = json_decode($jsonContents, true);
		if( !$config ) {
			throw new \Sh\FatalErrorException(array(
					'message' => 'Erro ao processar configurações da página "'.$pageFile.'"',
					'code' => 'SPR_XXXX'
			));
		}
	
		return $config;
	}
	
	/**
	 * Método para recuperar a página do GET
	 * @return string
	 */
	static protected function getPageFromParameter () {
		$page = null;
		
		//verificando modelos de chamadas de páginas
		if( isset($_GET['p']) ) {
			$page = trim($_GET['p']);
		}
		else if ( isset($_GET['page']) ) {
			$page = trim($_GET['page']);
		}
		else {
			$page = 'index';
		}
		return $page;
	}
	
}