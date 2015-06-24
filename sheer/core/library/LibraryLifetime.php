<?php

namespace Sh;

abstract class LibraryLifetime {
	
	/**
	 * Método para filtrar um conjunto de dados determinando o estágio de vida do dado considerando DataInicio e DataFim
	 * Este aceita configurações próprias para determinação dos estágios
	 * 
	 * @param array $collection Coleção de dados
	 * @param array $config 
	 * 		date => Data a ser tratada como referencia para calculo
	 * 		dataInicioLabel => Label para chave de DataInicio
	 * 		dataFimLabel => Label para chave de DataFim
	 * 		alive => Valor do filtro de retorno para estágio de vida do dado
	 * 			1=>Ativo, 2=>Encerrado, 3=>Aguardando
	 * 
	 * @return array
	 */
	static public function getLiveDataFromCollection ($collection, $config=array()) {
		
		if( !$collection ) {
			return array();
		}
		
		//determinando data de referencia
		if( !isset($config['date']) || !$config['date'] ) {
			$config['date'] = \Sh\FieldDate::formatInputDataToSheer(date('d/m/Y'));
		}
		//verificando se data passada é do tipo input
		else if ( is_string($config['date']) ) {
			$config['date'] = \Sh\FieldDate::formatInputDataToSheer($config['date']);
		}
		
		//determinando dataInicio e dataFim
		if( !isset($config['dataInicioLabel']) || !$config['dataInicioLabel'] ) {
			$config['dataInicioLabel'] = 'dataInicio';
		}
		if( !isset($config['dataFimLabel']) || !$config['dataFimLabel'] ) {
			$config['dataFimLabel'] = 'dataFim';
		}
		if( !isset($config['alive']) || !$config['alive'] ) {
			$config['alive'] = '1';
		}
		
		//determinando variaveis de controle
		$newCollection = array();
		
		foreach ( $collection as $id=>$element ) {
			
			$alive = self::getDataLifeStatus($element[$config['dataInicioLabel']], $element[$config['dataFimLabel']], $config['date']);
			if( $alive != $config['alive'] ) {
				continue;
			}
			$newCollection[$id] = $element;
		}
		
		return $newCollection;
	}
	
	/**
	 * Método para obter o status de vida do dado em um período dado
	 * 
	 * @param array $dataInicio SheerDate
	 * @param array $dataFim SheerDate
	 * @param array $dataComparar SheerDate
	 * @return number
	 * 		false => Erro ao tentar efetuar as operações
	 * 		1 => Ativo
	 * 		2 => Encerrado
	 * 		3 => Aguardando
	 */
	static public function getDataLifeStatus ( $dataInicio, $dataFim=null, $dataComparar=null ) {
		
		//verificando a data de inicio
		if( !\Sh\FieldDate::isSheerFormat($dataInicio) ) {
			return false;
		}
		//determinando a dataFim. Se não for uma data válida assumo null
		if( $dataFim && !\Sh\FieldDate::isSheerFormat($dataFim) ) {
			$dataFim = null;
		}
		
		//determinando data de comparacao
		if( !$dataComparar ) {
			$dataComparar = \Sh\FieldDate::formatInputDataToSheer(date('d/m/Y'));
		}
		//verificando a data de comparacao
		if( !\Sh\FieldDate::isSheerFormat($dataComparar) ) {
			return false;
		}
		
		//COMPARANDO AS DATAS
		//caso ainda não tenha alcançado a dataInicio retorno "AGUARDANDO"
		if( $dataComparar['dateTime'] < $dataInicio['dateTime'] ) {
			return 3;
		}
		//se o nosso dataFim existir e for anterior a nossa data retorno "ENCERRADO"
		else if ( $dataFim && ($dataComparar['dateTime'] > $dataFim['dateTime']) ) {
			return 2;
		}
		//em caso contrário o vinculo está "ATIVO"
		return 1;
		
	}
}