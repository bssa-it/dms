<?php 

/**
 * @description
 * This file saves a new contact
 * 
 * @author      Chezre Fredericks
 * @date_created 22/06/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

$b = new civicrm_dms_batch_header_extension();
foreach ($_POST as $k=>$v) $b->$k = $v;
$b->batch_status = 'open';
$b->created_datetime = date("Y-m-d H:i:s");
$b->created_by = $_SESSION['dms_user']['civ_contact_id'];
$b->office_id = $_SESSION['dms_user']['office_id'];
$b->Save();
$jsonReturn['result'][] = array('request'=>'civicrm_dms_batch_header_extension::Save()','result'=>$b);
$jsonReturn['message'] = "Batch created";
$jsonReturn['batch_id'] = (!empty($b->id)) ? $b->id:0;
print json_encode($jsonReturn);