<?php
set_time_limit(6000);
header('Content-Type: text/html; charset=UTF-8');
//INCLUINDO O SHEER
require_once '../sheer/core/setup/global-cli.php';


function verificaNulo($valor){
	if(!isset($valor) || !$valor){
		return 'NULL';
	}
	return '"'.$valor.'"';
}


\Sh\ContentActionManager::invokeGreenCard();

function importaMailer () {
	
	$connMAILER = \Sh\DatabaseConnectionProvider::newDatabaseConnection('mailer');
	$connSHEER = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
	$connSHEER->exec('SET foreign_key_checks = 0;');
	
	//Agendamento
	$selectAgendamentoElement = $connMAILER->query('SELECT * FROM sh_ml_agendamento LIMIT 0,100000;');
	$elementMAILER = $selectAgendamentoElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elementMAILER !== false) {
		foreach($elementMAILER as $element){
			$element['data'] = \Sh\FieldDate::formatPrimitiveDataToSheer($element['data']);
			$element['data'] = $element['data']['date'];
			
			$element['criadoEm'] = \Sh\FieldDate::formatPrimitiveDataToSheer($element['criadoEm']);
			$element['criadoEm'] = $element['criadoEm']['date'];
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaAgendamento/malaDiretaAgendamento', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	
	$disparoLista = '';
	
	//Disparo
	$selectAgendamentoElement = $connMAILER->query('SELECT * FROM sh_ml_disparo LIMIT 0,100000;');
	$elementMAILER = $selectAgendamentoElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elementMAILER !== false) {
		foreach ($elementMAILER as $element) {
			$element['disparadoEm'] = \Sh\FieldDate::formatPrimitiveDataToSheer($element['disparadoEm']);
			$element['disparadoEm'] = $element['disparadoEm']['date'];
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaDisparo/malaDiretaDisparo', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
			
			$id = \Sh\Library::getUniqueId();
			
			$disparoLista[$id] = array (
				'id' => $id,
				'idLista' => $element['idLista'],
				'idAgendamento' => $element['idAgendamento'],
				'idDisparo' => $element['id']
			);
		}
	}
	
	//Disparo Remoção
	$selectAgendamentoElement = $connMAILER->query('SELECT * FROM sh_ml_disparoRemocao LIMIT 0,100000;');
	$elementMAILER = $selectAgendamentoElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elementMAILER !== false) {
		foreach ($elementMAILER as $element) {
			$element['adicionadoEm'] = \Sh\FieldDateTime::formatPrimitiveDataToSheer($element['adicionadoEm']);
			$element['adicionadoEm'] = $element['adicionadoEm']['datetime'];
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaDisparo/malaDiretaDisparoRemocao', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	//Disparo Visualização
	$selectAgendamentoElement = $connMAILER->query('SELECT * FROM sh_ml_disparoVisualizacao LIMIT 0,100000;');
	$elementMAILER = $selectAgendamentoElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elementMAILER !== false) {
		foreach ($elementMAILER as $element) {
			$element['adicionadoEm'] = \Sh\FieldDateTime::formatPrimitiveDataToSheer($element['adicionadoEm']);
			$element['adicionadoEm'] = $element['adicionadoEm']['datetime'];
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaDisparo/malaDiretaDisparoVisualizacao', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	
	//Lista de Emails
	$selectAgendamentoElement = $connMAILER->query('SELECT * FROM sh_ml_lista LIMIT 0,100000;');
	$elementMAILER = $selectAgendamentoElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elementMAILER !== false) {
		foreach ($elementMAILER as $element) {
			$element['criadoEm'] = \Sh\FieldDateTime::formatPrimitiveDataToSheer($element['criadoEm']);
			$element['criadoEm'] = $element['criadoEm']['datetime'];
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaLista/malaDiretaLista', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	if($disparoLista != null) {
		foreach ($disparoLista as $element) {
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaDisparoLista/malaDiretaDisparoLista', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	//Contatos
	$selectAgendamentoElement = $connMAILER->query('SELECT * FROM sh_ml_listaEmail LIMIT 0,100000;');
	
	if($selectAgendamentoElement !== false) {
		$elementMAILER = $selectAgendamentoElement->fetchAll(\PDO::FETCH_ASSOC);
		
		if($elementMAILER !== false) {
			
			$totalRepetidos = 0;
			
			foreach ($elementMAILER as $element) {
				
				if($element['email'] == '') {
					continue;
				}
				
				if(\Sh\LibraryValidation::validateEmail($element['email']) == false){
					var_dump($element['email']);
				}
					
				$selectContato = $connSHEER->query('SELECT * FROM sh_ml_contato WHERE email="'.$element['email'].'";');
				if($selectContato === false){
					var_dump('Erro');
					return;
				}
				
				$contatoTB = $selectContato->fetchAll(\PDO::FETCH_ASSOC);
				if(!$contatoTB) {
					$contato = array(
						'id' => \Sh\Library::getUniqueId(),
						'nome' => $element['nome'],
						'email' => $element['email'],
					);
					
					$exec = 'INSERT INTO sh_ml_contato values ('.verificaNulo($contato['id']).', "'.$contato['nome'].'", '.verificaNulo($contato['email']).');';
					$insertContato = $connSHEER->exec($exec);
					
					if($insertContato === false){
						var_dump('Ocorreu um erro na importação de um contato.');
						return;
					}
				}
				else {
					$contato = reset($contatoTB);
				}
				
				$selectEmail = $connSHEER->query('SELECT * FROM sh_ml_listaEmail WHERE idContato="'.$contato['id'].'" AND idLista="'.$element['idLista'].'";');
				
				if($selectEmail === false){
					var_dump('Erro');
					return;
				}
				
				$emailTB = $selectEmail->fetchAll(\PDO::FETCH_ASSOC);
				
				if(!$emailTB){
					$element['idContato'] = $contato['id'];
					$element['adicionadoEm'] = \Sh\FieldDateTime::formatPrimitiveDataToSheer($element['adicionadoEm']);
					$element['adicionadoEm'] = $element['adicionadoEm']['dateTime']->format('Y-m-d H:i:s');
					
					$exec = 'INSERT INTO sh_ml_listaEmail values ('.verificaNulo($element['id']).', '.verificaNulo($element['idLista']).', '.verificaNulo($contato['id']).', '.verificaNulo($element['enviar']).', '.verificaNulo($element['adicionadoEm']).', '.verificaNulo($element['idImportacao']).')';
					$adicionarContatoInEmail = $connSHEER->exec($exec);
					
					if($adicionarContatoInEmail === false) {
						var_dump('Ocorreu um erro na importação de lista de email.');
						return;
					}
				}
			}
			
			var_dump($totalRepetidos);
		}
	}
	
	$connSHEER->commit();
	
}


function importaResto(){
	
	$connMAILER = \Sh\DatabaseConnectionProvider::newDatabaseConnection('mailer');
	$connSHEER = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
	$connSHEER->exec('SET foreign_key_checks = 0;');
	
	//CAMPANHA
	$selectElement = $connMAILER->query('SELECT * FROM sh_ml_campanha LIMIT 0,100000;');
	$elements = $selectElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elements !== false) {
		foreach($elements as $element){
			
			$element['criadoEm'] = \Sh\FieldDate::formatPrimitiveDataToSheer($element['criadoEm']);
			$element['criadoEm'] = $element['criadoEm']['date'];
			
			$element['atualizadoEm'] = \Sh\FieldDateTime::formatPrimitiveDataToSheer($element['atualizadoEm']);
			$element['atualizadoEm'] = $element['atualizadoEm']['datetime'];
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaCampanha/malaDiretaCampanha', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	//REMETENTE
	$selectElement = $connMAILER->query('SELECT * FROM sh_ml_remetente LIMIT 0,100000;');
	$elements = $selectElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elements !== false) {
		foreach($elements as $element){
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaRemetente/malaDiretaRemetente', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	//ENVIO TESTE
	$selectElement = $connMAILER->query('SELECT * FROM sh_ml_envioTeste LIMIT 0,100000;');
	$elements = $selectElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elements !== false) {
		foreach($elements as $element){
			
			$element['envioEm'] = \DateTime::createFromFormat('Y-m-d H:i:s',$element['envioEm']);
			$element['envioEm'] = $element['envioEm']->format('d/m/Y H:i:s');
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaEnvioTeste/malaDiretaEnvioTeste', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	//ARQUIVOS
	$selectElement = $connMAILER->query('SELECT * FROM sh_fileDocument LIMIT 0,100000;');
	$elements = $selectElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elements !== false) {
		foreach($elements as $element){
			$exec = 'INSERT INTO sh_fileDocument values ('.verificaNulo($element['id']).', '.verificaNulo($element['size']).', '.verificaNulo($element['name']).', '.verificaNulo($element['nameFull']).', '.verificaNulo($element['nameExt']).', '.verificaNulo($element['path']).', '.verificaNulo($element['adicionadoEm']).', '.verificaNulo($element['adicionadoPor']).', '.$element['downloads'].', '.verificaNulo($element['mimeType']).', '.verificaNulo($element['remove']).' )';
			$adicionar = $connSHEER->exec($exec);
			if($adicionar === false){
				var_dump('Erro em arquivo.');
				return;
			}
		}
	}
	
	//LISTAIMPORTACAO
	$selectElement = $connMAILER->query('SELECT * FROM sh_ml_listaImportacao LIMIT 0,100000;');
	$elements = $selectElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elements !== false) {
		foreach($elements as $element){
			$exec = 'INSERT INTO sh_ml_listaImportacao values ('.verificaNulo($element['id']).', '.verificaNulo($element['idLista']).', '.verificaNulo($element['importadoEm']).', '.verificaNulo($element['importadoPor']).', '.$element['total'].', '.$element['novos'].', '.$element['atualizados'].', '.verificaNulo($element['ativar']).', '.verificaNulo($element['arquivo']).')';
			$adicionar = $connSHEER->exec($exec);
			if($adicionar === false){
				var_dump('Erro em lista importacao.');
				return;
			}
		}
	}
	
	//LISTAIMPORTACAO
	$selectElement = $connMAILER->query('SELECT * FROM sh_ml_listaSincronizacao LIMIT 0,100000;');
	$elements = $selectElement->fetchAll(\PDO::FETCH_ASSOC);
	
	if($elements !== false) {
		foreach($elements as $element){
			
			$element['sincronizadoEm'] = \Sh\FieldDateTime::formatPrimitiveDataToSheer($element['sincronizadoEm']);
			$element['sincronizadoEm'] = $element['sincronizadoEm']['datetime'];
			
			$response = \Sh\ContentActionManager::doPrimitiveAction('malaDiretaLista/malaDiretaListaSincronizacao', 'add', $element, $connSHEER);
			\Sh\Library::actionResponseCheck($response);
		}
	}
	
	
	$connSHEER->commit();
}

// importaResto();
importaMailer();