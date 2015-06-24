<?php

namespace Sh;

abstract class AuthenticationControl {
	
	use \Sh\EventDrivenBehavior;
	
	static protected $initialized = false;
	
	/**
	 * Controlador de erros
	 * @var array("code", "message")
	 */
	static public $errorInfo = array(
		'code' => null,
		'message' => null
	);
	
	static public function init() {
		
		if( self::$initialized ) { return true; }
		
		//VERIFICO SE EXISTE O CONTROLE DE AUTHENTICAÇÃO SETADO, SE NÃO EXISTIR DEVO CRIAR
		if( !isset($_SESSION['sh_auth']) || !isset($_SESSION['sh_auth']['authenticated']) ) {
			self::authenticateAsGuest();
		}
		
		self::$initialized = true;
		return true;
		
	}
	
	/**
	 * Método para authenticar usuários a partir do seu login e senha
	 * @param string $login
	 * @param string $password
	 * @return boolean
	 */
	static public function authenticateUser($login, $password, \PDO $conn=null) {
		
		try {
			
			//limpando controlador de erros
			self::clearErrorControl();
			
			//DETERMINO OS PASSWORDS POSSÍVEIS
			$passMD5 = md5($password);
			$passSha256 = \Sh\Library::encodeForPassword($password);
			
			//EFETUANDO A BUSCA UTILIZANDO SHA256
			$query = 'SELECT * FROM sh_user AS u WHERE (u.login="'.$login.'" OR u.email="'.$login.'") AND  (u.password="'.$passSha256.'")';
			$response = \Sh\DatabaseManager::runQuery($query, $conn);
			//CASO NÃO ENCONTRE EFETUO A BUSCA UTILIZANDO MD5
			if( !$response ) {
				//NESTE ELEMENTO DEVEMOS OBRIGAR TOD-OS OS USUÁRIO A ATUALIZAREM SEU PASSWORD
				$query = 'SELECT * FROM sh_user AS u WHERE (u.login="'.$login.'" OR u.email="'.$login.'") AND  (u.password="'.$passMD5.'")';
				$response = \Sh\DatabaseManager::runQuery($query, $conn);
			}
			
			//RECUPERANDO DADOS DO USUARIO
			if( !$response ) {
				throw new \Sh\SheerException(array(
					'message' => 'Autenticação inválida',
					'code' => 'SAC_XXXX'
				));
			}
			$user = reset($response);
			
			//TRAVO POR USUÁRIO HABILITADO
			if( $user['habilitado'] == '2' ) {
				throw new \Sh\SheerException(array(
					'message' => 'Cadastro não habilitado para login',
					'code' => 'SAC_XXXX'
				));
			}
			
			//Efetuando autenticação do usuário
			return self::authenticateUserById($user['id'], $conn);
		}
		catch (\Sh\SheerException $e) {
			self::$errorInfo['code'] = $e->getErrorCode();
			self::$errorInfo['message'] = $e->getMessage();
			return false;
		}
		
	}
	
	/**
	 * Método para authenticar um usuário pelo seu id sem precisar da sua senha para validação
	 * @param string $user Identificador do usuário
	 * 		//TODO DEVE ACEITAR TANTO O IDENTIFICADO QUANTO O OBJETO DO USER/USER
	 * @param \PDO $conn Conexao a ser utilizada com o bacno de dados
	 * @return boolean
	 */
	static public function authenticateUserById($user, \PDO $conn=null) {
		
		try {
			
			//limpando controlador de erros
			self::clearErrorControl();
			
			//Recuperando usuário
			$user = \Sh\ContentProviderManager::loadContentById('user/user', $user, $conn);
			if( !$user ) {
				throw new \Sh\SheerException(array(
					'message' => 'Usuário inválido para autenticação',
					'code' => 'SAC_XXXX'
				));
			}
			
			//TRAVO POR USUÁRIO HABILITADO
			if( $user['habilitado'] == '2' ) {
				throw new \Sh\SheerException(array(
					'message' => 'Cadastro não habilitado para login',
					'code' => 'SAC_XXXX'
				));
			}
			
			//PRECISO CARREGAR OS PROFILES DO USUARIO
			$userProfiles = \Sh\ContentProviderManager::loadContent('user/perfisUsuario', array('idUser'=>$user['id']), array('conn'=>$conn));
			if( !$userProfiles || $userProfiles['available'] == 0 ) {
				throw new \Sh\SheerException(array(
					'message' => 'Usuário não possui perfis ativos',
					'code' => 'SAC_XXXX'
				));
			}
			//DETERMINANDO PROFILE DEFAULT
			$profiles = array();
			foreach ( $userProfiles['results'] as $userProfile ) {
				$profiles[$userProfile['idProfile']] = $userProfile['profile'];
			}
			
			//VERIFICANDO SE O USUÁRIO TEM O DEFAULT PROFILE A QUAL ESTÁ MARCADO
			if( !$user['defaultProfile'] || !isset($profiles[$user['defaultProfile']]) ) {
				throw new \Sh\SheerException(array(
					'message' => 'Usuário não possui perfil padrão de acesso. Contatar um administrador.',
					'code' => 'SAC_XXXX'
				));
			}
			$profile = $profiles[$user['defaultProfile']];
			
			$user['profile'] 	= $profile;
			$user['renderPath'] = $profile['renderPath'];
			
			self::trigger('login', null, $user);
			
			self::setAuthControlFromUserArray($user);
			return true;
		}
		catch (\Sh\SheerException $e) {
			self::$errorInfo['code'] = $e->getErrorCode();
			self::$errorInfo['message'] = $e->getMessage();
			return false;
		}
		
	}
	
