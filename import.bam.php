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

#   LOAD THE CIVICRM API CONFIG
require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

$session = new CRM_Core_Session;
$session->set('userID',2);

#   GET THE BAM RECORDS FROM THE SYSTEM
$sql = "SELECT dnr_no,`bam_ref_no`,`bam_certificate_printed`,civ_contact_id,bam_join_date FROM dms_donor
LEFT JOIN dms_bam ON bam_dnr_no = dnr_no
WHERE civ_contact_id IS NOT NULL
AND SUBSTR(`dnr_org_id`,1,1) = '9'";

$cntInserted = 0;
$sdate = date("Y-m-d H:i:s");

#   SET TIME LIMIT TO PROTECT AGAINST MEMORY OVERLOAD
set_time_limit(600);
$donors = $db->select($sql);

$output = '';
foreach ($donors as $k=>$v) {
    #   INSERT MEMBERSHIP FOR EACH RECORD.
    $membershipParams = array();
    $refNo = $v['bam_ref_no'];
    $certificatePrinted = $v['bam_certificate_printed'];
    $contactId = $v['civ_contact_id'];
    $joinDate = $v['bam_join_date'];
   
    try { 
        
        $membershipParams['contact_id'] = $contactId;
        $membershipParams['version'] = 3;
        $alreadyIn = civicrm_api('Membership', 'get', $membershipParams);
        
        #   SET MEMBERSHIP ID IF THE MEMBERSHIP ALREADY EXISTS
        if ($alreadyIn['count']>0) {
            foreach ($alreadyIn['values'] as $k=>$v) {
                $continue = ((int)$xmlConfig->bam->civiMembershipTypeId==$v['membership_type_id']);
            }
            if ($continue) continue;
        }
        
        
        #   UPDATE/INSERT MEMBERSHIP
        if (empty($membershipParams['id'])) {
            $membershipParams['join_date'] = $joinDate; 
            $membershipParams['membership_start_date'] = $joinDate;
        }
        
        $membershipParams['membership_type_id'] = (int)$xmlConfig->bam->civiMembershipTypeId;
        $membershipParams['custom_15'] = $refNo;
        $membershipParams['custom_16'] = $certificatePrinted;
        $result = civicrm_api('Membership', 'create', $membershipParams);
        
        if ($result['is_error']===0) {
            $cntInserted++;  
        } else {
            echo '<pre />';
            print_r($result);
        }
    } catch (Exception $e) {
        $output .= 'An error occured processing BAM donor ' .$v['dnr_no'].  ': ' . $e->getMessage();
    }
}
$output .= 'Records retrieved from DMS: ' . count($donors);
$output .= '<br />New BAM records: ' .$cntInserted. '</p>';

#   UPDATE VARIABLES FOR LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';

# SAVE LOG
file_put_contents('logs/bamImportLog.htm',$output,FILE_APPEND | LOCK_EX);

echo $output;