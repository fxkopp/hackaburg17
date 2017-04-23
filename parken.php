<?php

$url = 'https://www.regensburg.de/parkinfo/rss/index.php';

$xml = file_get_contents($url);
$xml = simplexml_load_string($xml);

$garages = $xml->xpath('//item');

$message = array();
foreach ($garages as $garage) {
        $message[] = (String) $garage->xpath('title')[0][0];
}

echo json_encode($message);