	/**
	 * Método para trocar o profile desejado do usuário
	 * @param string $idProfile
	 * @return boolean
	 */
	static public function changeUserProfile ($idProfile) {

		//limpando controlador de erros
		self::clearErrorControl();
		
		try {
			//capturando informações do usuário
			$user = self::getAuthenticatedUserInfo();
			
			//PRECISO CARREGAR OS PROFILES DO USUARIO
			$userProfiles = \Sh\ContentProviderManager::loadContent('user/perfisUsuario', array('idUser'=>$user['id']));
			if( !$userProfiles || $userProfiles['available'] == 0 ) {
				throw new \Sh\SheerException(array(
						'message' => 'Usuário não possui perfis ativos',
						'code' => 'SAC_XXXX'
				));
			}
			
			//DETERMINANDO PROFILE ESCOLHIDO
			$profile = null;
			foreach ( $userProfiles['results'] as $userProfile ) {
				if( $userProfile['idProfile'] == $idProfile ) {
					$profile = $userProfile['profile'];
					break;
				}
			}
			//VERIFICANDO EXISTENCIA DE PROFILE DESEJADO
			if( !$profile ) {
				throw new \Sh\SheerException(array(
						'message' => 'Usuário não possui perfil desejado',
						'code' => 'SAC_XXXX'
				));
			}
			
			//EFETUANDO A TROCA DE CONTEXTO DO PROFILE
			$_SESSION['sh_auth']['profile'] 		= $profile;
			$_SESSION['sh_auth']['renderPath'] 		= $profile['renderPath'];
			
			return true;
			
		}
		catch (\Sh\SheerException $e) {
			self::$errorInfo['code'] = $e->getErrorCode();
			self::$errorInfo['message'] = $e->getMessage();
			return false;
		}
		
		
		
	}
	
	/**
	 * Método para recuperar infomações do usuário autenticado no momento
	 * Caso o parametro $info não seja enviado iremos devolver todo o mapeamento do usuário na SESSION
	 * Com a passagem do parametro conseguimos recuperar alguma informação específica
	 * 		"authenticated", "id", "profile", "nome", "email", "login", "habilitado", "multiSecao", "renderPath"
	 * @param string $info
	 * @return unknown
	 */
	static public function getAuthenticatedUserInfo ($info=null) {
		
		//verificando se temos a sessao habilitada
		if( isset($_SESSION) ) {
			$userInfo = $_SESSION['sh_auth'];
		}
		else {
			$userInfo = null;
		}
		
		if( $info ) {
			switch ($info) {
				case 'authenticated':
				case 'id':
				case 'nome':
				case 'email':
				case 'login':
				case 'habilitado':
				case 'multiSecao':
				case 'renderPath':
				case 'profile':
					if( isset($userInfo[$info]) ) {
						$userInfo = $userInfo[$info];
					}
					else {
						$userInfo = null;
					}
					break;
			}
		}
		return $userInfo;
	}
	
	/**
	 * Retorna informando se o usuário atual é usuário guest
	 * 
	 * @return boolean
	 */
	static public function isGuestUser() {
		return !(boolean) self::getAuthenticatedUserInfo('authenticated');
	}
	
	/**
	 * Método para efetuar logout de usuário
	 * @return boolean
	 */
	static public function logoutUser() {
		return self::authenticateAsGuest();
	}
	
