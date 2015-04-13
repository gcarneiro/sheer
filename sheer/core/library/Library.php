<?php

namespace Sh;

abstract class Library {
	
	/**
	 * Método para remover o ultimo caracter se ele for uma barra
	 * 
	 * @param string $string
	 * @return string
	 */
	static public function removerUltimaBarra ($string) {
		if( strrpos($string, '/') == (strlen($string)-1) ) {
			$string = substr($string, 0, strlen($string)-1);
		}
		return $string;
	}
	
	/**
	 * Método que irá receber uma string e irá retornar uma string reduzida a dois nomes apenas
	 * Esta não considera como nome palavras com menos de 3 caracteres
	 * 
	 * @param string $nome
	 * @return string
	 */
	static public function getNomeFromNomeCompleto ( $nome ) {
		
		$primeiroNome = '';
		$ultimoNome = '';
		$tmp = explode(' ', $nome);
		$totalNomes = 0;
		
		//capturando o primeiro nome
		$primeiroNome .= $tmp[0];
		
		//agora inverto o array para buscar o ultimo nome
		$tmp = array_reverse($tmp);
		
		foreach ( $tmp as $nomeInterno ) {
			
			if($nomeInterno != $primeiroNome){
				//se nao for o primeiro nome, eu insiro espaco
				if( strlen($ultimoNome) > 0 ) {
					$ultimoNome = ' '.$ultimoNome;
				}
				
				//Encontrando um nome com mais de 2 caracteres insiro no ultimo nome e encerro a operacao
				if( strlen($nomeInterno) > 2 ) {
					$ultimoNome = $nomeInterno.$ultimoNome;
					break;
				}
				//nao encontrando, concateno no final do ultimoNome e sigo em frente
				else {
					$ultimoNome = $nomeInterno.$ultimoNome;
				}
			}
		}
		
		return $primeiroNome.' '.$ultimoNome;
	}
	
	/**
	 * Função para remover as virgulas de valores formatados em dinheiro
	 * @param string $dinheiro
	 * @return string
	 */
	static public function converterDinheiroParaCalcular ($dinheiro){

		$valor = str_replace('.', '', $dinheiro);
		$valor = str_replace(',', '.', $valor);
		return $valor;
		
	}
	
	/**
	 * Método para determinar o path completo de um template a partir do templateFile
	 * 
	 * @param string $templateFile Caminho do template desejado
	 * @return string|false
	 * 			string se for encontrado o template
	 * 			false se não for encontrado o template
	 */
	static public function getTemplatePath ( $templateFile ) {
	
		return \Sh\PathResolver::resolveTemplate($templateFile);
	
	}
	
	/**
	 * Método que irá determinar se deve e irá logar a query SQL caso esteja configurado para tal
	 * 
	 * @param string $query
	 */
	static public function logQuery ( $query ) {
		
		$settings = \Sh\ProjectConfig::getProjectConfigurationSettings();
		if( $settings && isset($settings['database']) && isset($settings['database']['logAllQuerys']) && $settings['database']['logAllQuerys'] ) {
			\Sh\LoggerProvider::log('database', $query);
// 			echo('<br /><br />'.$query);
		}
		
	}
	
	/**
	 * Método para Checar a resposta de uma ação.
	 * Se a resposta for negativa, iremos fabricar a exceção padrão,
	 * Em caso de resposta positiva, retornamos vazio para o processo continuar
	 * 
	 * 
	 * @param array $response ( "status", "code", "message" )
	 * @throws \Sh\SheerException
	 */
	static public function actionResponseCheck( $response ) {
		
		//verificando validade de resposta
		if( !is_array($response) || !isset($response['status']) ) {
			throw new \Sh\SheerException(array(
				'message' => 'Resposta de ação é inválida.',
				'code' => 'SCA_XXXX'
			));
		}
		
		if( !$response['status'] ) {
			if( !isset($response['message']) ) { $response['message'] = ''; }
			if( !isset($response['code']) ) { $response['code'] = ''; }
			
			throw new \Sh\SheerException(array(
					'message' => $response['message'],
					'code' => $response['code']
			));
		}
		
		return;
	}
	
	/**
	 * Método para encriptar passwords
	 * @param string $string
	 * @return string
	 */
	static public function encodeForPassword($string) {
		return hash('sha256', $string);
	}
	
