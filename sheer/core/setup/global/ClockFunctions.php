<?php

$currentTime = new \DateTime();
$time = array(
	'hour' => (int) $currentTime->format('H'),
	'minute' => (int) $currentTime->format('i'),
	'day' => (int) $currentTime->format('d'),
	'month' => (int) $currentTime->format('m')
);

\Sh\ContentLogCollector::run();