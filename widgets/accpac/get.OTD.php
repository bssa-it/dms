<?php

/**
 * @description
 * This script retrieves the month end status from the previous month
 * 
 * @author      Chezre Fredericks
 * @date_created 14/10/2015
 * @Changes
 * 
 */

include '../../inc/globals.php';

ini_set('display_errors',0);
error_reporting(0);

$xml = simplexml_load_file('otd.connection.xml');
foreach ($xml->children() as $x) $conn[$x->getName()] = (string)$x;
$db105 = new database($conn);

try {
    $sdate = date("Y-m-d 00:00:01");
    $edate = date("Y-m-d 23:59:59");
    $sql = "select count(*) `total` from importdate where `FileType` = 'BSSA_SALESTRAN' and `DateImported` between '$sdate' and '$edate'";
    $result = $db105->select($sql);
    $return['dataLoaded'] = ($result[0]['total']==1);
    $return['errors'] = '';
} catch (Exception $e) {
    $return['dataLoaded'] = false;
    $return['errors'] = $e->getMessage();
}

print json_encode($return);