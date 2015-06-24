<?php

namespace Sh;

abstract class ContentActionManager {
	
	static private $greencard = false;
	static protected $defaultPermission = false;
	
	/**
	 * Método para recuperar a permissão default do sistema
	 * 
	 * @return boolean
	 */
	static public function getDefaultPermission () {
		
		if( !self::$defaultPermission ) {
			$variavel = \Sh\Modules\variavel\variavel::getVariavelByAlias('sheer.ah.permissaoPadrao');
			self::$defaultPermission = $variavel['valor'];
		}
		return self::$defaultPermission;
	}
	
	/**
	 * Método para executar uma ação genérica do sistema.
	 * @param string $actionHandlerAlias idModule/idActionHandler
	 * @param array $data
	 * @param \PDO $conn Deverá ser NULL se não tiver conexão decidida
	 * @param boolean $greencard Assumi o permissionamento liberado para esta ação
	 * @throws \Sh\SheerException
	 * @return array(
	 * 		status : boolean indicando se a ação foi bem sucedida
	 * 		code : Código de resposta
	 * 		message : Mensagem de resposta
	 * 		data : Dados gerais da a resposta
	 * )
	 *		
	 */
	static public function doAction ($actionHandlerAlias, $data, \PDO $conn=null, $greencard=false) {
		
		//Invocando greencard
		if( $greencard ) {
			self::invokeGreenCard();
		}
		
		$action = self::getActionHandlerFromAlias($actionHandlerAlias);
		$response = $action->exec($conn, $data);
		
		//removendo greencard
		if( $greencard ) {
			self::removeGreenCard();
		}
		
		return $response;
		
	}
	
	/**
	 * @param string $dataSourceAlias idModule/idDataSource
	 * @param string $actionType tipo de ação a ser executada [add, update, delete]
	 * @param array $data
	 * @param \PDO $conn Deverá ser NULL se não tiver conexão decidida
	 * @throws \Sh\SheerException
	 * @return array(
	 * 		status : boolean indicando se a ação foi bem sucedida
	 * 		code : Código de resposta
	 * 		message : Mensagem de resposta
	 * 		data : Dados gerais da a resposta
	 * )
	 * 
	 */
	static public function doPrimitiveAction ( $dataSourceAlias, $actionType, $data, \PDO $conn=null ) {
		
		//DETERMINAR O MODULO E DATASOURCE
		list($idModule, $idDataSource) = explode('/', $dataSourceAlias);
		
		//determinando modulo
		$module = \Sh\ModuleFactory::getModuleFull($idModule);
		if( !$module ) {
			throw new \Sh\SheerException(array(
				'message' => 'Erro ao carregar módulo "'.$idModule.'"',
				'code' => 'SCA_XXXX'
			));
		}
		//determinando dataSource
		if( !isset($module->dataSources[$idDataSource]) || !$module->dataSources[$idDataSource] ) {
			throw new \Sh\SheerException(array(
					'message' => 'Erro ao carregar ActionHandler "'.$idActionHandler.'" do Módulo "'.$idModule.'"',
					'code' => 'SCA_XXXX'
			));
		}
		$dataSource = clone $module->dataSources[$idDataSource];
		
		//VALIDANDO TIPO DE AÇÃO
		if( !in_array($actionType, array('add', 'update', 'delete')) ) {
			throw new \Sh\SheerException(array(
				'message' => 'Tipo de ação primitiva é inválida',
				'code' => 'SCA_XXXX'
			));
		}
			
		//CRIANDO ACTIONHANDLER
		$actionHandler = new \Sh\ActionHandler('temporary', $dataSource, $actionType);
		
		//BUSCANDO CLASSE DE AÇÃO
		$actionClass = null;
		switch ($actionType) {
			case 'add':
				$actionClass = '\\Sh\\AddAction';
				break;
			case 'update':
				$actionClass = '\\Sh\\UpdateAction';
				break;
			case 'delete':
				$actionClass = '\\Sh\\DeleteAction';
				break;
		}
		
		//CRIANDO CLASSE DE AÇÃO
		$action = new $actionClass($actionHandler);
		
		//EXECUTANDO AÇÃO
		$response = $action->exec($conn, $data);
		return $response;
	}
	
