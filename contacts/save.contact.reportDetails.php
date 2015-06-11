<?php 

/**
 * @description
 * This script saves the contact's report details.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

$apiParams['version'] = 3;
$apiParams = array_merge($apiParams,$_POST);
$result = civicrm_api('Contact','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

$c = new civicrm_dms_contact_reporting_code_extension($_POST['contact_id']);
$c->organisation_id = $_POST['organisation_id'];
$c->category_id = str_pad($_POST['category_id'], 4,'0',STR_PAD_LEFT);
$result = $c->Save();
$jsonReturn['result'][] = array('request'=>$c,'result'=>$result);

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
$jsonReturn['message'] = "Report details updated";
echo json_encode($jsonReturn);