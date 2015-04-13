<?php

namespace Sh;

/**
 * @author Guilherme
 *
 * Biblioteca de validação de dados
 */
abstract class LibraryValidation {
	
	static public $xmlSchemaBasedError = null;
	
	/**
	 * Validar um xml contra o seu xsd
	 * 
	 * @param string $xmlPath
	 * @param string $schemaPath
	 * @return boolean
	 */
	static public function validateXmlFromSchema ($xmlPath, $schemaPath) {
		$xml = new \DOMDocument();
		$xml->load($xmlPath);
		$validacao = $xml->schemaValidate($schemaPath);
		return $validacao;
	}
	
	/**
	 * Método para validar telefone
	 * 
	 * @param string $string
	 * @return boolean
	 */
	static public function validateTelefone ( $string ) {
		$matches = preg_match('/^\([0-9]{2}\) [0-9]{4,5}\.[0-9]{4,5}$/', $string);
		return (boolean) $matches;
	}
	
	/**
	 * Método para validar email
	 * 
	 * @param unknown $string
	 * @return boolean
	 */
	static public function validateEmail ( $string ) {
		return !!filter_var($string, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * Método para validar IP
	 * 
	 * @param unknown $string
	 * @return boolean
	 */
	static public function validateIP( $string ) {
		return !!filter_var($ip_a, $string);
	}
	
	/**
	 * Método para validar CPF.
	 * 
	 * @param string $string
	 * @return boolean
	 */
	static public function validateCPF( $string ) {
		
	    if( !$string || strlen($string) < 11 ) {
	        return false;
	    }
	 
	    // ELIMANDO CARACTERES NÃO NUMÉRICO
	    $cpf = preg_replace('/[^0-9]/', '', $string);
	    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
	     
	    if (strlen($cpf) != 11) {
	        return false;
	    }
	    else if (
	    	$cpf == '00000000000' 
	        || $cpf == '11111111111' 
	        || $cpf == '22222222222' 
	        || $cpf == '33333333333' 
	       	|| $cpf == '44444444444' 
	        || $cpf == '55555555555' 
	        || $cpf == '66666666666' 
	        || $cpf == '77777777777' 
	        || $cpf == '88888888888' 
	        || $cpf == '99999999999'
		) {
	        return false;
		} 
		// Calcula os digitos verificadores para verificar se o
		// CPF é válido
		else {
			for ($t = 9; $t < 11; $t++) {
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += $cpf{$c} * (($t + 1) - $c);
				}
				$d = ((10 * $d) % 11) % 10;
				if ($cpf{$c} != $d) {
					return false;
				}
			}
			return true;
		}
	}
	
	/**
	 * Método para validar se um CNPJ é válido com seu verificador
	 * 
	 * @param string $cnpj
	 * @return boolean
	 */
	static public function validateCnpj ( $cnpj ) {
		// Deixa o CNPJ com apenas números
		$cnpj = preg_replace ( '/[^0-9]/', '', $cnpj );
		
		// Garante que o CNPJ é uma string
		$cnpj = (string) $cnpj;
		
		// O valor original
		$cnpj_original = $cnpj;
		
		// Captura os primeiros 12 números do CNPJ
		$primeiros_numeros_cnpj = substr ( $cnpj, 0, 12 );
		
		// Faz o primeiro cálculo
		$primeiro_calculo = self::multiplica_cnpj ( $primeiros_numeros_cnpj );
		
		// Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
		// Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
		$primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 : 11 - ($primeiro_calculo % 11);
		
		// Concatena o primeiro dígito nos 12 primeiros números do CNPJ
		// Agora temos 13 números aqui
		$primeiros_numeros_cnpj .= $primeiro_digito;
		
		// O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
		$segundo_calculo = self::multiplica_cnpj ( $primeiros_numeros_cnpj, 6 );
		$segundo_digito = ($segundo_calculo % 11) < 2 ? 0 : 11 - ($segundo_calculo % 11);
		
		// Concatena o segundo dígito ao CNPJ
		$cnpj = $primeiros_numeros_cnpj . $segundo_digito;
		
		// Verifica se o CNPJ gerado é idêntico ao enviado
		if ($cnpj === $cnpj_original) {
			return true;
		}
		return false;
	}
	/**
	 * Multiplicação do CNPJ
	 *
	 * @param string $cnpj
	 *        	Os digitos do CNPJ
	 * @param int $posicoes
	 *        	A posição que vai iniciar a regressão
	 * @return int O
	 *
	 */
	static private function multiplica_cnpj($cnpj, $posicao = 5) {
		// Variável para o cálculo
		$calculo = 0;
			
		// Laço para percorrer os item do cnpj
		for($i = 0; $i < strlen ( $cnpj ); $i ++) {
			// Cálculo mais posição do CNPJ * a posição
			$calculo = $calculo + ($cnpj [$i] * $posicao);
	
			// Decrementa a posição a cada volta do laço
			$posicao --;
	
			// Se a posição for menor que 2, ela se torna 9
			if ($posicao < 2) {
				$posicao = 9;
			}
		}
		// Retorna o cálculo
		return $calculo;
	}
	
	static public function validaCNS ( $string ) {
		
		// ELIMANDO CARACTERES NÃO NUMÉRICO
		$cns = preg_replace('/[^0-9]/', '', $string);
		
		if ( strlen(trim($cns)) != 15) {
			return false;
		}
		
		//determinando tipo de validacao, inicio [1,2] ou [7,8,9]
		if( $cns[0] == '1' || $cns[0] == '2' ) {
			return self::validaCNSInicio12($cns);
		}
		else if ( $cns[0] == '7' || $cns[0] == '8' || $cns[0] == '9' ) {
			return self::validaCNSInicio789($cns);
		}
		else {
			return false;
		}
		
	}
	
	/**
	 * Método para validar Cartão SUS com inicio em 1 e 2
	 * 
	 * @param string $cns
	 * @return boolean
	 */
	static protected function validaCNSInicio12 ( $cns ) {
		
		$pis = substr($cns,0,11);
		$soma = (((substr($pis, 0,1)) * 15) +
				((substr($pis, 1,1)) * 14) +
				((substr($pis, 2,1)) * 13) +
				((substr($pis, 3,1)) * 12) +
				((substr($pis, 4,1)) * 11) +
				((substr($pis, 5,1)) * 10) +
				((substr($pis, 6,1)) * 9) +
				((substr($pis, 7,1)) * 8) +
				((substr($pis, 8,1)) * 7) +
				((substr($pis, 9,1)) * 6) +
				((substr($pis, 10,1)) * 5));
		
		$resto = fmod($soma, 11);
		$dv = 11  - $resto;
		if ($dv == 11) {
			$dv = 0;
		}
		if ($dv == 10) {
			$soma = ((((substr($pis, 0,1)) * 15) +
					((substr($pis, 1,1)) * 14) +
					((substr($pis, 2,1)) * 13) +
					((substr($pis, 3,1)) * 12) +
					((substr($pis, 4,1)) * 11) +
					((substr($pis, 5,1)) * 10) +
					((substr($pis, 6,1)) * 9) +
					((substr($pis, 7,1)) * 8) +
					((substr($pis, 8,1)) * 7) +
					((substr($pis, 9,1)) * 6) +
					((substr($pis, 10,1)) * 5)) + 2);
			$resto = fmod($soma, 11);
			$dv = 11  - $resto;
			$resultado = $pis."001".$dv;
		}
		else {
			$resultado = $pis."000".$dv;
		}
		
		if ($cns != $resultado){
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Método para validar Cartão SUS com inicio em 7, 8 e 9
	 *
	 * @param string $cns
	 * @return boolean
	 */
	static protected function validaCNSInicio789 ( $cns ) {
		
		if ((strlen(trim($cns))) != 15) {
			return false;
		}
		$soma = (((substr($cns,0,1)) * 15) +
				((substr($cns,1,1)) * 14) +
				((substr($cns,2,1)) * 13) +
				((substr($cns,3,1)) * 12) +
				((substr($cns,4,1)) * 11) +
				((substr($cns,5,1)) * 10) +
				((substr($cns,6,1)) * 9) +
				((substr($cns,7,1)) * 8) +
				((substr($cns,8,1)) * 7) +
				((substr($cns,9,1)) * 6) +
				((substr($cns,10,1)) * 5) +
				((substr($cns,11,1)) * 4) +
				((substr($cns,12,1)) * 3) +
				((substr($cns,13,1)) * 2) +
				((substr($cns,14,1)) * 1));
		$resto = fmod($soma,11);
		if ($resto != 0) {
			return false;
		}
		return true;
	}
}

