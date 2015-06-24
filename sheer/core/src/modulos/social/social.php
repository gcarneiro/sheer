<?php

namespace Sh\Modules\social;

abstract class social {
	
	/**
	 * Método para capturar a imagem do facebook e salvar em um arquivo temporario
	 * 
	 * @param string $source
	 * @param string $target
	 * @return mixed
	 */
	static public function copyImageFromUrl ($source, $target) {
		
		$ch = curl_init($source);
		$fp = fopen($target, "wb");
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		$response = curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		
		return $response;
	}
	
}

/**
 * @author Guilherme
 * Método para o javascript buscar as informações da configuração do Facebook
 */
class facebookConfiguration extends \Sh\GenericContentProvider {
	
	public function getData ( $filters=array(), $configs=array() ) {
		
		$facebookApp = \Sh\ProjectConfig::getFacebookConfiguration();
		unset($facebookApp['appSecret']);
		return $facebookApp;
		
	}
	
}

/**
 * @author Guilherme
 * Método para o javascript buscar as informações da configuração do Facebook
 */
class googleConfiguration extends \Sh\GenericContentProvider {
	
	public function getData( $filters=array(), $configs=array() ) {
		$gmailApp = \Sh\ProjectConfig::getGoogleConfiguration();
		unset($gmailApp['appSecret']);
		return $gmailApp;
	}
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler para cadastrar/atualizar um usuário a partir do seu "fbId" e um "accessToken Válido"
 * Este método é capaz de determinar se o usuário já está cadastrado e atualizar apenas o seu AccessToken
 *
 */
class registrarUsuarioFacebook extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		try {
			
