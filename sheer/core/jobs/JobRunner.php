<?php

namespace Sh;

/**
 * @author Guilherme
 *
 * Classe responsável por executar todos os Jobs do Projeto
 */
abstract class JobRunner {
	
	static protected $tempoExecucao = null;
	
	/**
	 * Método para executar todos os jobs registrados no sistema
	 * Na execução iremos respeitar o seu intervalo
	 * 
	 * @param string $interval
	 * 		m 		=> todo minuto
	 * 		m1 		=> todo minuto
	 * 		m5 		=> a cada 5 minutos
	 * 		m10 	=> a cada 10 minutos
	 * 		m30 	=> a cada 30 minutos
	 * 		h		=> toda hora
	 * 		h1		=> toda hora
	 * 		h6		=> a cada 6 horas
	 * 		h12		=> a cada 12 horas
	 * 		d		=> todo dia
	 * 		w1		=> toda segunda
	 * 		w2		=> toda terca
	 * 		w3		=> toda quarta
	 * 		w4		=> toda quinta
	 * 		w5		=> toda sexta
	 * 		w6		=> todo sabado
	 * 		w7		=> todo domingo
	 * 
	 */
	static public function execAllJobs ( $interval=null ) {
		
		$forceAll = false;
		
		//Para executar os jobs eu assumo o greenCard
		\Sh\ContentActionManager::invokeGreenCard();
		//Calculando qual o tempo de execução atual
		self::$tempoExecucao = new \DateTime();
		
		//PRECISO RODAR TODoS OS MODULOS PARA RODAR OS SEUS JOBS
		//Carregando os módulos disponíveis
		$modulosDisponiveis = \Sh\ModuleControl::getModulesAvailable();
		//Operando por cada módulo vendo se ele possui um jobs
		foreach ( $modulosDisponiveis as $idModule=>$module ){
			//Carregando os jobs
			$jobs = \Sh\ModuleFactory::getModuleJobs($idModule);
			//caso possuam jobs para o módulo, irei roda-los
			if( $jobs ) {
				foreach ($jobs as $job) {
					try {
						//FIXME Irei remover essa opção de excludeFromCron
						//Verifico se esse job é para ser excluido do processo geral
						if( $job['excludeFromCron'] && !$forceAll ) {
							continue;
						}
						
						//Verificando a periodicidade contra a periodicidade pretendida
// 						if( $intervalo != $job['interval'] ) {
// 							continue;
// 						}
						
						//executando o job
						self::runJob($idModule, $job);
						
						//Logando a execução correta do JOB
						\Sh\LoggerProvider::log('cron', 'JOB SUCCESS: "'.$idModule.'/'.$job['id'].'"');
					}
					catch (\Sh\SheerException $e) {
						//LOG para descrição do job
						\Sh\LoggerProvider::log('cron', 'JOB FAIL: "'.$idModule.'/'.$job['id'].'": message: '.$e->getErrorMessage());
					}
					
				}
			}
		}
		
		\Sh\ContentActionManager::removeGreenCard();
		
	}
	
	/**
	 * Método para executar um job específico pelo seu alias
	 * @param string $jobId "idModulo/idJob"
	 */
	static public function execJob ( $jobId ) {
		
		self::$tempoExecucao = new \DateTime();
		
		//quebrando o identificador
		list($idModule, $idJob) = explode('/', $jobId);
		
		//Verificando a existencia do Job
		$jobs = \Sh\ModuleFactory::getModuleJobs($idModule);
		if( !isset($jobs[$idJob]) ) {
			return false;
		}
		
		//executando job
		try {
			self::runJob($idModule, $jobs[$idJob]);
		}
		catch (\Sh\SheerException $e) {
			//TODO devo fazer algum processamento de log para descrever o erro
		}
	}
	
	/**
	 * Método para executar um job específico
	 * 
	 * @param unknown $idModule
	 * @param unknown $job
	 * @throws \Sh\SheerException
	 */
	static protected function runJob ($idModule, $job) {
		
		//gerando nome da classe do método
		$jobClass = '\\Sh\\Modules\\'.$idModule.'\\'.$job['id'];
		
		//verificando se ele extende de GenericJob
		if( !is_subclass_of($jobClass, '\Sh\GenericJob') ) {
			throw new \Sh\SheerException(array(
				'message' => 'Classe de Job "'.$jobClass.'" não é dependente de \Sh\GenericJob',
				'code' => 'SCA_XXXX'
			));
		}
		
		//CRIANDO O JOB
		$job = new $jobClass(self::$tempoExecucao);
		$job->run();
		
	}
	
	
}