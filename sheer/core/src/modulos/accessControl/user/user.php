<?php

namespace Sh\Modules\user;

/**
 * @author Guilherme
 * 
 * ActionHandler customizado para adicionar usuários no sistema.
 * 		- Decide se login será igual ao email
 * 		- Verifica se pre-existencia de email ou login
 * 		- Criptografa a senha
 *
 */
class adicionarUsuario extends \Sh\AddAction {
	
	protected $processAddons = false;
	
	public function doAction($data) {
		
		//LOGIN
		//Verificando se possui login setado, se não tive utilizo o email
		if (!isset($data['login']) || strlen($data['login']) == 0  ) {
			$data['login'] = $data['email'];
		}
		else if( strlen($data['login']) < 4 ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Login de usuário deve ter pelo menos 4 caracteres'
			));
		}
		
		//buscando se temos usuário com este login
		$usuarios = \Sh\ContentProviderManager::loadContentCustomConfig('user/user_lista', array('login'=>array('required'=>true, 'value'=>$data['login'])), array('conn'=>$this->connection));
		if( $usuarios['total'] > 0 ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Login de usuário "'.$data['login'].'" já existe'
			));
		}
		
		//EMAIL
		$usuarios = \Sh\ContentProviderManager::loadContentCustomConfig('user/user_lista', array('email'=>array('required'=>true, 'value'=>$data['email'])), array('conn'=>$this->connection));
		if( $usuarios['total'] > 0 ) {
			throw new \Sh\ActionException(array(
					'code' => null,
					'message' => 'Email de usuário "'.$data['login'].'" já existe'
			));
		}
		
		//TRATANDO SENHA
		$data['password'] = \Sh\Library::encodeForPassword($data['password']);
		
		//CADASTRANDO O USUÁRIO
		$response = \Sh\ContentActionManager::doPrimitiveAction('user/user', 'add', $data, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return $response;
		
		
	}
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler customizado para atualizar usuários no sistema.
 *		- Decide se login será igual ao email
 * 		- Verifica se pre-existencia de email ou login
 * 		- Removo a senha. Esta não deve ser atualizada por este método
 */
class atualizarUsuario extends \Sh\UpdateAction {
	
	public function doAction($data) {
		
		//LOGIN E EMAIL
		if( isset($data['login']) && isset($data['email']) ) {
			
			
			//Verificando se possui login setado, se não tive utilizo o email
			if ( strlen($data['login']) == 0   ) {
				$data['login'] = $data['email'];
			}
			else if( strlen($data['login']) < 4 ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Login de usuário deve ter pelo menos 4 caracteres'
				));
			}
			
			//buscando se temos usuário com este login
			$usuarios = \Sh\ContentProviderManager::loadContentCustomConfig('user/user_lista', array('login'=>$data['login']));
			//se temos apenas 1, devemos verificar se é o mesmo usuário
			if( $usuarios['total'] > 0 && !isset($usuarios['results'][$data['id']]) ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Login de usuário "'.$data['login'].'" já existe'
				));
			}
			
			//EMAIL
			$usuarios = \Sh\ContentProviderManager::loadContentCustomConfig('user/user_lista', array('email'=>$data['email']));
			if( $usuarios['total'] > 0 && !isset($usuarios['results'][$data['id']])  ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Email de usuário "'.$data['login'].'" já existe'
				));
			}
			
		}
		//LOGIN SEM EMAIL
		else if ( isset($data['login']) ) {
			//LOGIN
			//Verificando se possui login setado, se não tive utilizo o email
			if( strlen($data['login']) < 4 ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Login de usuário deve ter pelo menos 4 caracteres'
				));
			}
				
			//buscando se temos usuário com este login
			$usuarios = \Sh\ContentProviderManager::loadContentCustomConfig('user/user_lista', array('login'=>$data['login']));
			//se temos apenas 1, devemos verificar se é o mesmo usuário
			if( $usuarios['total'] > 0 && !isset($usuarios['results'][$data['id']]) ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Login de usuário "'.$data['login'].'" já existe'
				));
			}
		}
		
		//REMOVENDO A SENHA
		$data['password'] = null;
		unset($data['password']);
		
		//CADASTRANDO O USUÁRIO
		$response = \Sh\ContentActionManager::doPrimitiveAction('user/user', 'update', $data, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return $response;
		
		
	}
	
}
/**
 * @author Guilherme
 * 
 * ActionHandler para trocar a senha do usuário sem confirmação da senha antiga
 */
