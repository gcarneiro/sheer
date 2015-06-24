<?php

namespace Sh\Modules\token;

class token {
	
	static public $status = array(
		1 => 'Ativo',
		2 => 'Utilizado',
		3 => 'Cancelado'
	);
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler responsável por gerar um novo Token
 * 
 */
class gerarToken extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//Gerando o token
		$tokenId = \Sh\Library::getUniqueId();
		$token = array(
			'id' => $tokenId,
			'status' => 1,
			'security' => (isset($data['security'])) ? $data['security'] : null
		);
		$response = \Sh\ContentActionManager::doPrimitiveAction('token/token', 'add', $token, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return $response;
		
	}
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler responsável por marcar a utilização de um token
 * 
 */
class utilizarToken extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//Carregando Token
		$token = \Sh\ContentProviderManager::loadContentById('token/token', $data['id']);
		if( !$token ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Token inválido para utilização'
			));
		}
		
		//VERIFICANDO SE EXISTE SECURITY SETADO DOS DOIS LADO
		if( isset($data['security']) && strlen($token['security']) ) {
			//verificando o valor
			if( $data['security'] != $token['security'] ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Informações de segurança são inválidas'
				));
			}
		}
		
		//Verificando se o token pode ser utilizado
		if( $token['status'] != 1 ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Este token não pode ser utilizado pois já foi utilizado ou foi cancelado.'
			));
		}
		
		//Atualizando o token
		$tokenAtualizar = array(
			'id' 			=> $token['id'],
			'status' 			=> 2,
			'finalizadoEm' 		=> date('d/m/Y H:i:s')
		);
		$responseToken = \Sh\ContentActionManager::doPrimitiveAction('token/token', 'update', $tokenAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($responseToken);
		
		return $responseToken;
	}
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler para travar com erros chamadas para sheer/token
 */
class retornarErro extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		return array(
			'status' => false,
			'code' => null,
			'message' => 'Modelo inválido para utilização de tokens'
		);
		
	}
}

/**
 * @author Guilherme
 * 
 * Job Para cancelar todos os tokens que tiveram mais de 24horas da sua geração
 *
 */
class cancelarTokens extends \Sh\GenericJob {
	
	public function run() {
		
		$connection = \Sh\DatabaseConnectionProvider::getDatabaseConnection();
		
		//Buscando os token com geração passada
		$tokensExpirados = \Sh\ContentProviderManager::loadContent('token/token_expirados');
		if( $tokensExpirados['total'] > 0 ) {
			foreach ( $tokensExpirados['results'] as $idToken=>&$token ) {
				$response = $connection->exec('UPDATE sh_token SET status=3, finalizadoEm=NOW() WHERE id="'.$idToken.'";');
			}
		}
		
		$connection->commit();
		
	}
	
}











