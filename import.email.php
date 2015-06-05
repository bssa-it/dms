<?php

/**
 * @description
 * This script imports the email addresses for the contacts into the CiviCRM database.
 * 
 * @author      Chezre Fredericks
 * @date_created 18/12/2014
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

$sql = "SELECT 
`cleanEmail`(`e_mail`) AS `email`,
`civ_contact_id` AS `contact_id`,
dnr_no 
FROM `dms_profile` 
INNER JOIN dms_donor ON add_dnr_no = dnr_no
WHERE ((LENGTH(`e_mail`) - LENGTH(REPLACE(`e_mail`,'@',''))) > 0) AND civ_contact_id IS NOT NULL
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_email` WHERE `contact_id` = civ_contact_id);";

$cntAddresses = 0;
$cntInserted = 0;
$sdate = date("Y-m-d H:i:s");
$output = '';

# GET NEW DONORS
$emailAddresses = $db->select($sql);
    
if (empty($emailAddresses)) {
    #   IF THERE ARE NO NEW RECORDS EXIT THE SCRIPT.   
    $output .= date("Y-m-d H:i:s") . '<br />All email addresses imported !';
} else {
    # START THE IMPORT
    $emailAddressPattern = '#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
    $failedAddresses = '';
    foreach ($emailAddresses as $k=>$v) {
        try {
            $isPrimary = '1';
            $emails = explode(';',$v['email']);
            foreach ($emails as $e) {
                $cntAddresses++;
                if (!preg_match($emailAddressPattern,$e)) {
                    $failedAddresses .= '<br />' . $e . ' - dnr no ' .$v['dnr_no'];
                    continue;   
                }

                $insertParams['email'] = $e;
                $insertParams['contact_id'] = $v['contact_id'];
                $insertParams['is_primary'] = $isPrimary;
                $insertParams['location_type_id'] = 1;
                $result = civicrm_api3('Email', 'create', $insertParams);
    
                if ($result['is_error']==0) {
                    $cntInserted++;
                    $isPrimary = '0';   
                }   
            }
        } catch (Exception $e) {
            $output .= "<pre />";
            $output .= "New Error - Donor #" .$v['trxn_id'] . "<br />";
            $output .= print_r($e,true);
            $output .= "end error - Donor #" .$v['trxn_id'];
        }
    }
    $output .= '<p>Records retrieved from DMS: ' .$cntAddresses;
    $output .= '<br />Records inserted into CiviCRM: ' .$cntInserted. '</p>';
    $output .= '<p>Failed Addresses: '.$failedAddresses.'</p>';
}


#   UPDATE THE VARIABLES KEPT FOR THE LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';

#   INSERT THE NEW LOG
file_put_contents('logs/donorEmailImportLog.htm',$output,FILE_APPEND | LOCK_EX);

#   SHOW RESULTS
echo $output;