			//VERIFICANDO SE O FACEBOOK ESTÁ ATIVO
			if( !\Sh\ProjectConfig::getFacebookConfiguration()['enable'] ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Facebook API não está habilitada para o projeto'
				));
			}
			
			//CONTROLES GERAIS
			$userGraph = false;
			
			//CRIANDO E VALIDANDO A SESSAO DO ACCESS TOKEN
			$session = new \Facebook\FacebookSession($data['accessToken']);
			
			//PROCURO O USUÁRIO DO FB PARA SABER SE POSSUO SEU REGISTRO NO SHEER SOCIAL
			$user = \Sh\ContentProviderManager::loadContent('social/facebook_loadByFBID', array('fbId'=>$data['fbId']), null, $this->connection);
			//já temos o usuário cadastrado
			if( $user['total'] == 1 ) {
				//recupero o usuário
				$user = reset($user['results']);
			}
			//ainda não temos o usuário cadastrado
			else {
				
				//PRECISO BUSCAR AS INFORMAÇÕES DO USUÁRIO
				$fbRequest = new \Facebook\FacebookRequest($session, 'GET', '/me');
				$fbResponse = $fbRequest->execute();
				$userGraph = $fbResponse->getGraphObject()->asArray();
				//COMPARANDO USUÁRIOS 
				if( $userGraph['id'] != $data['fbId'] ) {
					throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Usuário especificado para login é inválido para o Token de Acesso'
					));
				}
				// Buscando a foto do facebook
				$fbRequest = new \Facebook\FacebookRequest ( $session, 'GET', '/me/picture', array('redirect' => false,'type' => 'large'));
				$fbResponse = $fbRequest->execute();
				$fbPicture = $fbResponse->getGraphObject()->asArray();
				if ( $fbPicture && isset ( $fbPicture['is_silhouette'] ) && ! $fbPicture['is_silhouette'] ) {
					
					// copiando a imagem para pasta temporaria
					$imageTempPath = tempnam( sys_get_temp_dir(), $userGraph['id'] );
					// Copiando imagem do fb para temporario
					$imgResponse = \Sh\Modules\social\social::copyImageFromUrl($fbPicture['url'], $imageTempPath);
// 					$imgResponse = copy($fbPicture['url'], $imageTempPath );
					// erro ao recuperar foto
					if (!$imgResponse) {
						\Sh\LoggerProvider::log( 'full', 'Erro ao obter foto do facebook' );
					} 				
					// foto recuperada com sucesso
					else {
						// criando array temporário para inserção na pessoa
						$fbPicture = array (
							'tmp_name' => $imageTempPath,
							'name' => $userGraph['name'],
							'error' => null,
							'type' => null
						);
					}
				}
				else {
					$fbPicture = null;
				}
				
				$updatedTime = new \DateTime($userGraph['updated_time']);
				
				//Preciso processar a data de nascimento do usuário
				$birthday = null;
				if( isset($userGraph['birthday']) && strlen($userGraph['birthday']) == 10 ) {
					$birthday = \DateTime::createFromFormat('m/d/Y', $userGraph['birthday'])->format('d/m/Y');
				}
				
				//TODO Preciso obrigar o email.
				if( !isset($userGraph['email']) || !\Sh\LibraryValidation::validateEmail($userGraph['email']) ) {
					throw new \Sh\SheerException('O facebook não nos enviou seu email. Para acessar via Facebook o email é obrigatório.');
				}
				//TODO Preciso obrigar a permissão de amigos
				
				//CADASTRO O USUÁRIO
				$user = array(
					'id' 				=> \Sh\Library::getUniqueId(),
					'fbId'				=> $userGraph['id'],
					'sheerId'			=> null,
					'nome'				=> $userGraph['name'],
					'email'				=> $userGraph['email'],
					'username'			=> $userGraph['id'],
					'nascimento'		=> $birthday,
					'sexo'				=> \Sh\Facebook::getSexoFromGender($userGraph['gender']),
					'foto'				=> $fbPicture,
					'link'				=> $userGraph['link'],
					'fbUpdated'			=> $updatedTime->format('d/m/Y')
				);
				
				$responseUser = \Sh\ContentActionManager::doPrimitiveAction('social/facebook', 'add', $user, $this->connection);
				\Sh\Library::actionResponseCheck($responseUser);
			}
			
			//BUSCO O ACCESSTOKEN REGISTRADO PARA DECIDIR SE GERO UM NOVO PELO TEMPO DE EXPIRAÇÃO DO MESMO
			//sempre ao renover 
			//para isso recarrego o usuário pelo seu id
			$user 				= \Sh\ContentProviderManager::loadContentById('social/facebook', $user['id'], $this->connection);
			$userAccessToken 	= null;
			$renovarAccessToken = true;
			if( $user['accessToken'] ) {
				$userAccessToken = $user['accessToken'];
				
				//verifico se a expiração do accessToken é depois de 24 após hoje
				$diff = $userAccessToken['expiresAt']['dateTime']->diff(new \DateTime());
				if( ($diff->d > 1) || ($diff->m > 0) || ($diff->y > 0) ) {
					$renovarAccessToken = false;
				}
			}
			
			//renovando accessToken caso necessário
			if( $renovarAccessToken ) {
				//buscando o accessToken de longa vida
				$sessionTmp = $session->getLongLivedSession();
				$session = &$sessionTmp;
				
				//CADASTRO O ACCESSTOKEN
				$fbAccessToken = $session->getAccessToken();
				$accessToken = [
					'id' 				=> \Sh\Library::getUniqueId(),
					'fbId'				=> $user['fbId'],
					'accessToken'		=> $fbAccessToken->__toString(),
					'machineId'			=> $fbAccessToken->getMachineId(),
					'expiresAt'			=> $fbAccessToken->getExpiresAt()->format('d/m/Y H:i:s'),
					'createdAt'			=> date('d/m/Y H:i:s')
				];
				$responseAccessToken = \Sh\ContentActionManager::doAction('social/facebookAccessToken_add', $accessToken, $this->connection);
				\Sh\Library::actionResponseCheck($responseAccessToken);
				
				//alocando o accessToken nas informações do usuário
				$user['accessToken'] = $accessToken;
				$user['accessToken']['expiresAt'] = \Sh\FieldDateTime::formatInputDataToSheer($accessToken['expiresAt']);
				$user['accessToken']['createdAt'] = \Sh\FieldDateTime::formatInputDataToSheer($accessToken['createdAt']);
				
				//tendo renovado o accessToken irei regravar o rawData
				//defino a user folder
				$userFolder = SH_PROJECT_DATA_PATH.'facebook/'.$user['fbId'].'/';
				$userFolderExists = false;
				$userFileInfo = $userFolder.'info.json';
				if( !is_dir($userFolder) ) {
					$userFolderExists = mkdir($userFolder);
				}
				else {
					$userFolderExists = true;
				}
				//se temos a pasta do usuário criamos o arquivo raw
				if( $userFolderExists ) {
					//verifico se tenho o objeto raw do facebook
					if( !$userGraph ) {
						//PRECISO BUSCAR AS INFORMAÇÕES DO USUÁRIO
						$fbRequest = new \Facebook\FacebookRequest($session, 'GET', '/me');
						$fbResponse = $fbRequest->execute();
						$userGraph = $fbResponse->getGraphObject()->asArray();
					}
					
					$rawDataResponse = file_put_contents($userFileInfo, json_encode($userGraph));
					if( $rawDataResponse === false ) {
						//FIXME O QUE DEVO FAZER AO FALHAR O SALVAMENTO DO ARQUIVO?
					}
				}
			}
			
			//retorno
			return [
				'status' => true,
				'code' => null,
				'message' => null,
				'data' => $user
			];
			
			
		}
		catch (\Sh\SheerException $ex) {
			throw $ex;
		}
		catch (\Facebook\FacebookRequestException $ex) {
			throw new \Sh\ActionException(array(
				'code' => $ex->getCode(),
				'message' => $ex->getMessage()
			));
		} 
		catch (\Exception $ex) {
			throw new \Sh\ActionException(array(
				'code' => $ex->getCode(),
				'message' => $ex->getMessage()
			));
		}
		
	}
	
	
}

