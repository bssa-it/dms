<?php

/**
 * @description
 * This script removes contacts from the database that have a Z dms indicator.
 * 
 * @author      Chezre Fredericks
 * @date_created 12/12/2014
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

#   SQL TO FIND DONORS WITH A CIVICRM CONTACT ID BUT THE DMS INDICATOR IS Z
$sql = "SELECT dnr_no,d.`civ_contact_id` `contact_id`,`civ_contribution_id` `contribution_id` FROM `dms_donor` d 
left JOIN  dms_transaction t ON trns_dnr_no = dnr_no 
WHERE `dnr_tax_certf` = 'Z' AND d.`civ_contact_id` IS NOT NULL";
#   SQL TO FIND DONORS WITH A CIVICRM CONTRIBUTION ID BUT THE DMS INDICATOR IS Z
$sql2 = "SELECT dnr_no,d.`civ_contact_id` `contact_id`,`civ_contribution_id` `contribution_id` FROM `dms_donor` d 
left JOIN  dms_transaction t ON trns_dnr_no = dnr_no 
WHERE `dnr_tax_certf` = 'Z' AND t.`civ_contribution_id` IS NOT NULL";

$cntDonors = 0;
$cntDeleted = 0;
$sdate = date("Y-m-d H:i:s");

$output = '';

# SET THE SCRIPT TIME OUT LIMIT TO PROTECT FROM MEMORY OVERLOAD
set_time_limit(600);
# GET THE TRANSACTIONS FROM THE 2 SQL QUERIES AND MERGE THE RESULT SETS (ARRAY FORM)
$trxns = array();
$batch1 = $db->select($sql);
if (!empty($batch1)) $trxns = array_merge($trxns,$batch1);
$batch2 = $db->select($sql2);
if (!empty($batch2)) $trxns = array_merge($trxns,$batch2);

if (empty($trxns)) {
    # IF THERE ARE NO TRANSACTIONS TO BE DELETED THEN EXIT SCRIPT
    echo 'All contacts removed ! ' . date("Y-m-d H:i:s");
    exit();   
}
# REMOVE EXISTING LOG
if (file_exists('logs/removeContactLog.htm')) unlink('logs/removeContactLog.htm');
$donors = array();
foreach ($trxns as $tc=>$t) {
    # REMOVE CONTRIBUTION FOR EACH RECORD IN RESULT SET
    try {
        if (!in_array($t['contact_id'],$donors)&&!empty($t['contact_id'])) $donors[] = $t['contact_id']; 
        if (!empty($t['contribution_id'])) {
            $params['version'] = 3;
            $params['id'] = $t['contribution_id'];
            # BEFORE DELETING THE RECORD CHECK IF IT EXISTS
            $exists = civicrm_api('Contribution', 'get', $params);
            # THEN DELETE RECORD
            $result = civicrm_api('Contribution', 'delete', $params);
            if ($exists['count']===0||$result['is_error']===0) {
                # UPDATE THE DMS TO MAKE SURE THE EXISTING LINK IS REMOVED
                $dSql = "UPDATE `dms_transaction` SET `civ_contribution_id` = NULL";
                $dSql .= " WHERE `civ_contribution_id` = " . $t['contribution_id']; 
                $updateResult = $db->execute($dSql);
                if ($updateResult) $cntDeleted++;
            }   
        }
    } catch (Exception $e) {
        $output = 'An error occured removing donor ' . $t['dnr_no'] . ': ' . $e->getMessage() . "<br />";
    }  
}

if (!empty($donors)) {
    # REMOVE THE DONOR RECORD FROM THE CIVICRM DB
    $params = array();
    $params['version'] = 3;
    foreach ($donors as $d) {
        try {
            $params['version'] = 3;
            $params['contact_id'] = $d;
            $params['skip_undelete'] = 1;
            $result = civicrm_api('Contact', 'delete', $params);
            if ($result['is_error']===0) {
                # UPDATE THE DMS DATABASE
                $dSql = "UPDATE `dms_donor` SET `civ_contact_id` = NULL";
                $dSql .= " WHERE `civ_contact_id` = " . $d; 
                $updateResult = $db->execute($dSql);
                if ($updateResult) $cntDonors++;
            }
        } catch (Exception $e) {
            $output = 'An error occured removing donor ' . $t['dnr_no'] . ': ' . $e->getMessage() . "<br />";
        }    
    }
}

# POPULATE VARIABLES FOR LOG
$output .= "<p>Data retrieval script completed<br />";
$output .= 'Contacts deleted from CiviCRM: ' . $cntDonors;
$output .= '<br />Transactions deleted CiviCRM: ' .$cntDeleted. '</p>';

# CREATE LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';

file_put_contents('logs/removeContactLog.htm',$output);

#   ECHO LOG DATA
echo $output;