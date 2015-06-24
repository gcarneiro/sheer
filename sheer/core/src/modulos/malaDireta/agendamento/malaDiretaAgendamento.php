<?php
namespace Sh\Modules\malaDiretaAgendamento;

use Sh\ActionException;
class malaDiretaAgendamento {
	
	static public $status = array(
		1 => 'Realizado',
		2 => 'Pendente',
		3 => 'Cancelado',
	);
}

/**
 * ACTIONHANDLER para adicionar um agendamento de envio a uma mala direta.
 * @author Patrick
 */
class adicionarAgendamento extends \Sh\GenericAction {
	
	public function doAction($data){
		
		//VERIFICANDO DATA > HOJE
		//crio um objeto dateTime com a data e hora atual
		$hoje = new \DateTimeImmutable();
		
		//junto a data e hora em uma variavel para criar um datetime com hora e data para comparação
		$dataHoraAgendamento = $data['data'].' '.$data['hora'];
		$dataHoraAgendamento = \DateTimeImmutable::createFromFormat('d/m/Y H:i', $dataHoraAgendamento);
		
		//comparo a data e hora inserida coma a atual
		if( $dataHoraAgendamento <= $hoje ){
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'A data e hora de agendamento deve ser posterior a data e hora atual.'
			));
		}
		
		//BUSCO A CAMPANHA PARA RECUPERAR OS DADOS DELA
		
		$campanha = \Sh\ContentProviderManager::loadContentById('malaDiretaCampanha/malaDiretaCampanha', $data['idCampanha']);
		
		//montando array de inserção
		$adicionarAgendamento['id']				 = \Sh\Library::getUniqueId();
		$adicionarAgendamento['idCampanha']		 = $campanha['id'];
		$adicionarAgendamento['idRemetente']	 = $campanha['idRemetente'];
		$adicionarAgendamento['assunto']		 = $campanha['assunto'];
		$adicionarAgendamento['html']			 = $campanha['html'];
		$adicionarAgendamento['data']			 = $data['data'];
		$adicionarAgendamento['hora']			 = $data['hora'];
		
		//adiciono o agendamento
		$response = \Sh\ContentActionManager::doAction('malaDiretaAgendamento/malaDiretaAgendamento_add', $adicionarAgendamento ,$this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//para cada lista enviada, eu crio um disparoLista
		foreach ($data['idLista'] as $idLista) {
			
			$adicionarDisparoLista['idAgendamento'] = $adicionarAgendamento['id'];
			$adicionarDisparoLista['idLista']		= $idLista;
			
			//adiciono o disparoLista
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparoLista/malaDiretaDisparoLista_add', $adicionarDisparoLista, $this->connection);
			\Sh\Library::actionResponseCheck($response);
			
		};
		
		return array(
			'status' => true,
			'code' => null,
			'data' =>$response['data']
		);
		
	}
}

/**
 * ACTIONHANDLER para cancelar um agendamento
 * @author Patrick
 */
class cancelarAgendamento extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//verifico se o agendamento estÃ¡ realmente pendente para cancelar
		$agendamento = \Sh\ContentProviderManager::loadContentById('malaDiretaAgendamento/malaDiretaAgendamento', $data['id']);
		if($agendamento['status']!=2){
			throw new \Sh\ActionException(array(
				'message' => 'Agendamento realizado não pode ser cancelado',
				'code' => null
			));
		}
		
		$cancelarAgendamento = array(
			'id' => $data['id'],
			'status' => 3
		);
		
		$response = \Sh\ContentActionManager::doAction('malaDiretaAgendamento/malaDiretaAgendamento_update', $cancelarAgendamento, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return array(
			'status' => true,
			'code' => null,
			'data' => $response['data']
		);
		
	}
}

/**
 * Job para disparar os emails da campanha agendada
 * @author Patrick
 */
class enviarAgendamento extends \Sh\GenericJob {
	
	public function run() {
		//carrego todos os agendamentos pendentes e com datas anteriores ou igual a hoje
		$agendamentos = \Sh\ContentProviderManager::loadContent('malaDiretaAgendamento/agendamentoPendente', array('data'=>date('d/m/Y')));
		
		//verifico se tenho agendamentos para ser enviados
		if($agendamentos['total'] == 0){
			return;
		}
		//Abrindo conexão
		$connection = \Sh\DatabaseConnectionProvider::getDatabaseConnection();
		
		foreach ($agendamentos['results'] as $agendamento){
			
			//monto um datetime com a data e hora do agendamento
			$dataHoraAgendamento = $agendamento['data']['date'].' '.$agendamento['hora'];
			$dataHoraAgendamento = \DateTime::createFromFormat('d/m/Y H:i', $dataHoraAgendamento);
			
			//verifico se estou no dia e hora do envio da mala direta
			if( $dataHoraAgendamento > $this->moment ){
				continue;
			}
			
			$idLista = '';
			foreach($agendamento['disparoLista'] as $element){
				$idLista .= $element['idLista'].',';
			}
			$idLista = rtrim($idLista,',');
			
			//monto um array com os dados do agendamento para diparar os emails
			$dispararEmails = array(
				'idAgendamento' => $agendamento['id'],
				'idCampanha' => $agendamento['idCampanha'],
				'idRemetente' => $agendamento['idRemetente'],
				'idLista' => $idLista,
				'html' => $agendamento['html'],
				'assunto' => $agendamento['assunto'],
			);
			
			//chamo a acão que vai disparar os emails da campanha
			$response = \Sh\ContentActionManager::doAction('malaDiretaDisparo/dispararMalaDireta', $dispararEmails, $connection);
			
			//verifico se ela foi enviada corretamente e atualizo o agendamento como realizado
			if($response['status']){
				$atualizarAgendamento = array(
					'id' => $agendamento['id'],
					'status' => 1
				);
				
				\Sh\ContentActionManager::doAction('malaDiretaAgendamento/malaDiretaAgendamento_update', $atualizarAgendamento, $connection);
			}
			$connection->commit();
		}
	}
}