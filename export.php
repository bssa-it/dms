<?php

/**
 * @description
 * This script exports any array stored in the $_SESSION['export'] variable
 * 
 * @author      Chezre Fredericks
 * @date_created 21/01/2014
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");

if (empty($_SESSION['export']['array'])&&empty($_SESSION['dmsDonorSearchResultset'])) {
    echo "nothing to export";
    exit();
}
$filename = $_SESSION['export']['filename'];
if (!empty($_SESSION['export']['array'])) {
    $seshData =  $_SESSION['export']['array'];
    unset($_SESSION['export']['array']);
} else {
    $sowerExportCheckArray = array('group'=>(string)$GLOBALS['xmlConfig']->civiGroups->sower,'srch_database'=>'civicrm');
    if (!empty($_SESSION['dmsDonorSearchCriteria'])&&$_SESSION['dmsDonorSearchCriteria']==$sowerExportCheckArray) {
        # THIS IS A SOWER EXTRACT
        $filename = 'sower_extract_' . date("Ymd").'.txt';
        $seshData = $GLOBALS['functions']->getSowerData();
        $GLOBALS['functions']->exportArrayToCSV($filename,$seshData,true);
        exit();
    } else {
        $seshData = $_SESSION['dmsDonorSearchResultset'];
    }
}

#   IF NOTHING FOUND IN THE SESSION VARIABLE THEN EXIT
if (empty($filename)||empty($seshData)) exit();

foreach ($seshData as $s) {
    $mr = new mergerecord;
    $mr->LoadByContactId($s['contact_id']);
    
    $e = $mr->getMergeRecordHeadings();
    foreach ($mr->xmlFields->field as $m) {
        $f = (string)$m['id'];
        $e[(string)$m['label']] = $mr->$m['id'];
    }
    $exportArray[] = $e;
}

#   DOWNLOAD THE CURRENT ARRAY TO A CSV FILE
$GLOBALS['functions']->exportArrayToCSV($filename,$exportArray,true);