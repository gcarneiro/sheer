<?php

namespace Sh;

/**
 * @author guilherme
 * 
 * Esta classe será responsável pelo mapeamento de todos os módulos do sistema. 
 * Esta que irá determinar se um módulo é válido e existente.
 *
 */
abstract class ModuleControl {
	
	/**
	 * Variável de controle de inicialização
	 */
	static private $initialized = false;
	
	/**
	 * Variável de controle de módulos disponíveis
	 * @var array
	 */
	static protected $modules = array();
	
	/**
	 * Método de inicialização da classe
	 * 
	 * @return boolean
	 */
	static public function init () {
		
		if( self::$initialized ) {
			return true;
		}
		
		self::processModuleConfiguration();
		self::$initialized = true;
	}
	
	static public function getAvailableModules () {
		return self::$modules;
	}
	
	/**
	 * Determina se o módulo existe no projeto
	 * 
	 * @param string $idModule
	 * @return boolean
	 */
	static public function isModule($idModule) {
		if( !isset(self::$modules[$idModule]) ) {
			return false;
		}
		return true;
	}
	
	/**
	 * Método para recuperar as configurações de um módulo específico
	 * 
	 * @param string $idModule
	 * @return array
	 */
	static public function getModuleConfig($idModule) {
		if( !self::isModule($idModule) ) {
			return null;
		}
		return self::$modules[$idModule];
	}
	
	/**
	 * Método para recuperar todos os módulos disponíveis no sistema e suas configurações primitivas
	 * 
	 * @return array
	 */
	static public function getModulesAvailable () {
		return self::$modules;
	}
	
	/**
	 * Método para carregar os módulos disponíveis para o projeto.
	 * Inclui também as classes principais de cada módulo
	 */
	static protected function processModuleConfiguration() {
	
		//VARIAVEIS PARA GUARDAR OS MÓDULOS TEMPORARIAMENTE
		$modules = array();
		$modulesSheer = self::getModuleConfigs('sheer');
		$modulesProject = self::getModuleConfigs('project');
		
		//COMPILANDO INFORMAÇÕES FINAIS
		$modules = self::mergeModules($modulesSheer, $modulesProject);
		//SALVANDO
		self::$modules = $modules;
	
	}
	
