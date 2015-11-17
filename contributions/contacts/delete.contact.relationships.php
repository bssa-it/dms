<?php 

/**
 * @description
 * This deletes a contact's relationship to another contact
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

$apiParams['version'] = '3';
$apiParams['id'] = $_POST['id'];
$result = civicrm_api('relationship','delete',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

$GLOBALS['functions']->logContactChange($_POST['rightContact']);
$GLOBALS['functions']->logContactChange($_POST['contact']);
$jsonReturn['message'] = "Contact relationship deleted";
echo json_encode($jsonReturn);