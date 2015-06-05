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

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

$session = new CRM_Core_Session;
$session->set('userID',2);

$sql = "SELECT "
        . "'civicrm_contact' `entity_table`,"
        . "civ_contact_id `entity_id`,"
        . "drm_text `note`,"
        . "2 `contact_id`,"
        . "drm_entry_date `modified_date`,"
        . "drm_text `subject`,"
        . "0 `privacy` "
        . "FROM dms_remark "
        . "INNER JOIN dms_donor ON drm_dnr_no = dnr_no "
        . "WHERE civ_contact_id IS NOT NULL "
        . "AND NOT EXISTS (SELECT id FROM civicrm.civicrm_note WHERE entity_id = civ_contact_id);";

$cntNotes = $cntInserted = 0;
$sdate = date("Y-m-d H:i:s");
$output = '';

# GET NEW DONORS
$notes = $db->select($sql);
    
if (empty($notes)) {
    #   IF THERE ARE NO NEW RECORDS EXIT THE SCRIPT.   
    $output .= date("Y-m-d H:i:s") . '<br />All notes imported !';
} else {
    # START THE IMPORT
    foreach ($notes as $n) {
        try {
            $insertParams['version'] = 3;
            foreach ($n as $k=>$v) $insertParams[$k] = $v;
            $result = civicrm_api('Note', 'create', $insertParams);

            if ($result['is_error']==0) $cntInserted++;
        } catch (Exception $e) {
            $output .= "<pre />";
            $output .= print_r($e,true);
        }
    }
    $output .= '<p>Records retrieved from DMS: ' .$cntNotes;
    $output .= '<br />Records inserted into CiviCRM: ' .$cntInserted. '</p>';
}


#   UPDATE THE VARIABLES KEPT FOR THE LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';

#   INSERT THE NEW LOG
file_put_contents('logs/notesImportLog.htm',$output,FILE_APPEND | LOCK_EX);

#   SHOW RESULTS
echo $output;