<?php

namespace Sh;

/**
 * @author guilherme
 * Classe responsável pelos ActionHandlers do Sheer
 * 
 */
class ActionHandler {
	
	protected $module;
	protected $id;
	protected $action;
	/**
	 * @var \Sh\DataSource
	 */
	protected $dataSource;
	/**
	 * Controle de permissionamento
	 */
	protected $permissionDefault = null;
	protected $permissions = [];
	protected $greencard = false;
	
	
	public function __construct($id, \Sh\DataSource $dataSource, $action) {
		
		$this->module = $dataSource->getModuleId();
		$this->id = $id;
		$this->dataSource = $dataSource;
		$this->action = $action;
		
		//Definindo permissionamento padrao
		//TODO CRIAR VARIAVEL PARA CONTROLE
		$this->permissionDefault = \Sh\ContentActionManager::getDefaultPermission();
		
	}
	
	public function getId() 					{ return $this->id; }
	public function getModuleId()				{ return $this->module; }
	/**
	 * @return \Sh\DataSource
	 */
	public function getDataSource()				{ return $this->dataSource; }
	public function getActionId()				{ return $this->action; }
	
	/**
	 * Permite setar se o actionHandler deve invocar o greencard
	 * 
	 * @param boolean $greencard
	 */
	public function setGreenCard($greencard) {
		$this->greencard = (boolean) $greencard;
	}
	
	/**
	 * Recupera se o actionHandler deve setar o greencard para suas ações cascateadas
	 * @return boolean
	 */
	public function getGreenCard () {
		return $this->greencard;
	}
	
	
	/**
	 * Método para definir a permissão default para o actionHandler
	 * 
	 * @param string $perm [acceptAll|denyGuest|acceptAll]
	 */
	public function setPermissionDefault ($perm) {
		switch($perm) {
			case 'acceptAll':
			case 'denyGuest':
			case 'denyAll':
				$this->permissionDefault = $perm;
				break;
		}
	}
	
	/**
	 * Método para inserir uma permissão customizada
	 * 
	 * @param string $profile
	 * @param boolean $accept
	 * 
	 * FIXME Devo carregar já o profile defini-lo pelo seu id? Ou uso o alias mesmo e verifico com o logado?
	 */
	public function setPermission ($profile, $accept=true) {
		
		$this->permissions[$profile] = array(
			'profile' => $profile,
			'accept' => $accept
		);
		
	}
	
	/**
	 * Método que irá determinar se o usuário logado possui permissão para executar este dataProvider
	 * 
	 * @return boolean
	 */
	public function hasPermission() {
		
		$permission = false;
		
		//VERIFICAR GREENCARD
		if( !$permission && \Sh\ContentActionManager::hasGreenCard() ) {
			$permission = true;
		}
		
		//VERIFICANDO PROFILE LOGADO
		$currentProfile = \Sh\AuthenticationControl::getAuthenticatedUserInfo('profile');
		//verificando com as permissões customizadas
		//A pessoa deve possuir um perfil de acesso e ter permissoes customizadas
		if( !$permission && $currentProfile && $this->permissions && isset($this->permissions[$currentProfile['alias']]) && $this->permissions[$currentProfile['alias']] ) {
			$permission =  true;
		}
		
		//Não tendo permissão customizada irei olhar no padrão assumido
		//Aceitar todas
		if( !$permission && $this->permissionDefault == 'acceptAll' ) {
			$permission = true;
		}
		//Negar somente o guest
		else if ( !$permission && $this->permissionDefault == 'denyGuest' && !\Sh\AuthenticationControl::isGuestUser() ) {
			$permission = true;
		}
		
		//Logando negaçcão de permissão
		if( !$permission ) {
			$message = 'Permissão para execução negada. ah='.$this->getModuleId().'/'.$this->getId().', user='.\Sh\AuthenticationControl::getAuthenticatedUserInfo('id');
			\Sh\LoggerProvider::log('greencard', $message);
		}
		
		return $permission; 
		
		
	}
	
	
	/**
	 * Função para customizar o clone do objeto.
	 * Precisamos altera-lo para que o dataSource não seja referenciado igual em todas as instancias de ActionHandlers
	 */
	public function __clone() {
		$this->dataSource = clone $this->dataSource;
	}
	
	
}