	/**
	 * Método para geração de Identificadores unicos.
	 * Ele pode gerar UUIDs [CHAR36] ou GUIDs [CHAR32]
	 * @param string $uuid
	 * @return string
	 */
	static public function getUniqueId($uuid=true) {
		$key = \UUID::v4();
		if( !$uuid ) {
			$key = str_replace('-', '', $key);
		}
		return strtoupper($key);
	}
	
	/**
	 * Método para geração de Identificadores únicos númericos de tamanho de 14 ou 20 caracteres
	 * @return string Apenas retorna uma string pois um inteiro de 20 char causa overflow
	 */
	static public function getUniqueIntegerCode ($secondsEntropy=false) {
		
		$r1 = rand(0, 9999);
		$r2 = rand(0, 9999);
		
		while (strlen($r1) < 4) {
			$r1 = $r1.rand(0,9);
		}
		while (strlen($r2) < 4) {
			$r2 = $r2.rand(0,9);
		}
		
		//gerando string inicial
		$str = null;
		if( $secondsEntropy ) {
			$str = date('ymdHis');
		}
		else {
			$str = date('ymd');
		}
		
		return $str.$r1.$r2;
		
	}
	
	/**
	 * Método para determinar se nó boolean xml é true ou false
	 * @param \SimpleXMLElement $node
	 * @return boolean
	 */
	static public function getBooleanFromXmlNode ( \SimpleXMLElement $node ) {
		
		return ( (string) $node == 'true' ) ? true : false;
		
	}
	
	/**
	 * Redireciona usuário para url fornecida
	 * @param string $url
	 * 
	 * //FIXME FUNCAO INCOMPLETA
	 */
	static public function Redirect($url) {
		//caso os headers já tenham sido enviados, vamos redirecionar via js
		if(headers_sent()) {
			echo '<script>window.location.href = "'.$url.'"; </script>';
		}
		else {
			header('Location: '.$url);
		}
		exit;
	}
// 	static public function Redirect($url, $internalLink=true) {
// 		$link = '';
// 		if($internalLink) { $link = \Sheer\ProjectInfo::getRootUrl(); }
// 		$link .= $url;
			
// 		//caso os headers já tenham sido enviados, vamos redirecionar via js
// 		if(headers_sent()) {
// 			echo '<script>window.location.href = "'.$link.'"; </script>';
// 			exit;
// 		}
// 		else {
// 			header('Location: '.$link);
// 		}
// 		exit;
// 	}

	/**
	 * Método que irá determinar se a máscara é valida. Caso seja irá retornar a própria máscara. Em caso negativo false
	 * @param string $mask
	 * @return boolean
	 */
	static public function isMaskValid ( $mask ) {
		
		$final = false;
		switch($mask) {
			case 'date':
			case 'datetime':
			case 'cpf':
			case 'cep':
			case 'time':
			case 'telefone':
				$final = $mask;
				break;
			default:
				$final = false;
		}
		return $final;
	}
	
	/**
	 * Método para recuperar o nome do dia da semana
	 * @param integer $weekDay ( 1=>Segunda, ...., 7=>Domingo)
	 * @param string $language ( "ptbr" )
	 * @return array [
	 * 		0 | dayName => Nome completo do dia
	 * 		1 | dayNameAbbr => Nome abreviado do dia
	 * ]
	 */
	static public function getNamesFromWeekDay( $weekDay, $language = 'ptbr' ) {
		//mapeando array de resposta
		$response = array(
			'dayName' => null,
			'dayNameAbbr' => null
		);
		
		//criando controlador
		$weekDayNames = array(
			'ptbr' => array(
				1 => array('Segunda', 'Seg'),
				2 => array('Terça', 'Ter'),
				3 => array('Quarta', 'Qua'),
				4 => array('Quinta', 'Qui'),
				5 => array('Sexta', 'Sex'),
				6 => array('Sábado', 'Sáb'),
				7 => array('Domingo', 'Dom')
			)
		);
		
		//Buscando dado
		switch ($language) {
			case 'ptbr':
				$index = 'ptbr';
				break;
			default:
				$index = 'ptbr';
				break;
		}
		
		$weekDay = (integer) $weekDay;
		
		$response[0] 				= $weekDayNames[$index][$weekDay][0];
		$response['dayName'] 		= $weekDayNames[$index][$weekDay][0];
		$response[1] 				= $weekDayNames[$index][$weekDay][1];
		$response['dayNameAbbr'] 	= $weekDayNames[$index][$weekDay][1];
		
		return $response;
	}
	
