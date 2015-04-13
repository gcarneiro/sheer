<?php

namespace Sh;

abstract class GenericContentProvider {
	
	/**
	 * @var \Sh\DataProvider
	 */
	protected $dataProvider;
	
	public function __construct( \Sh\DataProvider $dataProvider ) {
		
		$this->dataProvider = $dataProvider;
		
	}
	
	/**
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
	 * 					]
	 * @param $deprecatedConfigs
	 * 		Este está @deprecated desde 2014.09.15
	 * 
	 * @return array(
	 * 		'available' => integer Determina quantos resultados são possíveis de serem recuperados com esses dados de busca
	 * 		'total' => integer Determina o total de registos que serão retornados nesta busca
	 * 		'results' => array Tras todos os resultados recuperados
	 * )
	 */
	abstract public function getData ( $filters=array(), $configs=array() );
	
}