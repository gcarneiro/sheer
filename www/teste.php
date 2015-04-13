<?php
header('Content-Type: text/html; charset=UTF-8');
//INCLUINDO O SHEER
require_once '../sheer/core/setup/global.php';

$inicio = microtime(true);

// \Sh\LibraryValidation::validateTelefone('(22s) 97402.7167');


/*
//Criando conexão com o mongo
$mongodb = new MongoClient();
//Capturando dados de pessoas
$dbSilvaJardim = $mongodb->silvaJardim;
$dbTbPessoas = $dbSilvaJardim->pessoas;

//Carregando de duas formas diferentes
$x1 = microtime(true);
$cursor = $dbTbPessoas->find(['id'=>'C811F3C7-985C-462C-AF11-9A7FDCEB10D1']);
foreach ( $cursor as $pessoa ) {
	var_dump($pessoa);
}
$x2 = microtime(true);
var_dump($x1);
var_dump($x2);
var_dump($x2 - $x1);

$x1 = microtime(true);
$cursor = $dbTbPessoas->find(['_id'=>'C811F3C7-985C-462C-AF11-9A7FDCEB10D1']);
foreach ( $cursor as $pessoa ) {
	var_dump($pessoa);
}
$x2 = microtime(true);
var_dump($x1);
var_dump($x2);
var_dump($x2 - $x1);

*/
$fim = microtime(true);

var_dump($inicio);
var_dump($fim);
var_dump($fim - $inicio );