	/**
	 * Método para recuperar o nome do mês
	 * @param integer $month ( 1=>Janeiro, ...., 12=>Dezembro)
	 * @param string $language ( "ptbr" )
	 * @return array [
	 * 		0 | monthName => Nome completo do mes
	 * 		1 | monthNameAbbr => Nome abreviadodo mes
	 * ]
	 */
	static public function getNamesFromMonth( $month, $language = 'ptbr' ) {
		//mapeando array de resposta
		$response = array(
				'monthName' => null,
				'monthNameAbbr' => null
		);
	
		//criando controlador
		$monthNames = array(
			'ptbr' => array(
				1 => array('Janeiro', 'Jan'),
				2 => array('Fevereiro', 'Fev'),
				3 => array('Março', 'Mar'),
				4 => array('Abril', 'Abr'),
				5 => array('Maio', 'Mai'),
				6 => array('Junho', 'Jun'),
				7 => array('Julho', 'Jul'),
				8 => array('Agosto', 'Ago'),
				9 => array('Setembro', 'Set'),
				10 => array('Outubro', 'Out'),
				11 => array('Novembro', 'Nov'),
				12 => array('Dezembro', 'Dez')
			)
		);
	
		//Buscando dado
		switch ($language) {
			case 'ptbr':
				$index = 'ptbr';
				break;
			default:
				$index = 'ptbr';
				break;
		}
	
		$month = (integer) $month;
	
		$response[0] 				= $monthNames[$index][$month][0];
		$response['monthName'] 		= $monthNames[$index][$month][0];
		$response[1] 				= $monthNames[$index][$month][1];
		$response['monthNameAbbr'] 	= $monthNames[$index][$month][1];
	
		return $response;
	}
	
	/**
	 * Método para gerar um permalink a partir de uma string generica
	 * 
	 * @param string $str String a ser processada
	 * @param array $replace Array de sub-strings a ser substituida por espaço
	 * @param string $delimiter
	 * @return string
	 */
	static public function generatePermalink ($str, $replace=array(), $delimiter='-') {
		if( !empty($replace) ) {
			$str = str_replace((array) $replace, ' ', $str);
		}
	
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	
		return $clean;
	}
	
	/**
	 * Método para processar a url do youtube e retornar uma url válida para embed
	 * 
	 * @param string $urlComplete
	 * @return string|null
	 */
	static public function getYoutubeEmbedUrl ($urlComplete) {
		
		$videoId = null;
		$finalUrl = null;
		$queryString = null;
		//Tratando url completa para diferenciar entre url e queryString
		$tmp = explode('?', $urlComplete);
		$url = $tmp[0];
		if( isset($tmp[1]) ) {
			$queryString = $tmp[1];
		}
		$parameters = array();
		if( $queryString ) {
			parse_str($queryString, $parameters);
		}
		
		//A url utiliza o shortUrl com path sendo o v
		//A url utiliza a url embed do Youtube com path final sendo o v
		if( strpos($url, 'youtube.com/embed/') !== false || strpos($url, 'youtu.be/') !== false ) {
			$posBarra = strrpos($url, '/');
			$videoId = substr($url, $posBarra+1);
		}
		//A url utiliza a url normal com parametro v
		else if( strpos($url, 'youtube.com/') !== false ) {
			if( isset($parameters['v']) ) {
				$videoId = $parameters['v'];
			}
		}
		
		//Tendo o video id gero a url menor
		if($videoId) {
			$finalUrl = '//www.youtube.com/embed/'.$videoId;
		}
		
		return $finalUrl;
		
	}
	
	/**
	 * Método para recuperar a quantidade total de memória sendo utilizada pelo sistema
	 * 
	 * @param string $format 
	 * 			Este irá definir o formato que deseja receber o dado [B, KB, MB, array]
	 * 			Caso nenhuma informação seja passada iremos responder em MB
	 * @return multitype:NULL number 
	 */
	static public function getCurrentMemoryUsage ( $format='MB' ) {
		
		//Calculando uso de memória
		$memory = array(
			'B' => memory_get_usage(),
			'KB' => null,
			'MB' => null
		);
		$memory['KB'] = round($memory['B']/1024, 2);
		$memory['MB'] = round($memory['KB']/1024, 2);
		
		//Definindo o formato
		if( !in_array(strtoupper($format), ['B', 'KB', 'MB']) ) {
			$format='MB';
		}
		
		return $memory[strtoupper($format)];
	}
	
