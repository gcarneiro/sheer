<?php
header('Content-Type: text/html; charset=UTF-8');
//INCLUINDO O SHEER
define('SH_CLI', true);
require_once '../sheer/core/setup/setup-cli.php';

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