<?php 

/**
 * @description
 * This script removes a phone number, email address or postal address from 
 * CiviCRM via the API
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

$apiParams['version'] = 3;
$apiParams['id'] = $_POST['ref'];
$result = civicrm_api($_POST['entity'],'delete',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
$jsonReturn['message'] = "Contact Detail Deleted";
echo json_encode($jsonReturn);