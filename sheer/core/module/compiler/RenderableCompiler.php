<?php

namespace Sh;

abstract class RenderableCompiler {
	
	/**
	 * Método compilador de Renderable, Fazemos
	 * 		compilar as informações básicas, 
	 * 		compilar referencia de dataSource, dataProvider e Styles
	 * 		devolver o Renderable completo
	 * 
	 * @param \SimpleXMLElement $xmlRenderable
	 * @param \Sh\Module $module
	 * @throws \Sh\SheerException
	 * @return \Sh\DataSource
	 */
	static public function compile(\SimpleXMLElement $xmlRenderable, \Sh\Module $module) {
		
		$id = (string) $xmlRenderable->attributes()->id;
		
		$renderable = new \Sh\Renderable($module->id, $id);
		
		//PROCESSANDO OS DATASOURCES
		if( isset($xmlRenderable->dataSources) && isset($xmlRenderable->dataSources->dataSource) ) {
			foreach ($xmlRenderable->dataSources->dataSource as $xmlDataSource) {
				//criando dataSource
				//verificando se o dataSource tem módulo externo ou não
				$idDataSource = (string) $xmlDataSource->attributes()->id;
				if( strpos($idDataSource, '/') === false  ) {
					$idDataSource = $module->id .'/'. $idDataSource;
				}
				$dataSource = new \Sh\RenderableDataSource($idDataSource);
				//inserindo no renderable
				$renderable->pushDataSource($dataSource);
			}
		}
		
		//PROCESSANDO OS DATAPROVIDERS
		if( isset($xmlRenderable->dataProviders) && isset($xmlRenderable->dataProviders->dataProvider) ) {
			foreach ($xmlRenderable->dataProviders->dataProvider as $xmlDataProvider) {

				//verificando se o dataProvider tem módulo externo ou não
				$idDataProvider = (string) $xmlDataProvider->attributes()->id;
				if( strpos($idDataProvider, '/') === false ) {
					$idDataProvider = $module->id .'/'. $idDataProvider;
				}
				
				/*
				 * PROCESSANDO OS FILTER PROCESSORS
				 * Se existir o nó filho chamado filterProcessor irei considerar apenas ele.
				 * Caso não exista irei considerar o parametro filterProcessor da tag e determinar a função que é responsável por tratar os parametros
				 */
				$filterProcessor = array(
					'function' => null,
					'filters' => null
				);
				if( isset($xmlDataProvider->filterProcessor) ) {
					//determinando funcao para filter processor
					if( isset($xmlDataProvider->filterProcessor->attributes()->function) ) {
						$filterProcessor['function'] = (string) $xmlDataProvider->filterProcessor->attributes()->function;
						if( !strlen($filterProcessor['function']) ) {
							$filterProcessor['function'] = null;
						}
					}
					
					//determinando filters determinados no proprio renderable
					if( isset($xmlDataProvider->filterProcessor->filter) ) {
						foreach ( $xmlDataProvider->filterProcessor->filter as $xmlFilterBy ) {
							
							//verificando se temos alguma configuração extendida para esse filtro, se nao tivermos ignoro
							if( !isset($xmlFilterBy->attributes()->param) && !isset($xmlFilterBy->attributes()->defaultValue) && !isset($xmlFilterBy->attributes()->required) ) {
								continue;
							}
							//processando filter
							$tmp = array();
							$tmp['by'] = (string) $xmlFilterBy->attributes()->by;
							if( isset($xmlFilterBy->attributes()->param) ) {
								$tmp['param'] = (string) $xmlFilterBy->attributes()->param;
							}
							if( isset($xmlFilterBy->attributes()->defaultValue) ) {
								$tmp['defaultValue'] = (string) $xmlFilterBy->attributes()->defaultValue;
							}
							if( isset($xmlFilterBy->attributes()->required) ) {
								$tmp['required'] = \Sh\Library::getBooleanFromXmlNode($xmlFilterBy->attributes()->required);
							}
							
							//registrando filter
							$filterProcessor['filters'][$tmp['by']] = $tmp;
						}
					}
				}
				else {
					//buscando filterProcessor para ocorrência do dataProvider
					if( isset($xmlDataProvider->attributes()->filterProcessor) ) {
						$filterProcessor['function'] = (string) $xmlDataProvider->attributes()->filterProcessor;
						if( !strlen($filterProcessor['function']) ) {
							$filterProcessor['function'] = null;
						}
					}
				}
				
				//PROCESSANDO OS SORTS CUSTOMIZADOS POR DATAPROVIDER
				$sortCustom = array();
				if( isset($xmlDataProvider->sort) && isset($xmlDataProvider->sort->by) ) {
					
					foreach ( $xmlDataProvider->sort->by as $xmlBy ) {
						
						$tmpSort = array(
							'field' 		=> (string) $xmlBy->attributes()->field,
							'order' 		=> null,
							'relationPath' 	=> null
						);
						//verificando a ordenação
						if( isset($xmlBy->attributes()->order) ) {
							$tmpSort['order'] = (string) $xmlBy->attributes()->order;
						}
						//verificando o path
						if( isset($xmlBy->attributes()->relationPath) ) {
							$tmpSort['relationPath'] = (string) $xmlBy->attributes()->relationPath;
						}
						$sortCustom[] = $tmpSort;
					}
					
				}
				
				//PROCESSANDO MAX ROWS
				$maxRows = null;
				if( isset($xmlDataProvider->maxRows) ) {
					$maxRows = (integer) $xmlDataProvider->maxRows;
				}
				
				//CRIANDO DATAPROVIDER
				$dataProvider = new \Sh\RenderableDataProvider($idDataProvider, $filterProcessor, null, $sortCustom, $maxRows);
				
				//inserindo no renderable
				$renderable->pushDataProvider($dataProvider);
			}
		}
		
		//PROCESSANDO OS STYLES
		if( isset($xmlRenderable->styles) && isset($xmlRenderable->styles->style) ) {
			foreach ($xmlRenderable->styles->style as $xmlStyle) {
				//criando style
				$id = (string) $xmlStyle->attributes()->id;
				$path = (string) $xmlStyle->attributes()->path;
				$default = false;
				if( isset($xmlStyle->attributes()->default) ) {
					$default = \Sh\Library::getBooleanFromXmlNode($xmlStyle->attributes()->default);
				}
				$style = new \Sh\RenderableStyle($id, $path, $default);
				//inserindo no renderable
				$renderable->pushStyle($style);
			}
		}
		
		
		return $renderable;
	}
	
	
	/**
	 * Definimos o dataFormatter a partir do dataFormatter passado
	 * @param string $dataFormatter
	 * @return string
	 */
	static protected function getDataFormatterFromString ( $dataFormatter ) {
		
		switch( $dataFormatter ) {
			case 'inlineContentPrefix':
			case 'fieldContent':
			case 'fieldContentMultiple':
			case 'relatedContent':
			case 'relatedContentMultiple':
				$dataFormatter = $dataFormatter;
				break;
			default:
				$dataFormatter = 'fieldContent';
				break;
		}
		return $dataFormatter;
	}
	
