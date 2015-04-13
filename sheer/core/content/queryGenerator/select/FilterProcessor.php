<?php

namespace Sh\QueryGenerator;

/**
 * @author guilherme
 * Classe responsável por resolver o filtro em questão sabendo tratar a sua existencia de valores
 */
class FilterProcessor {
	
	protected $filter;
	protected $filterData;
	protected $field;
	
	public function __construct($filter, \Sh\DataSourceField $field, $filterData) {
		$this->filter 		= $filter;
		$this->filterData	= $filterData;
		$this->field 		= $field;
	}
	
	/**
	 * Método processador e gerador da query de filtro
	 * Ele deverá receber a configuração de filtro e os valores completos passados para o ContentProvider
	 * @param array $filter
	 * @param \Sh\DataSourceField Será utilizado para parsear para a forma primitiva
	 * @param array $filterData
	 * @return string
	 */
	public function processFilter () {
		
		//FIXME gambiarra para deter erro de variavel não setada
		$filter = $this->filter;
		$field = $this->field;
		$filterData = $this->filterData;
		
		//controladores
		$valorEnviado = false;
		$valor = null;
		$valorDefault = null;
		$queryFilter = '';
		
		//FIXME arrumando buraco que restou do filter
		if( !isset($this->filter['useNullIfBlank']) ) {
			$this->filter['useNullIfBlank'] = false;
		}
		
		//PRECISO DETERMINAR SE O VALOR FOI ENVIADO
		if( isset($filterData[$this->filter['parameter']]) ) {
			$valorEnviado = true;
		}
		
		//SE O VALOR FOI ENVIADO PRECISO DETERMINAR O VALOR QUE FOI ENVIADO
		//NESTE MOMENTO CONVERTO
				//ARRAY VAZIO => NULL
				//STRING VAZIA => NULL
		if( $valorEnviado ) {
			$valor = $filterData[$this->filter['parameter']];
			//verificando se é array e se esta vazio
			//verificando se é string e se esta vazia
			if( (is_array($valor) && count($valor)===0) || (is_string($valor) && strlen($valor)===0) ) {
				$valor = null;
			}
		}
		
		//CAPTURANDO DEFAULT VALUE CASO EXISTA
		$valorDefaultEntrada = $this->filter['defaultValue'];
		if ( $valorDefaultEntrada && \Sh\RuntimeVariables::isAliasValue($valorDefaultEntrada) ) {
			$valorDefault = \Sh\RuntimeVariables::getAliasValue($valorDefaultEntrada);
		}
		else if ( $valorDefaultEntrada ) {
			$valorDefault = $valorDefaultEntrada;
		}
		
		//CASO O FILTRO SEJA OBRIGATÓRIO
		if( $this->filter['required'] ) {
			
			//VERIFICANDO SE O OPERADOR É DE "DATAFIMVALIDO" PARA QUANDO ESTE FOR REQUIRED NAO PRECISAR DE PARAMETRO
			if( $this->filter['operator'] == 'dataFimValido' ) {
				$queryFilter = $this->getComparisonClauseFromValue($filter, $field, 1);
			}
			
			//TENDO O VALOR ENVIADO PARA FILTRO
			else if( $valorEnviado ) {
				//determino se o valor é nulo
				if( $valor === null ) {
					$queryFilter = '0=1';
					//caso seja para utilizar NULL se estiver vazio, o faremos
					if( $this->filter['useNullIfBlank'] ) {
						$tmpFilter = $filter;
						$tmpFilter['operator'] = 'isNull';
						$queryFilter = $this->getComparisonClauseFromValue($tmpFilter, $field, 1);
					}
				}
				else {
					//preciso capturar o valor a partir de valor
					$queryFilter = $this->getComparisonClauseFromValue($filter, $field, $valor);
				}
			}
			//NAO TENDO O VALOR ENVIADO PARA FILTRO
			else {
				if( $valorDefault ) {
					///preciso capturar o valor a partir do valor default
					$queryFilter = $this->getComparisonClauseFromValue($filter, $field, $valorDefault);
				}
				else {
					$queryFilter = '0=1';
					//caso seja para utilizar NULL se estiver vazio, o faremos
					if( $this->filter['useNullIfBlank'] ) {
						$tmpFilter = $filter;
						$tmpFilter['operator'] = 'isNull';
						$queryFilter = $this->getComparisonClauseFromValue($tmpFilter, $field, 1);
					}
				}
			}
		}
		//NÃO OBRIGATÓRIO
		else {
			//determino se o valor foi enviado
			if( $valorEnviado ) {
				//determino se o valor é nulo
				if( $valor === null ) {
					$queryFilter = '1=1';
				}
				else {
					//preciso capturar o valor a partir de valor
					$queryFilter = $this->getComparisonClauseFromValue($filter, $field, $valor);
				}
			}
			else {
				if( $valorDefault ) {
					///preciso capturar o valor a partir do valor default
					$queryFilter = $this->getComparisonClauseFromValue($filter, $field, $valorDefault);
				}
				else {
					$queryFilter = '1=1';
				}
			}
		}
		
		return $queryFilter;
		
	}
	
