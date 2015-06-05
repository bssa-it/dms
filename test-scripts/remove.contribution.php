<?php

/**
 * @description
 * This script is part of the daily ETL procedure.  It imports the contributions into the CiviCRM database
 * via the CiviCRM API.   The script calls itself again at the end so that it can get through all the unprocessed
 * records.   The exit point from this loop is when there are no records left to process.
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * @copyright   None
 * @version     4.0.1
 * 
 * @Changes
 * 05/12/2014 - Chezre Fredericks:
 * File created
 * 
 */

#   SKELETON BOOTSTRAP
include("inc/class.db.php");
$xmlConfig = simplexml_load_file("inc/config.xml");

$dbConfig               = $xmlConfig->databaseConnection;
$db                     = new database;
$db->username           = (string)$dbConfig->username;
$db->password           = (string)$dbConfig->password;
$db->host               = (string)$dbConfig->host;
$db->database           = (string)$dbConfig->database;
$db->connect(true);
$GLOBALS['db'] = $db;

$civiConfig             = $xmlConfig->civiConnection;
$civiDb                 = new database;
$civiDb->username       = (string)$civiConfig->username;
$civiDb->password       = (string)$civiConfig->password;
$civiDb->host           = (string)$civiConfig->host;
$civiDb->database       = (string)$civiConfig->database;
$civiDb->connect(true);
$GLOBALS['civiDBConnectionDetails'] = $xmlConfig->civiConnection;
        
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

include("inc/class.acknowledgementpreferences.php");
include("inc/class.functions.php");

$sql = "
SELECT T.id `contribution_id` FROM civicrm.`civicrm_contribution` T
INNER JOIN civicrm.`civicrm_contact` C ON T.`contact_id` = C.`id`
WHERE trxn_id NOT LIKE CONCAT('%',external_identifier)";

$cntRecords = 0;
$cntDeleted = 0;
$output = '';
$sdate = date("Y-m-d H:i:s");

set_time_limit(600);
#$contributions = $GLOBALS['db']->select($sql);
$contributions[] = array('contribution_id'=>2341660);


if (empty($contributions)) {
    #   NO RECORDS FOUND EXIT THIS SCRIPT
    $output = 'Script completed at: ' . date("Y-m-d H:i:s");
    echo $output;
    exit();  #  <--- SCRIPT EXIT POINT
}

foreach ($contributions as $k=>$v) {
    $parms = array();
    $parms['version'] = 3;
    foreach ($v as $parameter=>$value) $parms[$parameter] = $value;
    try {
        $result = civicrm_api('Contribution', 'delete', $parms);
        if (isset($result['id'])) $cntDeleted++;  
        
        $output .= "<pre />";
        $output .= print_r($result,true);
        
    } catch (Exception $e) {
        $output .= "<pre />";
        $output .= "New Error - Contribution Id #" .$v['id'] . "\n";
        $output .= print_r($e,true);
        $output .= "End error - Contribution Id #" .$v['id'];
    }
    $cntRecords++;
    
} 

$output .= 'Records retrieved from DMS: ' . $cntRecords;
$output .= '<br />Records inserted into CiviCRM: ' .$cntDeleted. '</p>';


#   UPDATE VARIABLES FOR LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';

echo $output;