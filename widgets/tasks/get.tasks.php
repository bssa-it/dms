<?php

/**
 * @description
 * This script retrieves the group totals
 * 
 * @author      Chezre Fredericks
 * @date_created 15/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include '../../inc/globals.php';
$getMatch = false;

if (!empty($_SESSION['dms_user']['userid'])) {
    $xml = simplexml_load_file($_SESSION['dms_user']['userid'].".cricket.xml");
    $getMatch = (!empty($xml->matchId));
}

if ($getMatch) {
    $proxy = '129.47.16.13:3128';
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_URL, "cricscore-api.appspot.com/csa?id=".$xml->matchId); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch); 

    $result['result'] = json_decode($output);
} else {
    $result['result'] = '';
}
$result['matchOk'] = $getMatch;
print json_encode($result);