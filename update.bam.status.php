<?php

/**
 * @description
 * This script imports the BAM Club memberships into the memberships civi component
 * via the CiviCRM API.
 * 
 * @author      Chezre Fredericks
 * @date_created 03/12/2014
 * @Changes
 * 
 */

#   SKELETON BOOTSTRAP
include("inc/class.db.php");

$xmlConfig = simplexml_load_file("inc/config.xml");
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

#   LOAD THE CIVICRM API CONFIG
require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

#   GET THE BAM RECORDS FROM THE SYSTEM
$bamMemId = (string)$xmlConfig->bam->civiMembershipTypeId;
$sql = "select * from civicrm_membership where membership_type_id = $bamMemId";

$sdate = date("Y-m-d H:i:s");

#   SET TIME LIMIT TO PROTECT AGAINST MEMORY OVERLOAD
set_time_limit(600);
$memberships = $civiDb->select($sql);

$output = '';
foreach ($memberships as $k=>$v) {
  
    try { 
        $membership['version'] = 3;
        $membership['skipStatusCal'] = 0;
        
        $membership['id'] = $v['id'];
        $membership['join_date'] = $v['join_date'];
        $membership['start_date'] = $v['start_date'];
        $result = civicrm_api('Membership','create',$membership);
    } catch (Exception $e) {
        $output .= "<pre />";
        $output .= "New Error - Membership #" .$v['id'] . "\n";
        $output .= print_r($e,true);
        $output .= "end error - Membership #" .$v['id'];
    }
    
}
$output .= 'Records retrieved from DMS: ' . count($memberships);

#   UPDATE VARIABLES FOR LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';

# SAVE LOG
file_put_contents('logs/bamStatusUpdateLog.htm',$output,FILE_APPEND | LOCK_EX);

echo $output;