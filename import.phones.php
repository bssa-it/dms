<?php

/**
 * @description
 * This script imports the contact phone numbers into the CiviCRM database.
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
    `civ_contact_id` AS `contact_id`,
    1 AS `location_type_id`,
    1 AS `is_primary`,
    `tel_no` AS `phone`,
    con_telNo AS `phone_numeric`,
    1 AS `phone_type_id`
FROM dms_profile
INNER JOIN `dms_donor` ON add_dnr_no = dnr_no
INNER JOIN `dms_cleanContactNumbers` ON con_dnr_no = add_dnr_no
WHERE `civ_contact_id` IS NOT NULL
AND (LENGTH(TRIM(`tel_no`)) > 0)
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_phone` WHERE `contact_id` = civ_contact_id)
UNION ALL
SELECT 
    `civ_contact_id` AS `contact_id`,
    1 AS `location_type_id`,
    CASE WHEN (TRIM(`tel_no`) > 0) THEN 0 ELSE 1 END AS `is_primary`,
    `cell_no` AS `phone`,
    `con_cellNo` AS `phone_numeric`,
    2 AS `phone_type_id`
FROM dms_profile
INNER JOIN `dms_donor` ON add_dnr_no = dnr_no
INNER JOIN `dms_cleanContactNumbers` ON con_dnr_no = add_dnr_no
WHERE `civ_contact_id` IS NOT NULL
AND (LENGTH(TRIM(`cell_no`)) > 0)
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_phone` WHERE `contact_id` = civ_contact_id)
UNION ALL
SELECT 
    `civ_contact_id` AS `contact_id`,
    1 AS `location_type_id`,
    0 AS `is_primary`,
    `fax_no` AS `phone`,
    `con_faxNo` AS `phone_numeric`,
    3 AS `phone_type_id`
FROM dms_profile
INNER JOIN `dms_donor` ON add_dnr_no = dnr_no
INNER JOIN `dms_cleanContactNumbers` ON con_dnr_no = add_dnr_no
WHERE `civ_contact_id` IS NOT NULL
AND (LENGTH(TRIM(`fax_no`)) > 0)
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_phone` WHERE `contact_id` = civ_contact_id)";

$cntInserted = 0;
$sdate = date("Y-m-d H:i:s");
$output = '';

# GET NEW DONORS
$phones = $db->select($sql);

if (empty($phones)) {
    #   IF THERE ARE NO NEW RECORDS EXIT THE SCRIPT.   
    $output .= date("Y-m-d H:i:s") . '<br />All phone numbers imported !';
} else {
    # START THE IMPORT
    foreach ($phones as $k=>$v) {
        try {
            $insertParams['version'] = 3;
            foreach ($v as $parameter=>$value) $insertParams[$parameter] = $value;
            $result = civicrm_api('Phone', 'create', $insertParams);
            if ($result['is_error']==0) $cntInserted++;
        } catch (Exception $e) {
            $output .= "<pre />";
            $output .= "New Error - Donor #" .$v['trxn_id'] . "\n";
            $output .= print_r($e,true);
            $output .= "end error - Donor #" .$v['trxn_id'];
        }
    }
    $output .= 'Records retrieved from DMS: ' . count($phones);
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
file_put_contents('logs/donorPhoneImportLog.htm',$output,FILE_APPEND | LOCK_EX);

#   SHOW RESULTS
echo $output;

?>