<?php
//DETERMINANDO PATH DO SHEER
$sheerRootPath = '../sheer/';
if( is_file('../sh.path') ) {
	$tmp = file_get_contents('../sh.path');
	//colocando a barra no final
	if( strrpos($tmp, '/') != strlen($tmp)-1 ) {
		$tmp = $tmp.'/';
	}
	//Verificando existencia da pasta
	if( is_dir($tmp) ) {
		$sheerRootPath = $tmp;
	}
}

//INCLUINDO O SHEER
require_once $sheerRootPath.'core/setup/setup-cli.php';

//DEFINIR QUAL A PERIODICIDADE DE EXECUÇÃO
$periodo = null;
if( isset($argv) && isset($argv['']) ) {
	
	switch($argv[1]) {
		case 'm':
		case 'h':
		case 'h3':
		case 'h6':
		case 'h12':
		case 'd':
		case 'w1':
		case 'w2':
		case 'w3':
		case 'w4':
		case 'w5':
		case 'w6':
		case 'w7':
			$periodo = $argv[1];
			break;
	}
	
}

\Sh\JobRunner::execAllJobs();

?>