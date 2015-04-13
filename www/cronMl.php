<?php
header('Content-Type: text/html; charset=UTF-8');
//INCLUINDO O SHEER
define('SH_CLI', true);
require_once '../sheer/core/setup/setup-cli.php';

\Sh\JobRunner::execJob('malaDiretaDisparo/dispararEmails');

?>