/**
 * @author Guilherme
 * 
 * Action Handler para sincronizar o SheerUser com o FacebookUser
 *
 */
class fbVincularUsuarios extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//DETERMINANDO SE DEVO BUSCAR OS USUÁRIOS POR EMAIL OU ID
		//EMAIL
		if( isset($data['sheerId']) && isset($data['fbId']) ) {
			
			//BUSCANDO USUÁRIO SHEER
			$user = \Sh\ContentProviderManager::loadContentById('user/user', $data['sheerId'], $this->connection);
			if( !$user ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Usuário Sheer não foi encontrado'
				));
			}
			
			//BUSCANDO USUÁRIO DO FACEBOOK
			//busco pelo id registrado pelo Sheer
			$facebookUser = \Sh\ContentProviderManager::loadContentById('social/facebook', $data['fbId'], $this->connection);
			if( !$facebookUser ) {
				//não encontrado o registrado pelo sheer vou buscar pelo id do facebook
				$facebookUser = \Sh\ContentProviderManager::loadContent('social/facebook_loadByFBID', $data, null, $this->connection);
				if( $facebookUser['total'] != 1 ) {
					throw new \Sh\ActionException(array(
							'code' => null,
							'message' => 'Usuário Facebook não foi encontrado'
					));
				}
				$facebookUser = reset($facebookUser['results']);
			}
			
			//VERIFICANDO IGUALDADE DE EMAILS
			if( $user['email'] != $facebookUser['email'] ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Não é possível vincular usuários com emails diferentes'
				));
			}
			
		}
		//EMAIL
		else if ( isset($data['email']) ) {
			
			//BUSCANDO USUÁRIO SHEER
			$user = \Sh\ContentProviderManager::loadContent('user/user_lista', ['email'=>$data['email']], null, $this->connection);
			if( $user['total'] != 1 ) {
				throw new \Sh\ActionException(array(
						'code' => null,
						'message' => 'Usuário Sheer não foi encontrado'
				));
			}
			$user = reset($user['results']);
			
			//BUSCANDO USUÁRIO DO FACEBOOK
			$facebookUser = \Sh\ContentProviderManager::loadContent('social/facebook_loadByEmail', $data, null, $this->connection);
			if( $facebookUser['total'] != 1 ) {
				throw new \Sh\ActionException(array(
					'code' => null,
					'message' => 'Usuário Facebook não foi encontrado'
				));
			}
			$facebookUser = reset($facebookUser['results']);
			
		}
		//NENHUM DOS DOIS
		else {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Dados para sincronia de usuários são inválidos'
			));
		}
		
		//SALVANDO VINCULO
		$updateFacebook = [
			'id'		=> $facebookUser['id'],
			'sheerId'	=> $user['id']
		];
		$response = \Sh\ContentActionManager::doAction('social/facebook_update', $updateFacebook, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return $response;
		
	}
	
}