	/**
	 * Método para Autenticar o usuário com guest
	 * @return boolean
	 */
	static protected function authenticateAsGuest () {
		
		//INFORMAÇÕES PADRÃO DE AUTHENTICACAO
		$guestUser = array(
			'id' => null,
			'nome' => 'Visitante',
			'email' => null,
			'login' => null,
			'password' => null,
			'habilitado' => 1,
			'multiSecao' => 1,
			//INFORMACOES DE PROFILE
			'profile' 	=> null,
			'renderPath' => 'visitante/'
		);
		
		//PRECISO BUSCAR O PROFILE PARA USUÁRIOS GUEST
		$profileGuest = \Sh\ContentProviderManager::loadContent('user/profileUsuariosGuest');
		if( $profileGuest['total'] > 0 ) {
			$profileGuest = reset($profileGuest['results']);
			
			$guestUser['profile'] = $profileGuest;
			$guestUser['renderPath'] = $profileGuest['renderPath'];
		}

		self::setAuthControlFromUserArray($guestUser, false);
		return true;
	}
	
	static protected final function verifyChangePasswordLogin ($shAuthUser) {
		
		//CARREGANDO O USUÁRIO DE SISTEMA
		$user = \Sh\ContentProviderManager::loadContentById('user/user', $shAuthUser['id']);
		
		//caso não precise
		if( $user['changePassNextLogin'] != 1 ) {
			return $shAuthUser;
		}
		
		$idProfileTrocaSenha = 'F291FE30-9171-4703-AD5F-6872BA7B65B7';
		
		//PRECISO ATRIBUIR O PERFIL DE TROCA DE SENHA "sheer.loginChangePassword" AO USUÁRIO
		//buscando os profiles do usuario
		$userProfiles = \Sh\ContentProviderManager::loadContent('user/perfisUsuario', array('idUser'=>$user['id']));
		if( !$userProfiles || $userProfiles['available'] == 0 ) {
			throw new \Sh\SheerException(array(
					'message' => 'Usuário não possui perfis ativos',
					'code' => 'SAC_XXXX'
			));
		}
		
		//verificando se o profile de trocaSenha existe nos perfis do usuario
		$profileTrocaSenha = null;
		foreach ($userProfiles['results'] as $userProfileUser) {
			if( $userProfileUser['idProfile'] == $idProfileTrocaSenha ) {
				$profileTrocaSenha = $userProfileUser['profile'];
				break;
			}
		}
		//se nao existir preciso criar a ligacao
		if( !$profileTrocaSenha ) {
			$response = \Sh\ContentActionManager::doAction('user/userProfileUser_add', array(
				'idUser' => $user['id'],
				'idProfile' => $idProfileTrocaSenha
			));
			\Sh\Library::actionResponseCheck($response);
			
			//carregando o profile para usar no objeto de autenticacao
			$profileTrocaSenha = \Sh\ContentProviderManager::loadContentById('user/userProfile', $idProfileTrocaSenha);
		}
		
	
		//SETAR O PERFIL AO USUARIO
		$shAuthUser['profile'] = $profileTrocaSenha;
		$shAuthUser['renderPath'] = $profileTrocaSenha['renderPath'];
		
		//RETORNO O OBJETO DE AUTENTICACAO
		return $shAuthUser;
		
	}
	
	/**
	 * Método interno para preencher os dados de autenticação do usuário autenticado
	 * @param array $user
	 * @param boolean $authenticated
	 */
	static protected final function setAuthControlFromUserArray($user, $authenticated=true) {
		
		//criando configuracoes de controle de usuario
		$shAuthUser = array(
			//INFORMACOES DE USUARIO
			'authenticated' 	=> $authenticated,
			'id' 				=> $user['id'],
			'nome' 				=> $user['nome'],
			'email' 			=> $user['email'],
			'login' 			=> $user['login'],
			'habilitado' 		=> $user['habilitado'],
			'multiSecao' 		=> $user['multiSecao'],
			//INFORMACOES DE PROFILE
			'profile' 			=> $user['profile'],
			'renderPath'		=> $user['profile']['renderPath']
		);
		
		//CASO EU ESTEJA REALIZANDO UMA AUTENTICAÇÃO DEVO VERIFICAR SE O USUÁRIO DEVE EFETUAR TROCA DE SENHA
		if( $authenticated ) {
			$shAuthUser = self::verifyChangePasswordLogin($shAuthUser);
		}
		
		$_SESSION['sh_auth'] = $shAuthUser;
	}
	
	/**
	 * Método para limpar o controlador de erros do classe
	 */
	static private function clearErrorControl () {
		self::$errorInfo = array(
			'code' => null,
			'message' => null
		);
	}
	
}