	/**
	 * Método interno para buscar a query a partir das configurações de filtro e o valor final a ser aplicado
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param string|array $valor
	 * @return string
	 */
	protected function getComparisonClauseFromValue ($filter, \Sh\DataSourceField $field, $valor) {
		$comparison = '';
		
		//DEPENDENDO DO MODELO DO FILTRO IREMOS EXECUTAR E GERAR A QUERY DESEJADA
		switch ($filter['operator']) {
			case 'equal': 
				$comparison = $this->getEqualComparison($filter, $field, $valor); 
				break;
			case 'like':
				$comparison = $this->getLikeComparison($filter, $field, $valor);
				break;
			case 'likeSplit':
				$comparison = $this->getLikeSplitComparison($filter, $field, $valor);
				break;
			case 'greater':
				$comparison = $this->getGreaterComparison($filter, $field, $valor);
				break;
			case 'greaterOrEqual':
				$comparison = $this->getGreaterOrEqualComparison($filter, $field, $valor);
				break;
			case 'less':
				$comparison = $this->getLessComparison($filter, $field, $valor);
				break;
			case 'lessOrEqual':
				$comparison = $this->getLessOrEqualComparison($filter, $field, $valor);
				break;
			case 'different':
				$comparison = $this->getDifferentComparison($filter, $field, $valor);
				break;
			case 'in':
				$comparison = $this->getInComparison($filter, $field, $valor);
				break;
			case 'notIn':
				$comparison = $this->getNotInComparison($filter, $field, $valor);
				break;
			case 'isNull':
				$comparison = $this->getIsNullComparison($filter);
				break;
			case 'isNotNull':
				$comparison = $this->getIsNotNullComparison($filter);
				break;
			case 'periodFuture':
				$comparison = $this->getPeriodFutureComparison($filter, $field, $valor);
				break;
			case 'periodPast':
				$comparison = $this->getPeriodPastComparison($filter, $field, $valor);
				break;
			//TODO remover este tipo de filtro
			case 'dataFimValido':
				$comparison = $this->getDataFimValidoComparison($filter);
				break;
			//para o default uso o modelo "EQUAL"
			default: 
				$comparison = $this->getEqualComparison($filter, $field, $valor);
				break;
		}
		
		return $comparison;
		
	}
	
