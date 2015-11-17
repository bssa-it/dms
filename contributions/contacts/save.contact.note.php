<?php 

/**
 * @description
 * This file saves a note into the CiviCRM database for a contact.
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
$apiParams['entity_table'] = 'civicrm_contact';
$apiParams['entity_id'] = $_POST['note_contact_id'];
$apiParams['contact_id'] = $_SESSION['dms_user']['civ_contact_id'];
$apiParams['subject'] = $_POST['note_subject'];
$apiParams['note'] = $_POST['note_value'];

$result = civicrm_api('Note','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

$jsonReturn['message'] = "Contact note updated";
echo json_encode($jsonReturn);