	/**
	 * Definimos o operador final a partir o operator passado
	 * @param string $operator
	 * @return string
	 */
	static protected function getOperatorFromString ( $operator ) {
		
		switch( $operator ) {
			case 'equal':
			case 'like':
			case 'likeSplit':
// 			case 'likeSufix':
// 			case 'likePrefix':
			case 'greater':
			case 'greaterOrEqual':
			case 'less':
			case 'lessOrEqual':
			case 'different':
			case 'in':
			case 'notIn':
			case 'isNull':
			case 'isNotNull':
				$operator = $operator;
				break;
			default:
				$operator = 'equal';
				break;
		}
		return $operator;
	}
	
	/**
	 * Definimos se devemos aplicar alguma função de data para o filtro
	 * @param string $strDateFunction
	 * @return string
	 */
	static protected function getDateFunctionFromString ( $strDateFunction ) {
		
		$dateFunction = null;
	
		switch( strtolower($strDateFunction) ) {
			case 'date':
			case 'year':
			case 'month':
			case 'day':
			case 'hour':
			case 'minute':
			case 'second':
				$dateFunction = $strDateFunction;
				break;
		}
		return $dateFunction;
	}
	
	/**
	 * Método que recebe uma string e retorna a ordenação correta aplicada
	 * @param string $string
	 * @return string
	 */
	static public function getSortOrderFromString ($string) {
		$string = strtolower($string);
		//DETERMINANDO MODELO DE ORDENACAO
		switch ( $string ) {
			case 'asc': case 'desc': case 'random': break;
			default: $sort['order'] = 'asc';
		}
		
		return $string;
	}
	
}