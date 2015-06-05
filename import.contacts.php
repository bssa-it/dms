<?php

/**
 * @description
 * This script imports new contacts into the CiviCRM database.
 * 
 * @author      Chezre Fredericks
 * @date_created 02/12/2014
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

#   GET NEW DONORS
$sql = "SELECT
    (CASE WHEN ((`dnr_cntr_tp` > 100) AND (`dnr_cntr_tp` < 7999)) THEN 'Organization' ELSE 'Individual' END) AS `contact_type`, 
	(CASE 
		WHEN ((`dnr_cntr_tp` > 1000) AND (`dnr_cntr_tp` < 1999)) THEN 'Church' 
		WHEN ((`dnr_cntr_tp` > 5000) AND (`dnr_cntr_tp` < 5999)) THEN 'School'
		WHEN ((`dnr_cntr_tp` > 6000) AND (`dnr_cntr_tp` < 6999)) THEN 'Business'  
		ELSE NULL
	END) AS `contact_sub_type`,
	`dnr_no` AS `external_identifier`,
	TRIM(CONCAT(`dnr_name`,', ',`dnr_inits`)) AS `sort_name`,
	CASE WHEN TRIM(CONCAT(`dnr_title`,' ',`dnr_name`)) = '' THEN 'Unknown' ELSE TRIM(CONCAT(`dnr_title`,' ',`dnr_name`)) END AS `display_name`,
	`dms_preferred_communication_type_id` AS `preferred_communication_method`,
	(CASE WHEN (`dnr_lang` = 'A') THEN 'af_ZA' ELSE 'en_ZA' END) AS `preferred_language`,
	'Both' AS `peferred_mail_format`,
	TRIM(`dnr_inits`) AS `first_name`,
	TRIM(`dnr_name`) AS `last_name`,
		CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN '1' ELSE '4' END AS `email_greeting_id`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN NULL ELSE TRIM(`dnr_salut`) END AS `email_greeting_custom`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN NULL ELSE TRIM(`dnr_salut`) END AS `email_greeting_display`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN '1' ELSE '4' END AS `postal_greeting_id`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN NULL ELSE TRIM(`dnr_salut`) END AS `postal_greeting_custom`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN NULL ELSE TRIM(`dnr_salut`) END AS `postal_greeting_display`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN '1' ELSE '4' END AS `addressee_id`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN NULL ELSE TRIM(`dnr_salut`) END AS `addressee_custom`,
	CASE WHEN ISNULL(dnr_salut) OR  LENGTH(TRIM(`dnr_salut`)) = 0 THEN NULL ELSE TRIM(`dnr_salut`) END AS `addressee_display`,
	'0' AS `is_deceased`,
	(CASE WHEN (`dnr_deleted` = 'N') THEN 0 ELSE 1 END) AS `is_deleted`,
	(CASE WHEN ((`dnr_cntr_tp` > 100) AND (`dnr_cntr_tp` < 7999)) THEN CASE WHEN TRIM(CONCAT(`dnr_title`,' ',`dnr_name`)) = '' THEN 'Unknown' ELSE TRIM(CONCAT(`dnr_title`,' ',`dnr_name`)) END ELSE NULL END) AS `organization_name`,
	`rep_org_id` AS `rep_org_id`,
	`dnr_cntr_tp` AS `dnr_cntr_tp`,
	`dnr_thank` AS `dnr_thank`,
	`dnr_id` AS `dnr_id`,
	`dnr_title` AS `title`,
        `dnr_title` AS `custom_12`,
	`dnr_remnd_mnth` AS `dnr_remnd_mnth`,
	`dnr_tax_certf` AS `custom_14`,
	CASE WHEN dnr_birth_date = '0000-00-00' THEN NULL ELSE dnr_birth_date END AS `birth_date`,
        `civ_contact_id` as `contact_id`
FROM `dms_donor` 
WHERE 
    (`dnr_tax_certf` <> 'Z' AND `civ_contact_id` IS NULL) 
    OR 
    (`civ_contact_id` IS NOT NULL AND civ_last_update IS NULL)
LIMIT 500";

/* 24-02-2015
 * WHERE ((`dnr_tax_certf` <> 'Z') AND ((`dnr_last_date` > '1999-10-31') OR (`dnr_last_date` = '0000-00-00'))) 
    AND (`civ_contact_id` IS NULL
    OR (`civ_contact_id` IS NOT NULL AND civ_last_update IS NULL)) 
 */

