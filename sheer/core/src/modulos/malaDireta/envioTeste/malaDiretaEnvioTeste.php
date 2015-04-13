<?php
namespace Sh\Modules\malaDiretaEnvioTeste;

/**
 * ACTIONHANDLER de para adicionar um envio de teste, e enviar os emails de teste
 * @author Patrick
 */
class enviarTeste extends \Sh\GenericAction {
	
	public function doAction($data){
		
		//carrego a campanha para cadastrar os dados no envio
		$campanha = \Sh\ContentProviderManager::loadContentById('malaDiretaCampanha/malaDiretaCampanha', $data['idCampanha']);
		
		$adicionarEnvio = array();
		
		//montando array de inserção
		$adicionarEnvio['idCampanha']	 = $campanha['id'];
		$adicionarEnvio['idRemetente']	 = $campanha['idRemetente'];
		$adicionarEnvio['assunto']		 = $campanha['assunto'];
		$adicionarEnvio['html']			 = $campanha['html'];
		$adicionarEnvio['envioPor']		 = \Sh\AuthenticationControl::getAuthenticatedUserInfo('id');
		$adicionarEnvio['envioEm']		 = date('d/m/Y H:i:s');
		$adicionarEnvio['destinos']		 = $data['destinos'];
		
		
		//enviar emails de teste
		{
			//monto o html com o conteudo da campanha
			$html = '';
			$html .= '<div style="text-align:center;">';
				$html .= '<small>Caso não consiga visualizar a mensagem clique <a target="_blank" href="#">aqui</a></small>';
			$html .= '</div>';
			
			$html .= '<div>';
				$html .= $campanha['html'];
			$html .= '</div>';
			
			$html .= '<div style="text-align:center;">';
				$html .= '<small>Caso não queira receber esse tipo de e-mail clique <a target="_blank" href="#">aqui</a></small>';
			$html .= '</div>';
			
			
			//transformo todos os emails em array
			$emails = preg_split("/[\;\,]/", $data['destinos'] );
			$emailsLista = array();
			
			//coloco os emails no formato do mailerprovider
			foreach ($emails as $k=>$email){
				$emailsLista[$k]['name']= null;
				$emailsLista[$k]['email']= $email;
			}
			
			//envio os emails de teste
			$dadosEmail = array(
				'address' => $emailsLista,
				'from' => array('name'=>$campanha['remetente']['nomeEnvio'], 'email'=>$campanha['remetente']['emailEnvio']),
				'subject' => $campanha['assunto'],
				'body' => $html
			);
			
			\Sh\MailerProvider::sendMail($dadosEmail);
			
		}
		
		//cadastro o envio
		$response = \Sh\ContentActionManager::doAction('malaDiretaEnvioTeste/malaDiretaEnvioTeste_add', $adicionarEnvio, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return array(
			'status' => true,
			'code' => null,
			'data' => $response['data'],
		);
		
	}
}