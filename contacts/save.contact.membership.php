<?php 

/**
 * @description
 * This script saves a contact's memberships.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$apiParams = $_POST;
$apiParams['version'] = 3;
$apiParams['domain_id'] = '1';
if (empty($apiParams['id'])) $apiParams['join_date'] = date("Y-m-d");
if (!empty($_POST['is_override'])) $apiParams['is_override'] = '1';
if (!empty($_POST['upd_contact_id'])) unset($apiParams['upd_contact_id']);
$jsonReturn['post'] = $_POST;

$result = civicrm_api('Membership','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);


$cId = (!empty($_POST['contact_id'])) ? $_POST['contact_id']:$_POST['upd_contact_id'];
#   Log contact last modified
$GLOBALS['functions']->logContactChange($cId);
$jsonReturn['message'] = "Contact memberships updated";
echo json_encode($jsonReturn);