$cntDonors = 0;
$cntInserted = 0;
$sdate = date("Y-m-d H:i:s");

    #   SET TIME LIMIT TO PROTECT AGAINST MEMORY OVERLOAD
    set_time_limit(600);
    
    # GET NEW DONORS
    $donors = $db->select($sql);
    if (empty($donors)) {
        #   IF THERE ARE NO NEW RECORDS EXIT THE SCRIPT.   
        $output = date("Y-m-d H:i:s") . '<br />All donors imported !';
        file_put_contents('logs/donorImportLog.htm',$output,FILE_APPEND | LOCK_EX);
        echo $output;
        exit();
    } else {
        
        # START THE IMPORT
        $output = '';
        foreach ($donors as $k=>$v) { 
            try {
                $contactId = '';
                $insertParams = array();
                $insertParams['version'] = 3;
                foreach ($v as $parameter=>$value) $insertParams[$parameter] = $value;
                if (!isDateOk($v['birth_date'])) $insertParams['birth_date'] = null;
                $prefixId = getPrefixId($insertParams['title'],$civiDb);
                if (!empty($prefixId)) $insertParams['prefix_id'] = $prefixId;
                $result = civicrm_api('Contact', 'create', $insertParams);
                
                if ($result['is_error']===1) {
                    $output .= "<pre />";
                    $output .= "New Error - Donor #" .$v['external_identifier'] . "\n";
                    $output .= print_r($result,true);
                    $output .= "end error - Donor #" .$v['external_identifier'];
                } else {
                    # UPDATE THE dms DATABASE WITH THE CONTACT_ID
                    if (isset($result['id'])) {
                        $contactId = ", `civ_contact_id` = " . $result['id'];
                        $cntInserted++;
                        
                        # Rule for department A only
                        //$frequency = (substr($v['rep_org_id'],0,1)=='A') ? '3':'0';
                        $frequency = '0';
                        $method = ($v['preferred_communication_method']==2) ? 'Email':'Postal Mail';
                        
                        $iSql = "INSERT INTO `dms_acknowledgementPreferences` (`apr_contact_id`,`apr_dnr_no`,`apr_must_acknowledge`,";
                        $iSql .= "`apr_frequency`,`apr_preferred_method`) VALUES (".$result['id'].",".$v['external_identifier'];
                        $iSql .= ",'".$v['dnr_thank']."',$frequency,'$method');";
                        $db->execute($iSql);
                                            
                        $reportingCodesSql = "INSERT INTO `civicrm_dms_contact_reporting_code` (`contact_id`,`organisation_id`,`category_id`) ";
                        $reportingCodesSql .= "VALUES (" . $result['id'] .",'" . $v['rep_org_id'] . "','" .str_pad($v['dnr_cntr_tp'],4,'0',STR_PAD_LEFT). "');";
                        $civiDb->execute($reportingCodesSql);
                        
                        $doNotThank = ($v['dnr_thank']=='Y') ? 0:1;
                        $otherDataSql = "INSERT INTO `civicrm_dms_contact_other_data` (`contact_id`,`do_not_thank`,`reminder_month`,`id_number`) ";
                        $otherDataSql .= "VALUES (" . $result['id'] .", $doNotThank, " .$v['dnr_remnd_mnth']. ",'" .$v['dnr_id']. "');"; 
                        $civiDb->execute($otherDataSql);
                    }
                }
                
                updateDMSDonor($v['external_identifier'],$contactId,$db);
                
                $cntDonors++;
            } catch (Exception $e) {
                if (trim($e->getMessage())=='DB Error: already exists') {
                    $existingParams['external_identifier'] = $v['external_identifier'];
                    $existingResult = civicrm_api3('Contact','getsingle',$existingParams);
                    $contactId = ", `civ_contact_id` = " . $existingResult['id'];
                    updateDMSDonor($v['external_identifier'],$contactId,$db);
                } else {
                    $output .= "<pre />";
                    $output .= "New Error - Donor #" .$v['external_identifier'] . "\n";
                    $output .= print_r($e,true);
                    $output .= "end error - Donor #" .$v['external_identifier'];
                }
            }
        }
        $output .= '<p>Records retrieved from DMS: ' . $cntDonors;
        $output .= '<br />Records updated or inserted into CiviCRM: ' .$cntInserted. '</p>';
    }


#   UPDATE THE VARIABLES KEPT FOR THE LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';

#   INSERT THE NEW LOG
file_put_contents('logs/donorImportLog.htm',$output,FILE_APPEND | LOCK_EX);

#   SHOW RESULTS
header("location: import.contacts.php");

####  functions for this procedure
function isDateOk($d) {
    $date_format = 'Y-m-d';
    
    $input = trim($d);
    $time = strtotime($input);
    
    $is_valid = date($date_format, $time) == $input;
    
    return $is_valid;
}

function updateDMSDonor($donorNo,$cId,$connection){
    $dSql = "UPDATE `dms_donor` SET `civ_last_update` = '" . date("Y-m-d H:i:s") . "'". $cId;
    $dSql .= " WHERE `dnr_no` = " . $donorNo;
    $connection->execute($dSql);
}

function getPrefixId($title,$connection) {
    $title = trim($title);
    $sql = 'SELECT `value` `id` FROM `civicrm_option_value` WHERE TRIM(`label`) = "'.$title.'" OR TRIM(`name`) = "'.$title.'";';
    $result = $connection->select($sql);
    if (!$result) {
        return null;
    } else {
        return $result[0]['id'];
    }
}