<?php

$url = 'http://mobile.defas-fgi.de/begu/XML_DM_REQUEST?language=en&mode=direct&coordOutputFormat=MRCV&mergeDep=1&maxTimeLoop=1&canChangeMOT=0&useAllStops=1&useRealtime=1&locationServerActive=1&depType=stopEvents&includeCompleteStopSeq=1&name_dm=4014080&type_dm=stop&imparedOptionsActive=1&excludedMeans=checkbox&useProxFootSearch=0&itOptionsActive=1&trITMOTvalue100=10&changeSpeed=normal&routeType=LEASTTIME&ptOptionsActive=1&limit=30&useRealtime=1&mobileAppTripMacro=true';

$xml = file_get_contents($url);
$xml = simplexml_load_string($xml);

$stops = $xml->xpath('//dps/dp');
$dests = array();

$loops = 0;
foreach ($stops as $stop) {
        $m = $stop->xpath('m')[0];
        $des = $m->xpath('des/text()');
        // $dests[] = (String) $des[0][0];
        // Calculate minutes
        // print_r($stop->xpath('dt/rt'));
        $busTimeString = (!empty($stop->xpath('dt/rt'))) ? (String) $stop->xpath('dt/rt')[0][0] : '';
        $busTime = strtotime($busTimeString);
        $difference = round(($busTime - time()) / 60, 0);
        // print_r($busTime . '    ' . time() . '   -   ' . $difference);die();
        if ($difference > 0) {
                $dests[] = array(
                        'name' => (String) $des[0][0],
                        'leavesIn' => $difference
                );

        if ($loops > 3) {
                break;
        }

        $loops++;
        } else {
        }
}

echo json_encode($dests);