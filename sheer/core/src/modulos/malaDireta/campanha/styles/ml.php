<?php
//CARREGO O HTML DE ACORDO COM O ID PASSADO

//ID DA CAMPANHA
if(isset($_GET['idCampanha'])){
	$campanha = \Sh\ContentProviderManager::loadItem('malaDiretaCampanha/malaDiretaCampanha', $_GET['idCampanha']);
	echo '<title>'.$campanha['assunto'].'</title>';
	echo $campanha['html'];
}

//ID DO DISPARO
else if(isset($_GET['idDisparo'])){

	$disparo = \Sh\ContentProviderManager::loadItem('malaDiretaDisparo/malaDiretaDisparo', $_GET['idDisparo']);
	echo '<title>'.$disparo['assunto'].'</title>';
	echo $disparo['html'];
}

//ID DO AGENDAMENTO
else if(isset($_GET['idAgendamento'])){

	$agendamento = \Sh\ContentProviderManager::loadItem('malaDiretaAgendamento/malaDiretaAgendamento', $_GET['idAgendamento']);
	echo '<title>'.$agendamento['assunto'].'</title>';
	echo $agendamento['html'];
}

//ID DO TESTE DE ENVIO
else if(isset($_GET['idTesteEnvio'])){

	$testeEnvio = \Sh\ContentProviderManager::loadItem('malaDiretaEnvioTeste/malaDiretaEnvioTeste', $_GET['idTesteEnvio']);
	echo '<title>'.$testeEnvio['assunto'].'</title>';
	echo $testeEnvio['html'];
}

//VISUALIZAÇÃO DO CLIENTE
else if(isset($_GET['t']) || isset($_GET['u']) || isset($_GET['d'])){
	
	$data = array(
			'tipo' => $_GET['t'],
			'idUser' => $_GET['u'],
			'idDisparo' => $_GET['d']
	);
	
	$conteudo = \Sh\ContentActionManager::doAction('malaDiretaCampanha/malaDiretaActionPublico', $data);
}

else{
	echo 'Erro ao carregar conteúdo';
}
