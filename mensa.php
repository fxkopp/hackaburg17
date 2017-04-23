<?php

$url = 'https://stwno.de/infomax/daten-extern/html/speiseplaene.php?einrichtung=HS-R-tag';

$dom = new DomDocument;
$dom->loadHTMLFile($url);
$xpath = new DomXPath($dom);

$food = $xpath->query("//div");
// print_r($food);
foreach ($food as $f) {
        print_r($f);
}


echo json_encode('111');