	/**
	 * Método para processar configurações dos módulos e determinar todas as suas informações
	 * Este método também irá incluir o arquivo php do módulo caso exista
	 * 
	 * @param string $context [ "sheer", "project" ]
	 * @return array (
	 * 		id		=> string
	 * 		path	=> string
	 * 		config 	=> array(info, datasources, dataProviders, actionHandlers, renderables)
	 * )
	 */
	static protected function getModuleConfigs ( $context ) {
		
		$modules = array();
		$setupPath = $modulesPath = null;
		$json = $config = null;
		
		//DETERMINANDO CONTEXTO
		{
			if( strtolower($context) == 'sheer' ) {
				$setupPath = SH_SETUP_PATH.'modules.json';
				$modulesPath = SH_MODULE_PATH.'/';
			}
			else if ( strtolower($context) == 'project' ) {
				$setupPath = SH_PROJECT_SETUP_PATH.'modules.json';
				$modulesPath = SH_PROJECT_MODULE_PATH.'/';
			}
			else {
				return false;
			}
		}
		
		
		//PROCESSANDO INFORMAÇÕES INICIAIS E CAPTURANDO TODoS OS MÓDULOS
		{
			if( is_file($setupPath) ) {
				$json = file_get_contents($setupPath);
				$config = json_decode($json, true);
			}
			
			if( $config ) {
				foreach ($config as $idModule=>$pathModule) {
					$modules[$idModule] = array(
						'id' => $idModule,
						'path' => array(str_replace('//', '/', $modulesPath.$pathModule.'/')),
						'context' => array('sheer'=>false, 'project'=>false)
					);
				}
			}
		}
		
		//não encontrando módulos, retorno vazio
		if (!$modules) { return false; }
		
		//POSSUO TODAS AS INFORMAÇÕES BÁSICAS DOS MÓDULOS DO CONTEXTO [id, path]
		//preciso agora carregar as configurações de cada módulo: 
		//		"info", "datasource", "dataProviders", "renderables", "actionHandlers"
		foreach ( $modules as $idModule => $mod ) {
			
			//controlador do módulo
			$data = array(
				'info' => null,
				'datasources' => null,
				'dataProviders' => null,
				'actionHandlers' => null,
				'renderables' => null,
				'jobs' => null
			);
			
			//definindo controladores padroes
			$info = $ds = $dp = $ah = $rd = $jobs = null;
			
			//carregando arquivos do módulo
			$xmlModule = self::getXmlFileContent($mod, 'module');
			//TODO PRECISO CONSIDERAR OS ARQUIVOS PRÓPRIOS DE CONFIGURAÇÃO DE CADA ELEMENTO
// 			$xmlDataSources = self::getXmlFileContent($mod, 'datasources');
// 			$xmlDataProviders = self::getXmlFileContent($mod, 'dataProviders');
// 			$xmlActionHandlers = self::getXmlFileContent($mod, 'actionHandlers');
// 			$xmlRenderables = self::getXmlFileContent($mod, 'renderables');
			
			//verificando configuração básica do módulo
			if( !$xmlModule ) {
				\Sh\LoggerProvider::log('warning', 'O módulo "'.$idModule.'" é inválido.');
				unset($modules[$idModule]);
				continue;
			}
			
			//INCLUINDO PHP PRINCIPAL DO MÓDULO
			$path = reset($mod['path']);
			$moduleFilePath = $path.$idModule.'.php';
			if( is_file($moduleFilePath) ) {
				include $moduleFilePath;
			}
			
			//CONFIGURAÇÕES PRINCIPAIS DO MÓDULO
			{
				
				//info
				$data['info'] = $xmlModule->info;
				//datasources
				if( isset($xmlModule->datasources) && isset($xmlModule->datasources->datasource) ) {
					foreach ($xmlModule->datasources->datasource as $tmp) {
						$id = (string) $tmp->attributes()->id;
						$data['datasources'][$id] = $tmp;
					}
				}
				//dataProviders
				if( isset($xmlModule->dataProviders) && isset($xmlModule->dataProviders->dataProvider) ) {
					foreach ($xmlModule->dataProviders->dataProvider as $tmp) {
						$id = (string) $tmp->attributes()->id;
						$data['dataProviders'][$id] = $tmp;
					}
				}
				//actionHandlers
				if( isset($xmlModule->actionHandlers) && isset($xmlModule->actionHandlers->actionHandler) ) {
					foreach ($xmlModule->actionHandlers->actionHandler as $tmp) {
						$id = (string) $tmp->attributes()->id;
						$data['actionHandlers'][$id] = $tmp;
					}
				}
				//renderables
				if( isset($xmlModule->renderables) && isset($xmlModule->renderables->renderable) ) {
					foreach ($xmlModule->renderables->renderable as $tmp) {
						$id = (string) $tmp->attributes()->id;
						$data['renderables'][$id] = $tmp;
					}
				}
				//jobs
				if( isset($xmlModule->jobs) && isset($xmlModule->jobs->job) ) {
					foreach ($xmlModule->jobs->job as $tmp) {
						$jobTmp = array(
							'id' => (string) $tmp->attributes()->id,
							'excludeFromCron' => false
						);
						if( isset($tmp->attributes()->excludeFromCron) ) {
							$jobTmp['excludeFromCron'] = \Sh\Library::getBooleanFromXmlNode($tmp->attributes()->excludeFromCron);
						}
						$data['jobs'][$jobTmp['id']] = $jobTmp;
					}
				}
			}
			
			//GUARDANDO CONFIGURACOES DO MÓDULO
			$mod['context'][$context] = true;
			$mod['config'] = $data;
			$modules[$idModule] = $mod;
			
		}
		
		
		return $modules;
		
	}
	
