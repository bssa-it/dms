<?php 

/**
 * @description
 * This file saves the contacts contact numbers to CiviCRM.
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
$apiParams['is_primary'] = (!empty($_POST['is_primary'])) ? '1':'0';
unset($apiParams['phone_is_primary']);
if (empty($_POST['id'])) unset($apiParams['id']);

$result = civicrm_api('Phone','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

if (!empty($_POST['is_primary'])) {
    $parms['version'] = 3;
    $parms['id'] = $_POST['phone_is_primary'];
    $parms['is_primary'] = '0';
    $updateResult = civicrm_api('Phone','create',$parms);
    
    $jsonReturn['result'][] = array('request'=>$parms,'result'=>$updateResult);
     
}

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
$jsonReturn['message'] = "Contact telephone details updated";
echo json_encode($jsonReturn);