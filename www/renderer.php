<?php
header('Content-Type: text/html; charset=UTF-8');

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
require_once $sheerRootPath.'core/setup/setup.php';

\Sh\RendererRequest::process();


?>