	/**
	 * Método para processar uma string que é um link definindo e inserindo o protocolo correto para sua utilização
	 * 
	 * @param string $string
	 * @return 	string Quando sucesso
	 * 			false Quando string incompleta
	 */
	static public function getLinkCompletoFromString ( $string ) {
		
		//Verificando existencia da string
		if( strlen($string) < 4 ) {
			return false;
		}
		
		//VERIFICANDO SE POSSUI HTTP OU HTTPS
		//possui http
		if( strpos($string, 'http://') !== false ) {
			return $string;
		}
		//possui https
		else if ( strpos($string, 'https://') !== false ) {
			return $string;
		}
		//possui algum outro protocolo
		else if ( strpos($string, '://') !== false ) {
			return $string;
		}
		//Não possui protocolos, coloco o protocolo default http
		else {
			return 'http://'.$string;
		}
		
	}
	
	/**
	 * Método para efetuar reload na página para remover o www
	 * Este método só deverá ser executado
	 * 		- Se não for uma execução CLI
	 * 		- Se o servidor possuir o marcador $_SERVER e $_SERVER['SERVER_NAME']
	 */
	static public function relocationNoWWW () {
		
		//Verificando o CLI
		if( defined('SH_CLI') && SH_CLI ) {
			return;
		}
		//Verificando que temos a variavel de servidor
		if( !isset($_SERVER) || !isset($_SERVER['SERVER_NAME']) ) {
			return;
		}
		
		//capturo o dominio requisitado
		$requestDomain = $_SERVER['SERVER_NAME'];
		//Verifico se possuo o www
		if( strpos($requestDomain, 'www.') !== false ) {
			//Definindo protocolo
			$requestProtocol = 'http';
			if(isset($_SERVER['HTTPS'])){
				$requestProtocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
			}
			else if ( strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https/') !== false ) {
				$requestProtocol = 'https';
			}
			//removendo o www. do dominio
			$requestDomain = str_replace('www.', '', $requestDomain);
			header('Location: '.$requestProtocol.'://'.$requestDomain.$_SERVER['REDIRECT_URL']);
		}
	}
	
	/**
	 * remove_comments will strip the sql comment lines out of an uploaded sql file
	 * specifically for mssql and postgres type files in the install....
	 * 
	 * @param string $output
	 * @return string
	 */
	static protected function remove_comments(&$output) {
		$lines = explode ( "\n", $output );
		$output = "";
		
		// try to keep mem. use down
		$linecount = count ( $lines );
		
		$in_comment = false;
		for($i = 0; $i < $linecount; $i ++) {
			if (preg_match ( "/^\/\*/", preg_quote ( $lines [$i] ) )) {
				$in_comment = true;
			}
			
			if (! $in_comment) {
				$output .= $lines [$i] . "\n";
			}
			
			if (preg_match ( "/\*\/$/", preg_quote ( $lines [$i] ) )) {
				$in_comment = false;
			}
		}
		
		unset ( $lines );
		return $output;
	}
	
	/**
	 * remove_remarks will strip the sql comment lines out of an uploaded sql file
	 * 
	 * @param string $sql
	 * @return string
	 */
	static protected function remove_remarks($sql) {
		$lines = explode ( "\n", $sql );
		
		// try to keep mem. use down
		$sql = "";
		
		$linecount = count ( $lines );
		$output = "";
		
		for($i = 0; $i < $linecount; $i ++) {
			if (($i != ($linecount - 1)) || (strlen ( $lines [$i] ) > 0)) {
				if (isset ( $lines [$i] [0] ) && $lines [$i] [0] != "#") {
					$output .= $lines [$i] . "\n";
				} else {
					$output .= "\n";
				}
				// Trading a bit of speed for lower mem. use here.
				$lines [$i] = "";
			}
		}
		
		return $output;
	}
	
