<?php
/*
 * Este processador serve para renderizarmos uma página consumindo o conteúdo dos seus holders apenas
 * Ele irá responder em json com "status:boolean", "holders:object:html"
 */
header('Content-Type: text/html; charset=UTF-8');
//INCLUINDO O SHEER
require_once '../sheer/core/setup/setup.php';

\Sh\PageRequest::processHolders();


?>