	/**
	 * @param array $module (id, path)
	 * @param string $xmlFile [module, info, datasources, dataProviders, actionHandlers, renderables]
	 * @return \SimpleXMLElement
	 */
	static protected function getXmlFileContent($module, $xmlFile) {
		
		//neste simplesmente dou reset pois só temos um path processado nesta etapa
		$modulePath = reset($module['path']);
		
		//definindo tipo de arquivo
		switch ($xmlFile) {
			case 'module':
			case 'info':
				$xmlFilePath = $modulePath.$module['id'].'.xml';
				break;
			case 'datasources':
				$xmlFilePath = $modulePath.$module['id'].'.datasources.xml';
				break;
			case 'dataProviders':
				$xmlFilePath = $modulePath.$module['id'].'.dataProviders.xml';
				break;
			case 'actionHandlers':
				$xmlFilePath = $modulePath.$module['id'].'.actionHandlers.xml';
				break;
			case 'renderables':
				$xmlFilePath = $modulePath.$module['id'].'.renderables.xml';
				break;
			default:
				return null;
				break;
		}
		
		//verificando existencia de arquivo
		if( !is_file($xmlFilePath) ) {
			return null;
		}
		//carregando arquivo xml principal
		$xml = simplexml_load_file($xmlFilePath);
		
		return $xml;
	}
	
	/**
	 * Método para unir as configurações do módulo no Sheer e no Projeto.
	 * As configurações do projeto tem mais relevancia do que as do Sheer, então as sobrescreverão
	 * 
	 * @param array $sheer
	 * @param array $project
	 * @return array
	 */
	static private function mergeModules ($sheer, $project) {
		
		if( !$project ) { return $sheer; }
		
		foreach ( $project as $idModule => $mp ) {
			
			if( !isset($sheer[$idModule]) ) {
				$sheer[$idModule] = array(
					'id' 		=> $mp['id'],
					'context' 	=> array('sheer'=>false, 'project'=>false),
					'path' 		=> array(),
					'config' 	=> array(
							'info' => null,
							'datasources' => array(),
							'dataProviders' => array(),
							'actionHandlers' => array(),
							'renderables' => array(),
							'jobs' => array()
					)
				);
			}
			else {
				//Setando o contexto do sheer, pois detectamos que ele já existe
				$sheer[$idModule]['context']['sheer'] = true;
			}
			
			//Setando o contexto como sendo o do projeto, pois só chegamos aqui por ser do projeto
			$sheer[$idModule]['context']['project'] = true;
			
			
			//processando paths
			$mpPath = reset($mp['path']);
			$sheer[$idModule]['path'][] = $mpPath;
			
			//processando info
			$sheer[$idModule]['config']['info'] = $mp['config']['info'];
			
			//processando datasources
			if( $mp['config']['datasources'] ) {
				foreach ( $mp['config']['datasources'] as $k=>$v ) {
					$sheer[$idModule]['config']['datasources'][$k] = $v;
				}
			}
			
			//processando dataProviders
			if( $mp['config']['dataProviders'] ) {
				foreach ( $mp['config']['dataProviders'] as $k=>$v ) {
					$sheer[$idModule]['config']['dataProviders'][$k] = $v;
				}
			}
			
			//processando actionHandlers
			if( $mp['config']['actionHandlers'] ) {
				foreach ( $mp['config']['actionHandlers'] as $k=>$v ) {
					$sheer[$idModule]['config']['actionHandlers'][$k] = $v;
				}
			}
			
			//processando renderables
			if( $mp['config']['renderables'] ) {
				foreach ( $mp['config']['renderables'] as $k=>$v ) {
					$sheer[$idModule]['config']['renderables'][$k] = $v;
				}
			}
			
			//processando jobs
			if( $mp['config']['jobs'] ) {
				foreach ( $mp['config']['jobs'] as $k=>$v ) {
					$sheer[$idModule]['config']['jobs'][$k] = $v;
				}
			}
		}
		
		return $sheer;
	}
	
	
	
	
	
	
	
	
}