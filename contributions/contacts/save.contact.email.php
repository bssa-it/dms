<?php 

/**
 * @description
 * This file saves a email for a contact.
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
unset($apiParams['email_is_primary']);
if (empty($_POST['id'])) unset($apiParams['id']);

$result = civicrm_api('Email','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

if (!empty($_POST['is_primary'])) {
    $parms['version'] = 3;
    $parms['id'] = $_POST['email_is_primary'];
    $parms['is_primary'] = '0';
    $updateResult = civicrm_api('Email','create',$parms);
    $jsonReturn['result'][] = array('request'=>$parms,'result'=>$updateResult);
}

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
$jsonReturn['message'] = "Email address saved";
echo json_encode($jsonReturn);