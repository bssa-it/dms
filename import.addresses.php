<?php

/**
 * @description
 * This script imports the addresses for contacts into the CiviCRM database.
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

$session = new CRM_Core_Session;
$session->set('userID',2);

$sql = "SELECT 
     `civ_contact_id` AS `contact_id`,
    1 AS `location_type_id`,
    1 AS `is_primary`,
    CASE WHEN LENGTH(TRIM(SUBSTR(`dnr_addr`,1,30))) = 0 THEN NULL ELSE SUBSTR(`dnr_addr`,1,30) END AS `street_address`,
    (CASE WHEN LENGTH(`dnr_addr`) < 30 THEN NULL ELSE IF(LENGTH(dnr_addr)<60,NULL,SUBSTR(`dnr_addr`,31,30)) END) AS `supplemental_address_1`,
    (CASE WHEN LENGTH(`dnr_addr`) < 60 THEN NULL ELSE IF(LENGTH(dnr_addr)<90,NULL,SUBSTR(`dnr_addr`,61,30)) END) AS `supplemental_address_2`,
    `dnr_post_cd` AS `postal_code`,
    TRIM(UCASE((CASE WHEN (LENGTH(`dnr_addr`) BETWEEN 0 AND 30) THEN SUBSTR(`dnr_addr`,1,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 31 AND 60) THEN SUBSTR(`dnr_addr`,31,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 61 AND 90) THEN SUBSTR(`dnr_addr`,61,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 91 AND 120) THEN SUBSTR(`dnr_addr`,91,30) ELSE NULL END))) AS `city`
FROM `dms_donor` 
WHERE `civ_contact_id` IS NOT NULL
AND (LENGTH(TRIM(`dnr_addr`)) > 0 OR (`dnr_post_cd` > 0))
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_address` WHERE `contact_id` = `civ_contact_id`)
UNION ALL 
SELECT 
    `civ_contact_id` AS `contact_id`,
    1 `location_type_id`,
    0 AS `is_primary`,
    CASE WHEN LENGTH(TRIM(SUBSTR(`add_addr`,1,30))) = 0 THEN NULL ELSE SUBSTR(`add_addr`,1,30) END AS `street_address`,
    (CASE WHEN LENGTH(`add_addr`) < 30 THEN NULL ELSE IF(LENGTH(add_addr)<60,NULL,SUBSTR(`add_addr`,31,30)) END) AS `supplemental_address_1`,
    (CASE WHEN LENGTH(`add_addr`) < 60 THEN NULL ELSE IF(LENGTH(add_addr)<90,NULL,SUBSTR(`add_addr`,61,30)) END) AS `supplemental_address_2`,
    `add_post_cd` AS `postal_code`,
    TRIM(UCASE((CASE WHEN (LENGTH(`add_addr`) BETWEEN 0 AND 30) THEN SUBSTR(`add_addr`,1,30) WHEN (LENGTH(`add_addr`) BETWEEN 31 AND 60) THEN SUBSTR(`add_addr`,31,30) WHEN (LENGTH(`add_addr`) BETWEEN 61 AND 90) THEN SUBSTR(`add_addr`,61,30) WHEN (LENGTH(`add_addr`) BETWEEN 91 AND 120) THEN SUBSTR(`add_addr`,91,30) ELSE NULL END))) AS `city`
FROM `dms_profile`
INNER JOIN `dms_donor` ON add_dnr_no = dnr_no
WHERE `civ_contact_id` IS NOT NULL
AND (LENGTH(TRIM(`add_addr`)) > 0 OR (`add_post_cd` > 0))
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_address` WHERE `contact_id` = civ_contact_id)
UNION ALL 
SELECT 
     `civ_contact_id` AS `contact_id`,
    6 AS `location_type_id`,
    0 `is_primary`,
    CASE WHEN LENGTH(TRIM(SUBSTR(`dnr_addr`,1,30))) = 0 THEN NULL ELSE SUBSTR(`dnr_addr`,1,30) END AS `street_address`,
    (CASE WHEN LENGTH(`dnr_addr`) < 30 THEN NULL ELSE IF(LENGTH(dnr_addr)<60,NULL,SUBSTR(`dnr_addr`,31,30)) END) AS `supplemental_address_1`,
    (CASE WHEN LENGTH(`dnr_addr`) < 60 THEN NULL ELSE IF(LENGTH(dnr_addr)<90,NULL,SUBSTR(`dnr_addr`,61,30)) END) AS `supplemental_address_2`,
    `dnr_post_cd` AS `postal_code`,
    TRIM(UCASE((CASE WHEN (LENGTH(`dnr_addr`) BETWEEN 0 AND 30) THEN SUBSTR(`dnr_addr`,1,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 31 AND 60) THEN SUBSTR(`dnr_addr`,31,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 61 AND 90) THEN SUBSTR(`dnr_addr`,61,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 91 AND 120) THEN SUBSTR(`dnr_addr`,91,30) ELSE NULL END))) AS `city`
FROM `dms_donor` 
WHERE `civ_contact_id` IS NOT NULL  AND dnr_sower = 'Y'
AND (LENGTH(TRIM(`dnr_addr`)) > 0 OR (`dnr_post_cd` > 0))
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_address` WHERE `contact_id` = `civ_contact_id`)";

/*$sql = "SELECT 
     `civ_contact_id` AS `contact_id`,
    6 AS `location_type_id`,
    0 AS `is_primary`,
    CASE WHEN LENGTH(TRIM(SUBSTR(`dnr_addr`,1,30))) = 0 THEN NULL ELSE SUBSTR(`dnr_addr`,1,30) END AS `street_address`,
    (CASE WHEN LENGTH(`dnr_addr`) < 30 THEN NULL ELSE IF(LENGTH(dnr_addr)<60,NULL,SUBSTR(`dnr_addr`,31,30)) END) AS `supplemental_address_1`,
    (CASE WHEN LENGTH(`dnr_addr`) < 60 THEN NULL ELSE IF(LENGTH(dnr_addr)<90,NULL,SUBSTR(`dnr_addr`,61,30)) END) AS `supplemental_address_2`,
    `dnr_post_cd` AS `postal_code`,
    TRIM(UCASE((CASE WHEN (LENGTH(`dnr_addr`) BETWEEN 0 AND 30) THEN SUBSTR(`dnr_addr`,1,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 31 AND 60) THEN SUBSTR(`dnr_addr`,31,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 61 AND 90) THEN SUBSTR(`dnr_addr`,61,30) WHEN (LENGTH(`dnr_addr`) BETWEEN 91 AND 120) THEN SUBSTR(`dnr_addr`,91,30) ELSE NULL END))) AS `city`
FROM `dms_donor` 
WHERE `civ_contact_id` IS NOT NULL
AND (LENGTH(TRIM(`dnr_addr`)) > 0 OR (`dnr_post_cd` > 0))
AND NOT EXISTS (SELECT id FROM `civicrm`.`civicrm_address` WHERE `contact_id` = civ_contact_id AND location_type_id = 6)
AND EXISTS (SELECT id FROM `civicrm`.`civicrm_group_contact` WHERE `contact_id` = civ_contact_id AND `status` = 'Added')
AND dnr_sower = 'Y'";*/

$cntInserted = 0;
$sdate = date("Y-m-d H:i:s");

# START IMPORT PROCESS
$output = '';

# GET NEW DONORS
$addresses = $db->select($sql);
if (empty($addresses)) {
    #   IF THERE ARE NO NEW RECORDS EXIT THE SCRIPT.   
    $output .= date("Y-m-d H:i:s") . '<br />All addresses imported !';
} else {
    # START THE IMPORT
    foreach ($addresses as $k=>$v) {
        try {
            foreach ($v as $parameter=>$value) $insertParams[$parameter] = $value;
            $insertParams['version'] = 3;
            $result = civicrm_api('Address', 'create', $insertParams);
            if ($result['is_error']==0) $cntInserted++;
        } catch (Exception $e) {
            $output .= "<pre />";
            $output .= "New Error - Donor #" .$v['trxn_id'] . "\n";
            $output .= print_r($e,true);
            $output .= "end error - Donor #" .$v['trxn_id'];
        }
    }
    $output .= 'Records retrieved from DMS: ' . count($addresses);
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
file_put_contents('logs/donorAddressImportLog.htm',$output,FILE_APPEND | LOCK_EX);

#   SHOW RESULTS
echo $output;