class atualizarSenhaSemConfirmacao extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//IGUALDADE DE NOVAS SENHAS
		if( $data['password'] != $data['passwordConfirmar'] ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'As duas senhas não coincidem'
			));
		}
		
		//atualizacao senha
		$dataAtualizar = array(
			'id' => $data['id'],
			'password' => \Sh\Library::encodeForPassword($data['password'])
		);
		//verificando a necessidade de troca de senha no próximo login
		if( isset($data['changePassNextLogin']) ) {
			$dataAtualizar['changePassNextLogin'] = $data['changePassNextLogin'];
		}
		
		//CADASTRANDO O USUÁRIO
		$response = \Sh\ContentActionManager::doPrimitiveAction('user/user', 'update', $dataAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return $response;
		
		
	}
	
}

/**
 * @author Guilherme
 *
 * ActionHandler que irá atualizar os perfis de acesso de um usuário
 * Este deverá receber
 *
 * 	id => id do usuario
 *  profiles => os ids dos profiles que a pessoa deve possuir sendo a chave do array
 *
 */
class atualizarPerfisAcesso extends \Sh\GenericAction {

	public function doAction($data) {

		//DEVO VALIDAR USUARIO
		$user = \Sh\ContentProviderManager::loadContentById('user/user', $data['id'], $this->connection);
		if( !$user ) {
			throw new \Sh\ActionException(array(
					'code' => null,
					'message' => 'Usuário inválido para atualização de perfis'
			));
		}
		$profilePadraoExiste = false;
		$profilePadraoNovo = null;

		//REMOVENDO TODoS OS PERFIS DE ACESSO
		$query = 'DELETE FROM sh_userProfileUser WHERE idUser="'.$user['id'].'"';
		$result = $this->connection->exec($query);
		if( $result === false ) {
			throw new \Sh\ActionException(array(
					'code' => null,
					'message' => 'Erro ao remover perfis de acesso antigos'
			));
		}

		//ADICIONANDO OS NOVOS PERFIS DE ACESSO
		if( isset($data['profiles']) && $data['profiles'] ) {
			foreach ( $data['profiles'] as $idProfile=>$val ) {
				$userProfileUser = array(
						'idUser' => $user['id'],
						'idProfile' => $idProfile
				);
				$response = \Sh\ContentActionManager::doAction('user/userProfileUser_add', $userProfileUser, $this->connection);
				\Sh\Library::actionResponseCheck($response);

				if( !$profilePadraoNovo ) {
					$profilePadraoNovo = $idProfile;
				}

				//verificando profile padrao
				if( $idProfile == $user['defaultProfile'] ) {
					$profilePadraoExiste = true;
				}
			}
		}

		//SE O USUÁRIO NÃO POSSUIR MAIS O PROFILE PADRAO, PRECISO ATUALIZA-LO COLOCANDO UM NOVO
		if( !$profilePadraoExiste ) {
			$userUpdate = array(
					'id' => $user['id'],
					'defaultProfile' => $profilePadraoNovo
			);
			$response = \Sh\ContentActionManager::doAction('user/user_update', $userUpdate, $this->connection);
			\Sh\Library::actionResponseCheck($response);
		}

		//OCORRENDO TUDO CERTO DEVO RETORNAR
		return array(
			'status' => true,
			'code' => null,
			'data' => array(
					'id' => $user['id'],
					'profiles' => $data['profiles']
			)
		);

	}

}

/**
 * @author guilherme
 *
 * ActionHandler para trocar perfil de Usuário
 */
class alteraPerfilAcesso extends \Sh\GenericAction {

	public function doAction($data) {
		
		//determinando identificador do profile
		$idProfile = $data['id'];
		$response = \Sh\AuthenticationControl::changeUserProfile($data['id']);

		//verificando sucesso ao trocar perfil
		if( !$response ) {
			throw new \Sh\ActionException(\Sh\AuthenticationControl::$errorInfo);
		}

		//preparando resposta
		$response = array(
			'status' => true,
			'code' => null,
			'message' => null,
			'data' => null
		);

		return $response;
	}

}


/**
 * @author Guilherme
 * 
 * DataProvider para buscar as informações do usuário logado no momento
 *
 */
class getAuthenticatedUserInfo extends \Sh\GenericContentProvider {
	
	public function getData( $filters=array(), $configs=array() ) {
		
		$usuarioAutenticado = \Sh\AuthenticationControl::getAuthenticatedUserInfo();
		return $usuarioAutenticado;
		
	}
	
	
}


//-------------------------------------------------------		ANTIGOS

/**
 * @author guilherme
 *
 * ActionHandler para tratar o login do Usuário
 */
class userLoginAction extends \Sh\GenericAction {
	
	protected $ignorePkValidation = true;
	