	/**
	 * split_sql_file will split an uploaded sql file into single sql statements.
	 * Note: expects trim() to have already been run on $sql.
	 * 
	 * @param string $sql
	 * @param string $delimiter
	 * @return array
	 */
	static protected function split_sql_file($sql, $delimiter) {
		// Split up our string into "possible" SQL statements.
		$tokens = explode ( $delimiter, $sql );
	
		// try to save mem.
		$sql = "";
		$output = array ();
	
		// we don't actually care about the matches preg gives us.
		$matches = array ();
	
		// this is faster than calling count($oktens) every time thru the loop.
		$token_count = count ( $tokens );
		for($i = 0; $i < $token_count; $i ++) {
			// Don't wanna add an empty string as the last thing in the array.
			if (($i != ($token_count - 1)) || (strlen ( $tokens [$i] > 0 ))) {
				// This is the total number of single quotes in the token.
				$total_quotes = preg_match_all ( "/'/", $tokens [$i], $matches );
				// Counts single quotes that are preceded by an odd number of backslashes,
				// which means they're escaped quotes.
				$escaped_quotes = preg_match_all ( "/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens [$i], $matches );
					
				$unescaped_quotes = $total_quotes - $escaped_quotes;
					
				// If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
				if (($unescaped_quotes % 2) == 0) {
					// It's a complete sql statement.
					$output [] = $tokens [$i];
					// save memory.
					$tokens [$i] = "";
				} else {
					// incomplete sql statement. keep adding tokens until we have a complete one.
					// $temp will hold what we have so far.
					$temp = $tokens [$i] . $delimiter;
					// save memory..
					$tokens [$i] = "";
	
					// Do we have a complete statement yet?
					$complete_stmt = false;
	
					for($j = $i + 1; (! $complete_stmt && ($j < $token_count)); $j ++) {
						// This is the total number of single quotes in the token.
						$total_quotes = preg_match_all ( "/'/", $tokens [$j], $matches );
						// Counts single quotes that are preceded by an odd number of backslashes,
						// which means they're escaped quotes.
						$escaped_quotes = preg_match_all ( "/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens [$j], $matches );
							
						$unescaped_quotes = $total_quotes - $escaped_quotes;
							
						if (($unescaped_quotes % 2) == 1) {
							// odd number of unescaped quotes. In combination with the previous incomplete
							// statement(s), we now have a complete statement. (2 odds always make an even)
							$output [] = $temp . $tokens [$j];
	
							// save memory.
							$tokens [$j] = "";
							$temp = "";
	
							// exit the loop.
							$complete_stmt = true;
							// make sure the outer loop continues at the right point.
							$i = $j;
						} else {
							// even number of unescaped quotes. We still don't have a complete statement.
							// (1 odd and 1 even always make an odd)
							$temp .= $tokens [$j] . $delimiter;
							// save memory.
							$tokens [$j] = "";
						}
					} // for..
				} // else
			}
		}
	
		return $output;
	}
	
	/**
	 * Método para importar um arquivo .sql a um database
	 * 
	 * @param string $filePath
	 * @param \PDO $conn
	 * @throws \Sh\SheerException
	 */
	static public function importFileToDatabase ($filePath, \PDO $conn) {
	
		$sqlSource = file_get_contents($filePath);
		if( !$sqlSource ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Arquivo .sql não encontrado'
			));
		}
		$sqlQueries = self::remove_remarks($sqlSource);
		$queries = self::split_sql_file($sqlQueries,';');
		
		foreach ($queries as $sql) {
			$response = $conn->exec($sql);
			if( $response === false ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Erro ao executar query "'.$sql.'"'
				));
			}
		}
		
	}
	
}

/**
 * @author Guilherme
 * 
 * Classe criada para ser uma biblioteca de opcões padrões para fields
 *
 */
abstract class LibraryFieldOptions {
	
	/**
	 * Variável de controle de dias da semana seguindo a norma ISO-8601 ou 'N' no php
	 * @var array
	 */
	static public $diaSemana = array(
		1 => 'Segunda',
		2 => 'Terça',
		3 => 'Quarta',
		4 => 'Quinta',
		5 => 'Sexta',
		6 => 'Sábado',
		7 => 'Domingo'
	);
	
	/**
	 * Variável de mapeamento de opções booleanas com valores inteiros para "Sim" e "Não"
	 * @var array
	 */
	static public $simNao = array(
		1 => 'Sim',
		2 => 'Não'
	);
	
	/**
	 * Variável para controlar o status de ativo dos modulos dos sim
	 * 1 => 'Ativo', 2 => 'Encerrado', 3 => 'Aguardando'
	 * @var array
	 */
	static public $simStatusAtivo = array(
		1 => 'Ativo',
		2 => 'Encerrado',
		3 => 'Aguardando'
	);
	
	/**
	 * Variável de mapeamento de opções booleanas com valores inteiros para "Habilitado" e "Desabilitado"
	 * @var array
	 */
	static public $habilitado = array(
		1 => 'Habilitado',
		2 => 'Desabilitado'
	);
	
}