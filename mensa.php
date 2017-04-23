<?php
error_reporting(0);
header('Content-Type: text/html; charset=utf-8');

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();

// POST Request
$url = 'https://stwno.de/infomax/daten-extern/html/speiseplan-render.php';
$response = $client->request('POST', $url, [
        'form_params' => [
                'func' => 'make_spl',
                'locId' => 'UNI-R',
                'lang' => 'de',
                'date' => '2017-04-24',
                'w' => ''
        ]
]);

// echo $response->getBody();
// die();

$dom = new DomDocument;
$dom->loadHTML(utf8_decode((String) $response->getBody()));
$xpath = new DomXPath($dom);

$trs = $xpath->query("//tr");

$found = false;
$gerichte = [];
foreach ($trs as $tr) {
        $titel = $xpath->query("td[@class='cell0 bold right']",$tr);
        if ($titel[0] !== NULL) {
                if (trim($titel[0]->nodeValue) === "Hauptgerichte") {
                        $found = true;
                }
        }

        if ($titel[0] !== NULL) {
                if (trim($titel[0]->nodeValue) === "Hauptgerichte" OR trim($titel[0]->nodeValue) === "") {
                } else {
                        $found = false;
                }
        }

        if ($found == true) {
                $artikel = $xpath->query("td/div[@class='artikel']", $tr);
                if (preg_match('/\(.+\)/', (String) $artikel[0]->nodeValue, $match)) {
                        $name = str_replace($match[0], "", $artikel[0]->nodeValue);
                        $gerichte[] = $name;
                }

        }
}


echo json_encode($gerichte);