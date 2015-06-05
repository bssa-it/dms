<?php

/**
 * @description
 * This script is part of the ETL procedure.   It updates the sower group.
 * 
 * @author      Chezre Fredericks
 * @date_created 11/12/2014
 * @Changes
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

$civiConfig             = $xmlConfig->civiConnection;
$civiDb                 = new database;
$civiDb->username       = (string)$civiConfig->username;
$civiDb->password       = (string)$civiConfig->password;
$civiDb->host           = (string)$civiConfig->host;
$civiDb->database       = (string)$civiConfig->database;
$civiDb->connect(true);

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

$session = new CRM_Core_Session;
$session->set('userID',2);

/*  old sql

$sql = "SELECT civ_contact_id AS contact_id,1 as group_id,dnr_sower FROM `dms_donor` 
WHERE civ_contact_id IS NOT NULL AND dnr_no not between 100000 and 199999
order by civ_contact_id
LIMIT $currentLimit";

*/

#   GET NEXT BATCH OF RECORDS TO UPDATE
$sql = "SELECT civ_contact_id AS contact_id,1 AS group_id,dnr_sower FROM `dms_donor` 
WHERE 
civ_contact_id IS NOT NULL 
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_group_contact` WHERE `contact_id` = civ_contact_id)
AND dnr_sower = 'Y'
ORDER BY civ_contact_id";

$cntRecords = 0;
$cntInserted = 0;
$sdate = date("Y-m-d H:i:s");

$sowerList = $db->select($sql);
$output = '';
if (empty($sowerList)) {
    #   IF THE LIMIT IS BEYOND THE NUMBER OF RECORDS THEN THE SCRIPT WILL EXIT
    $output .= 'All sower flags imported';
} else {
    #   SET THE TIME LIMIT FOR THE SCRIPT
    ini_set('max_execution_time', 600);
    
    foreach ($sowerList as $k=>$v) {
        try {
            foreach ($v as $parameter=>$value) $insertParams[$parameter] = $value;
            unset($insertParams['dnr_sower']);
            $insertParams['version'] = 3;
            $result = civicrm_api('group_contact', 'create', $insertParams);
            $cntInserted++;
        } catch (Exception $e) {
            $output .= "<pre />";
            $output .= "New Error - Donor #" .$v['trxn_id'] . "\n";
            $output .= print_r($e,true);
            $output .= "end error - Donor #" .$v['trxn_id'];
        }
        
    }
    
}

$output .= '<p>Records retrieved from DMS: ' . count($sowerList);
$output .= '<br />Records inserted into CiviCRM: ' .$cntInserted. '</p>';

#   RESET THE MAX EXECUTION TIME
ini_set('max_execution_time', 30);

#   UPDATE THE VARIABLES FOR THE LOG
$edate = date('Y-m-d H:i:s');
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';
#   CREATE THE LOG
file_put_contents('logs/sowerImportLog.htm',$output,FILE_APPEND | LOCK_EX);

echo $output;