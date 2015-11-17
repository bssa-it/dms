<?php 

/**
 * @description
 * This script saves the contact's relationships
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

if ($_POST['contact_left_dnr_no']==$_POST['contact_right_dnr_no']) exit();
$apiParams['version'] = 3;

# EDITING A RELATIONSHIP MEANS DELETING OLD RELATIONSHIP AND CREATING A NEW ONE
if (!empty($_POST['id'])) { 
    $apiParams['id'] = $_POST['id'];
    $result = civicrm_api('relationship','delete',$apiParams);
    unset($apiParams['id']);
} 

$contactIds['contact_id_left'] = $_POST['contact_id_left'];
if (empty($_POST['contact_id_right'])) {
    $contactRight = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($_POST['contact_right_dnr_no']);
    $contactIds['contact_id_right'] = $contactRight['contact_id'];
} else {
    $contactIds['contact_id_right'] = $_POST['contact_id_right']; 
}

$relationshipElements = explode('-',$_POST['relationship_description']);
$relationship['contact_id_a'] = $contactIds['contact_id_'.$relationshipElements[0]];
$relationship['contact_id_b'] = $contactIds['contact_id_'.$relationshipElements[1]];
$relationship['relationship_type_id'] = $relationshipElements[2];
$relationship['start_date'] = date('Y-m-d');
$relationship['is_active'] = '1';

#  CHECK FOR DUPLICATE RELATIONSHIP
$duplicateCheck['contact_id_a'] = $contactIds['contact_id_'.$relationshipElements[1]];
$duplicateCheck['contact_id_b'] = $contactIds['contact_id_'.$relationshipElements[0]];
$duplicateCheck['relationship_type_id'] = $relationshipElements[2];
$duplicateCheck['is_active'] = '1';

$dParams['version'] = '3';
$dParams = array_merge($dParams,$duplicateCheck);
$dResult = civicrm_api('Relationship','get',$dParams);
if ($dResult['count']>0) exit();

$dParams = array_merge($dParams,$relationship);
unset($dParams['start_date']);
$dResult = civicrm_api('Relationship','get',$dParams);
if ($dResult['count']>0) exit();

#   NO DUPLICATES FOUND, CONTINUE TO INSERT THE RELATIONSHIP
$apiParams = array_merge($apiParams,$relationship); 
$result = civicrm_api('relationship','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id_left']);
$jsonReturn['message'] = "Contact relationships updated";
echo json_encode($jsonReturn);