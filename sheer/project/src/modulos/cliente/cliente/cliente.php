<?php
namespace Sh\Modules\cliente;

use Sh\ContentActionManager;
class cliente{
	static public $tipo = array(
		1 => 'Pessoa Jurídica',
		2 => 'Pessoa Física'
	); 
}

/**
 * Função para alterar cliente, verificando se vai atualizar ou cadastrar um novo
 * @author bgstudiosAsus
 *
 */
class alterarCliente extends \Sh\GenericAction{
	
	public function doAction($data){
		$dataResponse = null;
		//Verifica se o id está setado e se ele possui um valor, se sim atualizo
		
		if(isset($data['id']) && $data['id']!= null){
			$response = \Sh\ContentActionManager::doAction('cliente/cliente_update', $data, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			$dataResponse = $response['data'];
		}
		// Se não possuo id, vou adicionar um novo registro
		else{
			// Verifico se algum cliente possui código, se tiver uso esse código +1 para o novo cliente, se não uso 200
			$maiorCodigo = \Sh\ContentProviderManager::loadContent('cliente/clientePorCodigo');
			$maiorCodigo = reset($maiorCodigo['results']);
			$data['codigo'] = \Sh\FieldString::formatInputDataToSheer(200);
			if(isset($maiorCodigo['codigo'])){
				$data['codigo'] = \Sh\FieldString::formatInputDataToSheer((int) $maiorCodigo['codigo'] + 1);
			}
			
			
			$response = \Sh\ContentActionManager::doAction('cliente/cliente_add', $data , $this->connection);
			\Sh\Library::actionResponseCheck($response);
			$dataResponse = $response['data'];
		}
		
		return array(
			'status' => true,
			'code' => null,
			'data' => $dataResponse,
		);
	}
}

class parseClientePadrao extends \Sh\GenericDataParser {
	
	public function parseData($data){
		
		if($data){
		
			foreach ($data as $idConteudo=>$conteudo){
				
				$conteudo['avatar'] = array();
				$conteudo['avatar']['30'] = './imagens/icon_cliente30.png';
				$conteudo['avatar']['90'] = './imagens/icon_cliente90.png';
						
				$data[$idConteudo] = $conteudo;
			}
			
		return $data;
		
		}
		
	}
}