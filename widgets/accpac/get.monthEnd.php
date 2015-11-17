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

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include '../../inc/globals.php';

$fileList = glob("../../../monthEnd/work/*.xml");
$requiredFile = date("Y-m",strtotime('last month')) . '.xml';
$return['isComplete'] = 'N';
$return['requiredFile'] = $requiredFile;
foreach ($fileList as $f) {
    $fname = basename($f);
    $return['filelist'][] = $fname;
    if ($return['isComplete']=='Y') break;
    $return['isComplete'] = ($fname==$requiredFile) ? 'Y':'N';
    /* if (preg_match('/([0-9]{4})-([0-9]{2})/', $fname)) $return[] = $fname; */
}
print json_encode($return);