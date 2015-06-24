<?php

namespace Sh\Modules\variavel;

class variavel {
	
	static public $tipoVariavel = array(
			
			1 => 'String',
			2 => 'Integer',
			3 => 'Float',
			4 => 'Date',
			5 => 'Time'
	);
	
	/**
	 * 
	 * @param string $alias
	 * @return NULL|array(variavel)
	 */
	static public function getVariavelByAlias ($alias) {
		
		$variavel = \Sh\DatabaseManager::runQuery('SELECT * FROM sh_variavel WHERE nomeAcesso="'.$alias.'";');
		if( !is_array($variavel) || $variavel && count($variavel) != 1 ) {
			return null;
		}
		
		return reset($variavel);
		
	}
}

class adicionarVariavel extends \Sh\GenericAction {

	protected function doAction($data){

		$data['nomeAcesso'] = str_replace(' ', '', $data['nomeAcesso']);
		$dados = \Sh\ContentProviderManager::loadContent('variavel/listaFiltro', array('nomeAcesso'=>$data['nomeAcesso']));

		if( $dados['total'] > 0 ){
			throw new \Sh\SheerException(array('message'=>'Nome de Acesso já cadastrado','code'=>null));
			exit;
		}

		$response = \Sh\ContentActionManager::doAction('variavel/variavel_add', $data,$this->connection);
		\Sh\Library::actionResponseCheck($response);

		return array(
				'status' => true,
				'code' => null,
				'data' => $data
		);
	}

}


/**
 * @author Guilherme
 * 
 * ActionHandler customizado para salvar lista de variaveis enviadas pelo renderable de gerenciarEmLista
 *
 */
class salvarListaGerenciavel extends \Sh\GenericAction {

	protected $ignorePkValidation = true;

	protected function doAction($data) {
		$dataResponse = array();

		foreach ($data['variavel'] as $id=>$valor){
			$variavel = array();
			$variavel['id'] = $id;
			$variavel['valor'] = $valor;

			$response = \Sh\ContentActionManager::doAction('variavel/variavel_update', $variavel, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			$dataResponse[] = $response['data'];
		}

		return array(
			'status' => true,
			'code' => null,
			'data' => $dataResponse
		);
	}
}