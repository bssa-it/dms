<?php 

/**
 * @description
 * This script deletes a contact's membership from CiviCRM 
 * via the API
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

# FIRST UPDATE CONTACT RECORD WITH DETAILS OF WHO EDITED THIS CONTACT
$getMembership = civicrm_api('Membership','getsingle',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$getMembership);

$contactLastUpdate['version'] = '3';
$contactLastUpdate['id'] = $getMembership['contact_id'];
$contactLastUpdate['modified_date'] = date("Y-m-d H:i:s");
$contactLastUpdate['custom_20'] = $_SESSION['dms_user']['civ_contact_id'];
$update = civicrm_api('Contact','Create',$contactLastUpdate);
$jsonReturn['result'][] = array('request'=>$contactLastUpdate,'result'=>$update);

# NOW DELETE MEMBERSHIP
$result = civicrm_api('Membership','delete',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

$jsonReturn['message'] = "Membership deleted.";
echo json_encode($jsonReturn);