	/**
	 * Método de geração de cláusula de comparação "EQUAL"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getEqualComparison ($filter, \Sh\DataSourceField $field, $valor) {
		
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR 
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"'.$valor.'"';
		
		//PROCESSANDO DATE FUNCTION 
		if( $filter['dateFunction'] ) {
			//Verificando se é dayMonth
			if( $filter['dateFunction'] == 'dayMonth' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%m%d")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%m%d")';
			}
			//Verificando se é monthYear
			else if( $filter['dateFunction'] == 'monthYear' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%Y%m")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%Y%m")';
			}
			else {
				$leftComparison 	= strtoupper($filter['dateFunction']).'('.$leftComparison.')';
				$rightComparison 	= strtoupper($filter['dateFunction']).'('.$rightComparison.')';
			}
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' = '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "LIKE"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getLikeComparison ($filter, \Sh\DataSourceField $field, $valor) {
		
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"%'.$valor.'%"';
		
		//PROCESSANDO DATE FUNCTION
		if( $filter['dateFunction'] ) {
			\Sh\LoggerProvider::log('warning', 'Não é possível utilizar funções de data (dateFunction) em cláusulas de filtros com Operador "LIKE".');
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' LIKE '.$rightComparison;
		return $comparison;
		
	}
	
	/**
	 * Método de geração de cláusula de comparação "LIKESPLIT"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getLikeSplitComparison ($filter, \Sh\DataSourceField $field, $valor) {
		
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//removendo espacos duplicados da string
		$valor = preg_replace('/(\s)+/', ' ', $valor);
		$valor = str_replace(' ', '% %', $valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"%'.$valor.'%"';
		
		//PROCESSANDO DATE FUNCTION
		if( $filter['dateFunction'] ) {
			\Sh\LoggerProvider::log('warning', 'Não é possível utilizar funções de data (dateFunction) em cláusulas de filtros com Operador "LIKE".');
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' LIKE '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "Greater"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getGreaterComparison ($filter, \Sh\DataSourceField $field, $valor) {
	
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"'.$valor.'"';
		
		//PROCESSANDO DATE FUNCTION 
		if( $filter['dateFunction'] ) {
			//Verificando se é dayMonth
			if( $filter['dateFunction'] == 'dayMonth' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%m%d")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%m%d")';
			}
			//Verificando se é monthYear
			else if( $filter['dateFunction'] == 'monthYear' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%Y%m")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%Y%m")';
			}
			else {
				$leftComparison 	= strtoupper($filter['dateFunction']).'('.$leftComparison.')';
				$rightComparison 	= strtoupper($filter['dateFunction']).'('.$rightComparison.')';
			}
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' > '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "GreaterOrEqual"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getGreaterOrEqualComparison ($filter, \Sh\DataSourceField $field, $valor) {
	
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"'.$valor.'"';
		
		//PROCESSANDO DATE FUNCTION 
		if( $filter['dateFunction'] ) {
			//Verificando se é dayMonth
			if( $filter['dateFunction'] == 'dayMonth' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%m%d")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%m%d")';
			}
			//Verificando se é monthYear
			else if( $filter['dateFunction'] == 'monthYear' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%Y%m")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%Y%m")';
			}
			else {
				$leftComparison 	= strtoupper($filter['dateFunction']).'('.$leftComparison.')';
				$rightComparison 	= strtoupper($filter['dateFunction']).'('.$rightComparison.')';
			}
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' >= '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "Less"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getLessComparison ($filter, \Sh\DataSourceField $field, $valor) {
	
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"'.$valor.'"';
		
		//PROCESSANDO DATE FUNCTION 
		if( $filter['dateFunction'] ) {
			//Verificando se é dayMonth
			if( $filter['dateFunction'] == 'dayMonth' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%m%d")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%m%d")';
			}
			//Verificando se é monthYear
			else if( $filter['dateFunction'] == 'monthYear' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%Y%m")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%Y%m")';
			}
			else {
				$leftComparison 	= strtoupper($filter['dateFunction']).'('.$leftComparison.')';
				$rightComparison 	= strtoupper($filter['dateFunction']).'('.$rightComparison.')';
			}
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' < '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "LessOrEqual"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getLessOrEqualComparison ($filter, \Sh\DataSourceField $field, $valor) {
	
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"'.$valor.'"';
		
		//PROCESSANDO DATE FUNCTION 
		if( $filter['dateFunction'] ) {
			//Verificando se é dayMonth
			if( $filter['dateFunction'] == 'dayMonth' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%m%d")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%m%d")';
			}
			//Verificando se é monthYear
			else if( $filter['dateFunction'] == 'monthYear' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%Y%m")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%Y%m")';
			}
			else {
				$leftComparison 	= strtoupper($filter['dateFunction']).'('.$leftComparison.')';
				$rightComparison 	= strtoupper($filter['dateFunction']).'('.$rightComparison.')';
			}
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' <= '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "LessOrEqual"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function getDifferentComparison ($filter, \Sh\DataSourceField $field, $valor) {
	
		$valorOriginal = $valor;
		
		//PROCESSANDO VALOR
		$valor = $field::formatInputDataToPrimitive($valor);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '"'.$valor.'"';
		
		//PROCESSANDO DATE FUNCTION 
		if( $filter['dateFunction'] ) {
			//Verificando se é dayMonth
			if( $filter['dateFunction'] == 'dayMonth' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%m%d")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%m%d")';
			}
			//Verificando se é monthYear
			else if( $filter['dateFunction'] == 'monthYear' ) {
				$leftComparison 	= 'DATE_FORMAT('.$leftComparison.', "%Y%m")';
				$rightComparison 	= 'DATE_FORMAT('.$rightComparison.', "%Y%m")';
			}
			else {
				$leftComparison 	= strtoupper($filter['dateFunction']).'('.$leftComparison.')';
				$rightComparison 	= strtoupper($filter['dateFunction']).'('.$rightComparison.')';
			}
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' <> '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "In"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param string|array $valor
	 * @return string
	 */
	protected function getInComparison ($filter, \Sh\DataSourceField $field, $valor) {
	
		$valorOriginal = $valor;
		$valor = $this->createInNotInValueClause($filter, $field, $valorOriginal);
		
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '('.$valor.')';
		
		//PROCESSANDO DATE FUNCTION
		//neste só utilizo o dateFunction no leftComparison pois o do lado direito é aplicado dentro do processamento de cláusula do in e notIn
		if( $filter['dateFunction'] ) {
			\Sh\LoggerProvider::log('warning', 'Não é possível utilizar funções de data (dateFunction) em cláusulas de filtros com Operador "LIKE".');
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' IN '.$rightComparison;
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "NotIn"
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param string|array $valor
	 * @return string
	 */
	protected function getNotInComparison ($filter, \Sh\DataSourceField $field, $valor) {
	
		$valorOriginal = $valor;
		$valor = $this->createInNotInValueClause($filter, $field, $valorOriginal);
	
		//criando blocos de comparacao
		$leftComparison 	= 't'.$filter['relationIndex'].'.'.$filter['field'];
		$rightComparison 	= '('.$valor.')';
		
		//PROCESSANDO DATE FUNCTION
		//neste só utilizo o dateFunction no leftComparison pois o do lado direito é aplicado dentro do processamento de cláusula do in e notIn
		if( $filter['dateFunction'] ) {
			\Sh\LoggerProvider::log('warning', 'Não é possível utilizar funções de data (dateFunction) em cláusulas de filtros com Operador "LIKE".');
		}
		
		//CRIANDO CLAUSULA DE COMPARACAO
		$comparison = $leftComparison.' NOT IN '.$rightComparison;
		return $comparison;
	
	}
	
	/**
	 * Método de geração de cláusula de comparação "IsNull"
	 * @param array $filter
	 * @return string
	 */
	protected function getIsNullComparison ($filter) {
		$comparison = 't'.$filter['relationIndex'].'.'.$filter['field'].' IS NULL';
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "IsNotNull"
	 * @param array $filter
	 * @return string
	 */
	protected function getIsNotNullComparison ($filter) {
		$comparison = 't'.$filter['relationIndex'].'.'.$filter['field'].' IS NOT NULL';
		return $comparison;
	}

	/**
	 * Método para gerar a clausula de comparação de periodo passado
	 * 
	 * Este método irá procurar pelo parametro "parameter_beginDate" para utilizar como data base
	 * 
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param string $valor
	 * 
	 * @return string
	 */
	protected function getPeriodFutureComparison ($filter, \Sh\DataSourceField $field, $valor) {
		
		//Criando o período para subtrair da data atual
		$interval = null;
		try {
			$interval = new \DateInterval($valor);
		}
		catch (\Exception $e) {
			//Caso tenha acontecido algum erro ao criar o período, retornarmos uma query não trará ninguém.
			return '0=1';
		}
		
		//Criando data de comparação atual
		$paramBeginDate = $filter['parameter'].'_beginDate';
		$dataInicio = null;
		if( isset($this->filterData[$paramBeginDate]) ) {
			$dataInicio = \Sh\FieldDateTime::formatInputDataToSheer($this->filterData[$paramBeginDate]);
		}
		else {
			$dataInicio = new \DateTimeImmutable('now');
		}
		
		//Determinando dataFinal
		$dataFinal = $dataInicio->sub($interval);
		if( !$dataFinal ) {
			return '0=1';
		}
		
		//Criando a comparação
		$comparison = '( ';
			//comparacao de inicio
			$comparison .= 't'.$filter['relationIndex'].'.'.$filter['field'].' >= "'.$dataInicio->format('Y-m-d H:i:s').'" AND ';
			//comparacao de fim
			$comparison .= 't'.$filter['relationIndex'].'.'.$filter['field'].' <= "'.$dataFinal->format('Y-m-d H:i:s').'"';
		$comparison .= ' )';
		
		//Retornando
		return $comparison;
	}
	
	/**
	 * Método para gerar a clausula de comparação de periodo para o passado
	 * 
	 * Este método irá procurar pelo parametro "parameter_beginDate" para utilizar como data base
	 * 
	 * @param array $filter
	 * @param \Sh\DataSourceField $field
	 * @param string $valor
	 * 
	 * @return string
	 */
	protected function getPeriodPastComparison ($filter, \Sh\DataSourceField $field, $valor) {
		
		//Criando o período para subtrair da data atual
		$interval = null;
		try {
			$interval = new \DateInterval($valor);
		}
		catch (\Exception $e) {
			//Caso tenha acontecido algum erro ao criar o período, retornarmos uma query não trará ninguém.
			return '0=1';
		}
	
		//Criando data de comparação atual
		$paramBeginDate = $filter['parameter'].'_beginDate';
		$dataInicio = null;
		if( isset($this->filterData[$paramBeginDate]) ) {
			$dataInicio = \Sh\FieldDateTime::formatInputDataToSheer($this->filterData[$paramBeginDate]);
			$dataInicio = $dataInicio['dateTime'];
		}
		else {
			$dataInicio = new \DateTime('now');
		}
	
		//Determinando dataFinal
		$dataFinal = clone $dataInicio;
		$dataFinal = $dataFinal->sub($interval);
		if( !$dataFinal ) {
			return '0=1';
		}
	
		//Criando a comparação
		$comparison = '( ';
			//comparacao de inicio
			$comparison .= 't'.$filter['relationIndex'].'.'.$filter['field'].' <= "'.$dataInicio->format('Y-m-d H:i:s').'" AND ';
			//comparacao de fim
			$comparison .= 't'.$filter['relationIndex'].'.'.$filter['field'].' >= "'.$dataFinal->format('Y-m-d H:i:s').'"';
		$comparison .= ' )';
		
		//Retornando
		return $comparison;
	}
	
	/**
	 * Método de geração de cláusula de comparação "IsNull"
	 * @param array $filter
	 * @return string
	 */
	protected function getDataFimValidoComparison ($filter) {
		$fieldAlias = 't'.$filter['relationIndex'].'.'.$filter['field'];
		$comparison = '( '.$fieldAlias.' IS NULL OR '.$fieldAlias.' >= CURDATE() )';
		return $comparison;
	}
	
	
	/**
	 * Método para criar o valor final de uma comparação IN ou NOT IN.
	 * Irá processar o valor tanto sendo uma string quanto um array de valores
	 * @param \Sh\DataSourceField $field
	 * @param unknown $valor
	 * @return string
	 */
	protected function createInNotInValueClause ($filter, \Sh\DataSourceField $field, $valor) {
		
		$tmp = '';
		
		//CRIANDO O ARRAY DE MATCHES
		$matches = $valor;
		if( !is_array( $matches ) ) {
			$matches = preg_split('/[,;](\s*)/', $valor);
		}
		
		//PROCESSANDO TODoS OS VALORES
		if( $matches ) {
			$first = true;
			foreach ($matches as $match) {
				//removendo parametros nulos
				if(!$match) {
					continue;
				}
			
				//FORMATANDO O VALOR
				$match = $field::formatInputDataToPrimitive($match);
				$match = '"'.$match.'"';
			
				//PROCESSANDO DATE FUNCTION
				if( $filter['dateFunction'] ) {
					$match = strtoupper($filter['dateFunction']).'('.$match.')';
				}
			
				//gerando query
				if ( $first ) {
					$first = false;
					$tmp .= $match;
				}
				else {
					$tmp .= ','.$match;
				}
			}
			$valor = $tmp;
		}
		
		return $valor;
	}
	
}