	/**
	 * Método responsável por compilar e criar o actionHandler desejado
	 * 
	 * @param string $actionHandlerAlias idModule/idActionHandler
	 * @throws \Sh\SheerException
	 * @return \Sh\GenericAction
	 */
	static public function getActionHandlerFromAlias ($actionHandlerAlias) {
		
		list($idModule, $idActionHandler) = explode('/', $actionHandlerAlias);
			
		//CARREGANDO MODULO
		$module = \Sh\ModuleFactory::getModuleFull($idModule);
		if( !$module ) {
			throw new \Sh\SheerException(array(
					'message' => 'Erro ao carregar módulo "'.$idModule.'"',
					'code' => 'SCA_XXXX'
			));
		}
		//CARREGANDO ACTIONHANDLER
		$actionHandler = clone $module->actionHandlers[$idActionHandler];
		if( !$actionHandler ) {
			throw new \Sh\SheerException(array(
					'message' => 'Erro ao carregar ActionHandler "'.$idActionHandler.'" do Módulo "'.$idModule.'"',
					'code' => 'SCA_XXXX'
			));
		}
		
		//PRECISO DEFINIR E CARREGAR O ACTION CORRETO
		$actionClass = '';
		switch ($actionHandler->getActionId()) {
			case 'add':
				$actionClass = 'AddAction';
				$fullActionClass = '\\Sh\\'.$actionClass;
				break;
			case 'update':
				$actionClass = 'UpdateAction';
				$fullActionClass = '\\Sh\\'.$actionClass;
				break;
			case 'delete':
				$actionClass = 'DeleteAction';
				$fullActionClass = '\\Sh\\'.$actionClass;
				break;
			default:
				$actionClass = $actionHandler->getActionId();
				$fullActionClass = '\\Sh\\Modules\\'.$actionHandler->getModuleId().'\\'.$actionClass;
				break;
		}
		
		//verifico a dependencia da classe "GenericAction"
		if( !is_subclass_of($fullActionClass, '\Sh\GenericAction') ) {
			throw new \Sh\SheerException(array(
					'message' => 'Classe de ação "'.$fullActionClass.'" não é dependente de \Sh\GenericAction',
					'code' => 'SCA_XXXX'
			));
		}
		$action = new $fullActionClass($actionHandler);
		
		return $action;
	}
	
	/**
	 * @param string $idModule
	 * @param string $idDataSource
	 * @param string $idAction
	 * @throws \Sh\SheerException
	 * @return \Sh\GenericAction
	 */
	static public function getSheerActionHandlerFromModuleAndDataSource ($idModule, $idDataSource, $idAction) {
		
		//CARREGANDO MODULO
		$module = \Sh\ModuleFactory::getModuleFull($idModule);
		if( !$module ) {
			throw new \Sh\SheerException(array(
					'message' => 'Erro ao carregar módulo "'.$idModule.'"',
					'code' => 'SCA_XXXX'
			));
		}
		
		//CARREGANDO DATASOURCE
		$dataSource = $module->getDataSource($idDataSource);
		if( !$dataSource ) {
			throw new \Sh\SheerException(array(
				'message' => 'Erro ao carregar DataSource "'.$idDataSource.'"',
				'code' => 'SCA_XXXX'
			));
		}
		
		//PRECISO DEFINIR E CARREGAR O ACTION CORRETO
		$actionHandler = $action = null;
		switch ($idAction) {
			case 'add':
				$actionHandler = \Sh\ActionHandlerCompiler::getDefaultAddActionHandlerFromDataSource($dataSource, $module);
				$fullActionClass = '\\Sh\\AddAction';
				break;
			case 'update':
				$actionHandler = \Sh\ActionHandlerCompiler::getDefaultUpdateActionHandlerFromDataSource($dataSource, $module);
				$fullActionClass = '\\Sh\\UpdateAction';
				break;
			case 'delete':
				$actionHandler = \Sh\ActionHandlerCompiler::getDefaultDeleteActionHandlerFromDataSource($dataSource, $module);
				$fullActionClass = '\\Sh\\DeleteAction';
				break;
			default:
				throw new \Sh\SheerException(array(
					'message' => 'Action Handler "'.$idAction.'" do Sheer é inválido "'.$idModule.'"',
					'code' => 'SCA_XXXX'
				));
				break;
		}
		
		$action = new $fullActionClass($actionHandler);
		
		return $action;
		
	}
	
	/**
	 * Método para assumir o papel GreenCard
	 */
	static public function invokeGreenCard () {
		self::$greencard = true;
		\Sh\LoggerProvider::log('greenCard', 'GreenCard Invocado.');
	}
	
	/**
	 * Método para remover o papel GreenCard
	 */
	static public function removeGreenCard () {
		self::$greencard = false;
		\Sh\LoggerProvider::log('greenCard', 'GreenCard Revogado.');
	} 
	
	/**
	 * Retorna a situação atual do papel GreenCard
	 * @return boolean
	 */
	static public function hasGreenCard() {
		return self::$greencard;
	}
	
	
}