	public function doAction($data) {
		
		$response = array(
			'status' => false,
			'code' => null,
			'message' => null,
			'data' => null
		);
		
		//FIXME ainda falta algum tratamento para determinar para qual página o usuário será enviado caso algo aconteca.
		//Exemplo: entrar numa página restrita e depois do login voltar
		$autenticacao = \Sh\AuthenticationControl::authenticateUser($data['login'], $data['password']);
		if( $autenticacao ) {
			$response['status'] = true;
			$response['data'] = \Sh\AuthenticationControl::getAuthenticatedUserInfo();
			
			//DANDO CERTO O LOGIN, PRECISO SALVAR NO LOG DE LOGIN AS INFORMAÇÕES DA AUTENTICAÇÃO
			$loggedIn = array(
				'idUser' => $response['data']['id'],
				'loggedAt' => date('d/m/Y H:i:s'),
				'userAgent' => $_SERVER['HTTP_USER_AGENT'],
				'userIp' => $_SERVER['REMOTE_ADDR'],
				'sessionId' => \Sh\SessionControl::getSessionId()
			);
			\Sh\ContentActionManager::doAction('user/userLoggedIn_add', $loggedIn, $this->connection);
		}
		else {
			$errorInfo = \Sh\AuthenticationControl::$errorInfo;
			
			$response['status'] = false;
			$response['code'] = $errorInfo['code'];
			$response['message'] = $errorInfo['message'];
		}
		
		return $response;
	}
	
}

/**
 * @author guilherme
 *
 * ActionHandler para deslogar usuário
 */
class userLogoutAction extends \Sh\GenericAction {

	protected $ignorePkValidation = true;

	public function doAction($data) {
		
		$response = array(
			'status' => false,
			'code' => null,
			'message' => null,
			'data' => null
		);

		$response['status'] = \Sh\AuthenticationControl::logoutUser();

		return $response;
	}

}

/**
 * @author Guilherme
 * 
 * ActionHandler para registrar a troca de senha de um usuário e quando for determinado a trocar senha em próximo login
 *
 */
class trocarSenhaRequisitadaLogin extends \Sh\GenericAction {
	
	public function doAction($data) {
		$responseTrocaSenha = array(
			'status' => true,
			'code' => null,
			'message' => null,
			'data' => null
		);
		
		//ZERANDO ESPACOS
		$data['password'] = trim($data['password']);
		$data['confirmPassword'] = trim($data['confirmPassword']);
		
		//VERIFICANDO IGUALDADE DE SENHA
		if( $data['password'] != $data['confirmPassword'] ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Senhas não coincidem'
			));
		}
		
		//VERIFICO QUE NÃO É A MESMA QUE A JÁ UTILIZADA
		$user = \Sh\ContentProviderManager::loadContentById('user/user', \Sh\AuthenticationControl::getAuthenticatedUserInfo('id'));
		$oldPass = $user['password'];
		$newPass = \Sh\Library::encodeForPassword($data['password']);

		if( $oldPass == $newPass ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'A nova senha deve ser diferente da anterior'
			)); 
		}
		
		//ATUALIZANDO USUARIO COM A NOVA SENHA
		$userUpdate = array(
			'id' => $user['id'],
			'password' => $newPass,
			'changePassNextLogin' => 2
		);
		$response = \Sh\ContentActionManager::doAction('user/user_update', $userUpdate, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//PRECISO REMOVER O PERFIL DE ACESSO TEMPORÁRIO DE TROCA DE SENHA
		//buscando os perfis de acesso do usuario
		$userProfiles = \Sh\ContentProviderManager::loadContent('user/perfisUsuario', array('idUser'=>$user['id']));
		if( !$userProfiles || $userProfiles['available'] == 0 ) {
			throw new \Sh\SheerException(array(
				'message' => 'Erro ao determinar os perfis de acesso do usuário',
				'code' => 'SAC_XXXX'
			));
		}
		
		//buscando o profile de troca de senha
		$idProfileTrocaSenha = 'F291FE30-9171-4703-AD5F-6872BA7B65B7';
		$profileTrocaSenha = null;
		foreach ($userProfiles['results'] as $userProfileUser) {
			if( $userProfileUser['idProfile'] == $idProfileTrocaSenha ) {
				$profileTrocaSenha = $userProfileUser;
				break;
			}
		}
		//verificando profile
		if( !$profileTrocaSenha ) {
			throw new \Sh\SheerException(array(
				'message' => 'Erro ao determinar profile de acesso temporário',
				'code' => 'SAC_XXXX'
			));
		}
		
		//removendo o profile
		$profileRemover = array('id'=>$profileTrocaSenha['id']);
		$response = \Sh\ContentActionManager::doAction('user/userProfileUser_delete', $profileRemover, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//PRECISO AGORA REAUTENTICAR O USUÁRIO COM SEU PERFIL DE
		$response = \Sh\AuthenticationControl::changeUserProfile($user['defaultProfile']);
		if( !$response ) {
			throw new \Sh\SheerException(array(
				'message' => 'Erro ao trocar profile de usuário',
				'code' => 'SAC_XXXX'
			));
		}

		return $responseTrocaSenha;
	}
	
}