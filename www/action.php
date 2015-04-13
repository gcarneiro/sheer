<?php
header('Content-Type: text/html; charset=UTF-8');
//INCLUINDO O SHEER
require_once '../sheer/core/setup/setup.php';

$response = \Sh\ActionRequest::process();
echo json_encode($response);
?>