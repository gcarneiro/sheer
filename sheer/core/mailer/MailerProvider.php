<?php

namespace Sh;

/**
 * @author Guilherme
 * 
 * Classe responsável por prover a habilidade de enviar emails
 *
 */
abstract class MailerProvider {
	
	/**
	 * Método para enviar email diretamente sem ter que se precupar com os mailers
	 * @param array $emailInfo [
	 * 		from 			=> array(name, email)
	 * 		replyTo 		=> array(name, email)
	 * 		address 		=> array(name, email) | array( array(name, email) )
	 * 		cc				=> array(name, email) | array( array(name, email) )
	 * 		bcc				=> array(name, email) | array( array(name, email) )
	 * 		subject			=> string
	 * 		body			=> string
	 * 		bodyPlain		=> string
	 * ]
	 * @param string $idMailer [opcional, irá capturar o default]
	 * 
	 * @return array [
	 * 		status
	 * 		code
	 * 		message
	 *  	data
	 * ]
	 */
	static public function sendMail ($emailInfo, $idMailer = 'default') {
		
		//capturando mailer
		$mailer = self::getMailer($idMailer);
		if( !$mailer ) {
			return false;
		}
		
		try {
			
			//validando configurações para envio de email
			self::validateEmailInfo($emailInfo);
			
			//SETANDO REMETENTE
			$mailer->From = $emailInfo['from']['email'];
			if( isset($emailInfo['from']['name']) ) {
				$mailer->FromName = $emailInfo['from']['name'];
			}
			//SETANDO DESTINOS
			//destino unico
			if( isset( $emailInfo['address']['email']) ) {
				//determinando nome
				$name = null;
				if( isset($emailInfo['address']['name']) ) { 
					$name = $emailInfo['address']['name'];
				}
				
				$mailer->addAddress($emailInfo['address']['email'], $name);
			}
			//multiplos destinos
			else {
				foreach ( $emailInfo['address'] as $dest ){
					//determinando nome
					$name = null;
					if( isset($dest['name']) ) {
						$name = $dest['name'];
					}
					
					$mailer->addAddress($dest['email'], $name);
				}
			}
			
			//SETAR REPLY TO
			if( isset( $emailInfo['replyTo']['email']) ) {
				//determinando nome
				$name = null;
				if( isset($emailInfo['address']['name']) ) { 
					$name = $emailInfo['address']['name'];
				}
			}
			
			//TODO SETAR CC
			
			//TODO SETAR BCC
			
			//CONFIGURANDO ASSUNTO E CORPO DO EMAIL
			$mailer->Subject = $emailInfo['subject'];
			$mailer->Body    = $emailInfo['body'];
			if( isset( $emailInfo['bodyPlain'] ) ) {
				$mailer->AltBody = $emailInfo['bodyPlain'];
			}
			
			//EFETUANDO O ENVIO
			if( !$mailer->send() ) {
				$errorMessage = $mailer->ErrorInfo;
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => $errorMessage
				));
			}
			
			//TODO SETAR REPLY TO
			//TODO SETAR CC
			//TODO SETAR BCC
				
			//TODO SETAR ATTACHMENT
			
			//retornando
			return array(
				'status' => true,
				'code' => null,
				'message' => null,
				'data' => null
			);
			
		}
		catch (\Sh\SheerException $e) {
			
			//retornando
			return array(
				'status' => false,
				'code' => $e->getErrorCode(),
				'message' => $e->getErrorMessage(),
				'data' => $mailer->ErrorInfo
			);
			
		}
		
		
		
	}
	
	/**
	 * Método para validar as informações do email que deve ser enviado
	 * @param array $emailInfo
	 * @throws \Sh\SheerException
	 */
	static protected function validateEmailInfo ($emailInfo) {
		
		//validando entrada dos dados
		if( !is_array($emailInfo) ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Configurações básicas do emails são inválidas'
			));
		}
		
		//validando email do remetente
		if( !isset($emailInfo['from']) || !isset($emailInfo['from']['email']) ) {
			throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Email do remetente não foi configurado'
			));
		}
		
		//Validando destinatarios
		if( !isset($emailInfo['address']) ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Não foram configurados destinatários'
			));
		}
		else {
			
			//se tivermos a posição email marcada, só temos um endereco
			if( isset($emailInfo['address']['email']) && strlen($emailInfo['address']['email']) < 3 ) {
				throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Destinatário inválido'
				));
			}
			else if( !isset($emailInfo['address']['email']) ) {
				$dest = reset($emailInfo['address']);
				if( !isset($dest['email']) || strlen($dest['email']) < 3 ) {
					throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Destinatário principal é inválido'
					));
				}
			}
			
		}
		
		//TODO VALIDAR EMAILS DOS CC
		//TODO VALIDAR EMAILS DOS BCC
		
		//Validando assunto
		if( !isset($emailInfo['subject']) && strlen($emailInfo['subject']) < 3 ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Assunto do email é inválido ou inexistente'
			));
		}
		//Validando o corpo do email
		if( !isset($emailInfo['body']) && strlen($emailInfo['body']) < 3 ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Corpo do email é inválido ou inexistente'
			));
		}
		
		
	}
	
	/**
	 * Método para produzir um mailer da classe phpMailer
	 * 
	 * @param string $idMailer
	 * @return \PHPMailer
	 * 			false em caso de erro
	 */
	static public function getMailer ( $idMailer = 'default' ) {
		
		//capturando configurações do mailer
		$config = self::getMailerConfig($idMailer);
		if( !$config ) {
			return false;
		}
		
		//Criando objeto de email do PHPMailer
		$phpMailer = new \PHPMailer();
		$phpMailer->IsSMTP();
		$phpMailer->CharSet = 'utf-8';
		$phpMailer->setLanguage('pt');
		$phpMailer->isHTML(true);
		//configurando servidor smtp e seguranca
		$phpMailer->Host = $config['host'];
		$phpMailer->SMTPAuth = true;
		$phpMailer->Username = $config['username'];
		$phpMailer->Password = $config['password'];
		
		return $phpMailer;
		
	}
	
	/**
	 * Método para recuperar as configurações de mailer desejadas
	 * @param string $idMailer
	 * @return array
	 */
	static protected function getMailerConfig($idMailer) {
		
		$mailerConfig = \Sh\ProjectConfig::getMailerConfiguration($idMailer);
		if( !$mailerConfig ) {
			return null;
		}
		return $mailerConfig;
	}
	
	
}