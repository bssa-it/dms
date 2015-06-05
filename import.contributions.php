<?php

/**
 * @description
 * This script is part of the daily ETL procedure.  It imports the contributions into the CiviCRM database
 * via the CiviCRM API.   The script calls itself again at the end so that it can get through all the unprocessed
 * records.   The exit point from this loop is when there are no records left to process.
 * 
 * @author      Chezre Fredericks
 * @date_created 05/12/2014
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

$session = new CRM_Core_Session;
$session->set('userID',2);

include("inc/class.acknowledgementpreferences.php");
include("inc/class.civicrm_dms_contact_other_data.php");
include("inc/class.functions.php");

$sql = "
SELECT 
	`trns_id`,
	`trns_dnr_no`,
	`civ_financial_type_id` `financial_type_id`,
	`civ_payment_instrument_id` `contribution_payment_instrument_id`,
	`trns_date_received` `receive_date`,
	`trns_amount_received` `total_amount`,
	`civ_trxn_id` `trxn_id`,
	`civ_contribution_status_id` `contribution_status_id`,
	`trns_motivation_id` `motivation_id`,
	`R`.`category_id` `category_id`,
	`O`.`org_region` `region_id`,
	`R`.`organisation_id` `organisation_id`,
	`trns_acknowledgement_date` `thankyou_date`,
	`D`.`civ_contact_id` `contact_id`,
	CASE WHEN civ_contribution_status_id = 7 THEN cty_description ELSE NULL END `cancel_reason`,
	CASE WHEN civ_contribution_status_id = 7 THEN `trns_date_received` ELSE NULL END `cancel_date`,
    `dnr_org_id`
 FROM `dms_transaction` `T`
 INNER JOIN `dms_donor` `D` ON trns_dnr_no = dnr_no
 LEFT JOIN `dms_contributionTypes` ON trns_receipt_type = cty_id
 LEFT JOIN civicrm.civicrm_dms_contact_reporting_code `R` ON contact_id = `D`.civ_contact_id
 LEFT JOIN civicrm.civicrm_dms_organisation `O` ON `R`.`organisation_id` = `O`.`org_id`
 WHERE `civ_contribution_id` IS NULL AND `D`.`civ_contact_id` IS NOT NULL
 AND dms_etl_error_id IS NULL
 ORDER BY receive_date DESC,`civ_trxn_id` LIMIT 500";

$cntRecords = 0;
$cntInserted = 0;
$output = '';
$sdate = date("Y-m-d H:i:s");

#   LOAD 5000 UNPROCESSED RECORDS TO BE IMPORTED THROUGH THE API.
set_time_limit(600);
$contributions = $GLOBALS['db']->select($sql);
if (empty($contributions)) {
    #   NO RECORDS FOUND EXIT THIS SCRIPT
    $output = 'Script completed at: ' . date("Y-m-d H:i:s");
    echo $output;
    file_put_contents('logs/contributionImportLog.htm',$output,FILE_APPEND | LOCK_EX);
    exit();  #  <--- SCRIPT EXIT POINT
}

$exclude = array('trns_id','trns_dnr_no','dnr_org_id','motivation_id','category_id','region_id','organisation_id');
foreach ($contributions as $k=>$v) {
    $insertParams = array();
    $insertParams['version'] = 3;
    foreach ($v as $parameter=>$value) if (!in_array($parameter,$exclude)) $insertParams[$parameter] = $value;
    try {
        
        $result = civicrm_api('Contribution', 'create', $insertParams);
        
        if (!empty($result['is_error'])) {
            if (preg_match('/Duplicate error/',$result['error_message'])) updateTransactionByTrxnid($v['trxn_id']);
        }
        
        if (isset($result['id'])) {
            updateTransaction($v['contact_id'],$result['id'],$v['trns_id']);
            updateLastDate($v['contact_id'],$v['receive_date'],$v['total_amount']);
            
            # civicrm_dms_transaction
            $reportingCodesSql = "INSERT INTO `civicrm_dms_transaction` (`contribution_id`,`motivation_id`,`category_id`,`region_id`,`organisation_id`) ";
            $reportingCodesSql .= "VALUES (" . $result['id'] ."," . $v['motivation_id'] . ", '" .str_pad($v['category_id'],4,'0',STR_PAD_LEFT). "',".$v['region_id'].",'" . $v['organisation_id'] . "');";
            $civiDb->execute($reportingCodesSql);

            # BAM TRANSACTION RECORD
            if (substr($v['dnr_org_id'],0,1)=='9'&&$v['motivation_id']=='1130') {
                $membershipParams['contact_id'] = $v['contact_id'];
                $membershipParams['version'] = 3;
                $alreadyIn = civicrm_api('Membership', 'get', $membershipParams);
                if ($alreadyIn['count']>0) {
                    foreach ($alreadyIn['values'] as $km=>$vm) {
                        if ((string)$xmlConfig->bam->civiMembershipTypeId==$vm['membership_type_id']) {
                            $bamPayment['contribution_id'] = $result['id'];
                            $bamPayment['membership_id'] = $vm['id'];
                            $bamPayment['version'] = 3;
                            $bamPaymentResult = civicrm_api('MembershipPayment','create',$bamPayment);

                            $membershipParams['id'] = $vm['id'];
                            $membershipParams['start_date'] = $v['receive_date'];
                            $membershipParams['skipStatusCal'] = 0;
                            $mResult = civicrm_api('Membership', 'create', $membershipParams);
                        }
                    }
                }
            }

            # ACKNOWLEDGEMENT PREFERENCES
            $a = new acknowledgementpreferences();
            if ($a->LoadByContactId($v['contact_id'])) {
                $mustAcknowledge = ($a->apr_must_acknowledge=='Y');
                $isTime = $a->isTimeForAcknowledgment();
                $isBam = functions::isCiviBamMember($v['contact_id']);

                if ($mustAcknowledge&&$isTime&&!$isBam) {
                    $tsql = "UPDATE `dms_transaction` SET `dms_must_acknowledge` = 'Y' WHERE `trns_id` = " . $v['trns_id'];
                    $GLOBALS['db']->execute($tsql);
                }
                $a->setUnacknowledgedTotal();
            }

            # FINALLY COUNT AS INSERTED
            $cntInserted++;
        }   
    } catch (Exception $e) {
        $output .= "<pre />";
        $output .= "New Error - Donor #" .$v['trxn_id'] . "\n";
        $output .= print_r($e,true);
        $output .= "end error - Donor #" .$v['trxn_id'];
    }
    $cntRecords++;
    
} 

$output .= 'Records retrieved from DMS: ' . $cntRecords;
$output .= '<br />Records inserted into CiviCRM: ' .$cntInserted. '</p>';


#   UPDATE VARIABLES FOR LOG
$edate = date('Y-m-d H:i:s');
$output .= '<p>Start time: ' . $sdate;
$output .= '<br />End Time: ' . $edate;
$interval  = abs(strtotime($edate) - strtotime($sdate));
$minutes   = round($interval / 60,2);
$output .= '<br />Script completed in ' .$minutes. ' minutes</p>';
#   CREATE LOG
file_put_contents('logs/contributionImportLog.htm',$output,FILE_APPEND | LOCK_EX);

#   RESTART THE SCRIPT
header('location:import.contributions.php');

function updateTransaction($contactId,$contributionId,$transId){
    $tsql = "UPDATE `dms_transaction` SET `civ_contact_id` = " . $contactId;
    $tsql .= ", `civ_contribution_id` = " . $contributionId . " WHERE `trns_id` = " . $transId;
    $GLOBALS['db']->execute($tsql);
}

function updateLastDate($contactId,$lastDate,$lastAmount) {
    $l = new civicrm_dms_contact_other_data();
    $l->LoadByContactId($contactId);
    $l->last_contribution_date = $lastDate;
    $l->last_contribution_amount = $lastAmount;
    $l->Save();
}

function updateTransactionByTrxnid($trxnid){
    $sql = "UPDATE dms_transaction 
INNER JOIN civicrm.civicrm_contribution C ON trxn_id = civ_trxn_id
SET `civ_contribution_id` = C.`id`
WHERE `trxn_id` = '$trxnid'";
    $GLOBALS['db']->execute($sql);
}