<?php 

/**
 * @description
 * This script changes the contact's primary detail.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

# fetch record
$apiParams['version'] = 3;
$apiParams = array_merge($apiParams,$_POST);
unset($apiParams['entity']);
$result = civicrm_api($_POST['entity'],'get',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

# Make it the primary
$newRecord['version'] = 3;
$newRecord = array_merge($newRecord,$result['values'][$result['id']]);
$newRecord['is_primary'] = 1;
$saveResult = civicrm_api($_POST['entity'],'create',$newRecord);
$jsonReturn['result'][] = array('request'=>$newRecord,'result'=>$saveResult);

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
$jsonReturn['message'] = "Contact primary detail updated";
echo json_encode($jsonReturn);