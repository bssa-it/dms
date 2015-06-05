<?php 

/**
 * @description
 * This script saves the activity's status
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

if (!empty($_POST['quickClose'])) {  
    $apiParams['version'] = 3;
    $apiParams['id'] = $_POST['a'];
    $apiParams['status_id'] = 2;
    $result = civicrm_api('Activity','create',$apiParams);    
    exit();
}

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

$apiParams['version'] = 3;
$apiParams['id'] = $_POST['id'];
$apiParams['status_id'] = $_POST['value'];
$result = civicrm_api('Activity','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['upd_contact_id']);
$jsonReturn['message'] = "Activity status updated";
echo json_encode($jsonReturn);