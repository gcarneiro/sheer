<?php

namespace Sh;

abstract class tempoVidaControlador {
	
	/**
	 * Função para ativar/encerrar conteudo por data
	 * 
	 * @param array $idDataSourceAlias [
	 * 		modulo/dataProvider => array modulo/idDataSource[
	 * 
	 * 			dataInicioLabel => string nome do campo de data inicio do dataSource,
	 * 			dataFimLabel => string nome do campo de data fim do dataSource,
	 * 			ativoLabel => string nome do campo de ativo do dataSource,
	 * 			processador => string nome da função a ser chamada
	 * 		]
	 * 		
	 * ]
	 * @throws \Sh\SheerException
	 * @return boolean
	 */
	static function processar ($idDataSourceAlias, \PDO $connection=null) {
		
		foreach ( $idDataSourceAlias as $idModuloDataSource => $labels ) {
			
			try {
				
				list ( $idModule, $idDataSource ) = explode ( '/', $idModuloDataSource );
				
				// CARREGANDO MODULO
				$module = \Sh\ModuleFactory::getModuleFull ( $idModule );
				if ( !$module ) {
					throw new \Sh\SheerException ( array (
							'message' => 'Erro ao carregar modulo "' . $idModule . '"',
							'code' => 'SCP_XXXX' 
					) );
				}
				
				// VERIFICANDO A EXISTENCIA DO DATASOURCE
				$dataSource = $module->getDataSource ( $idDataSource );
				if ( !$dataSource ) {
					throw new \Sh\SheerException ( array (
							'message' => 'Erro ao carregar DataSource "' . $idDataSource . '" do modulo "' . $idModule . '"',
							'code' => 'SCP_XXXX' 
					) );
				}
				
				//determinando que o labels é um array
				if( !is_array($labels) ) {
					$labels = array();
				}
				
				//DETERMINANDO OS CAMPOS PADROES
				if( !isset($labels['dataInicioLabel']) ) {
					$labels ['dataInicioLabel'] = 'dataInicio';
				}
				if( !isset($labels['dataFimLabel']) ) {
					$labels ['dataFimLabel'] = 'dataFim';
				}
				if( !isset($labels['ativoLabel']) ) {
					$labels ['ativoLabel'] = 'ativo';
				}
				if( !isset($labels['processador']) ) {
					$labels ['processador'] = '\Sh\tempoVidaControlador::ativarEncerrarPorData';
				}
				
				// CAPTURANDO OS CAMPOS
				$campos = array ();
				$campos['dataInicio'] = $dataSource->getField ( $labels ['dataInicioLabel'] );
				$campos['dataFim'] = $dataSource->getField ( $labels ['dataFimLabel'] );
				$campos['ativo'] = $dataSource->getField ( $labels ['ativoLabel'] );
				
				foreach ( $campos as $idField => $field ) {
					if ( !$field ) {
						throw new \Sh\SheerException ( array (
							'message' => 'Erro ao carregar Field "' . $idField . '"',
							'code' => 'SCP_XXXX' 
						));
					}
				}
				
				//PRIMARY KEY DO DATASOURCE
				$pk = $dataSource->getPrimaryKey(false);
				
				// CRIANDO DATAPROVIDER
				$dpFiltroAtivo = new \Sh\DataProvider ( 'dpFiltroAtivo', $dataSource );
				$dpFiltroAtivo->setMaxRows ( 0 );
				$filtroAtivo = array (
						'id' => $pk->getId(),
						'relationPath' => '/',
						'relationIndex' => 0,
						'field' => $campos ['ativo']->getId (),
						'operator' => 'in',
						'dateFunction' => null,
						'defaultValue' => array ('1','3'),
						'parameter' => $pk->getId(),
						'required' => true 
				);
				$dpFiltroAtivo->pushFilter ( $filtroAtivo );
				
				
				// CRIANDO CONTENTPROVIDER
				$contentProvider = new \Sh\ContentProvider ( $dpFiltroAtivo );
				
				// BUSCANDO DADOS
				$data = $contentProvider->getData ();
				if ( !$data || $data['total'] == 0 ) {
					throw new \Sh\SheerException ( array (
							'message' => 'Nenhum dado encontrado',
							'code' => null 
					) );
				}
				
				//ITERANDO PELOS VALORES PARA MODIFICAR OS SEUS "ATIVOS"
				foreach ( $data ['results'] as $id => $values ) {
					
					try {
						//INICIANDO CONEXAO COM BANCO DE DADOS
						$connectionOwner = false;
						if( !$connection ) {
							$connectionOwner = true;
							$connection = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
						}
						
						// MONTANDO ARRAY PARA VERIFICACAO
						$verificar = array ();
						$verificar ['id'] = $values ['id'];
						$verificar ['ativo'] = $values ['ativo'];
						$verificar ['dataFim'] = $values [$campos ['dataFim']->getId ()] ['dateTime'];
						$verificar ['dataInicio'] = $values [$campos ['dataInicio']->getId ()] ['dateTime'];
							
						//CHAMO A FUNCAO PASSADA NO ARRAY INICIAL
						$atualizar = call_user_func($labels['processador'], $verificar, $connection);
						if ( $atualizar ) {
							$response = \Sh\ContentActionManager::doAction ( $idModuloDataSource . '_update', $atualizar, $connection );
							\Sh\Library::actionResponseCheck($response);
						}
						
						if($connectionOwner) {
							$connection->commit();
							$connection = null;
						}
						
					}
					catch ( \Sh\SheerException $e ) {
						if($connectionOwner) {
							$connection->rollBack();
							$connection = null;
						}
					}
				}
			}
			
			catch ( \Sh\SheerException $e ) {
			}
		}
	}
	
	/**
	 * Função para encerrar(dataFim) e ativar (dataInicio)
	 * @param array $data
	 * @return array
	 */
	static protected function ativarEncerrarPorData ($data, \PDO $connection){
		
		$hoje = new \DateTimeImmutable();
		
		//encerrar
		if( $data['dataFim'] != null && $hoje > $data['dataFim'] ){
			$atualizar = array();
			$atualizar['ativo'] = 2;
			$atualizar['id'] = $data['id'];
			return $atualizar;
		}
		//ativar
		if( $data['ativo'] == 3 && $hoje >= $data['dataInicio'] ){
			$atualizar = array();
			$atualizar['id'] = $data['id'];
			$atualizar['ativo'] = 1;
			return $atualizar;
		}
	}
}
