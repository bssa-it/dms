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

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

echo "<pre />";
if (is_array($_POST['id'])) {
    $limit = count($_POST['id'])-1;
    foreach(range(0,$limit) as $i) {
        $re = new civicrm_dms_receipt_entry_extension($_POST['id'][$i]);
        $re->receipt_id = $_POST['receipt_id'];
        $re->received_datetime = date("Y-m-d H:i:s",strtotime($_POST['received_datetime'][$i]));
        $re->received_by = $_SESSION['dms_user']['civ_contact_id'];
        $re->receipt_amount = $_POST['allocated_amount'][$i];
        $re->contact_id = $_POST['contact_id'][$i];
        $re->motivation_id = $_POST['motivation'][$i];
        $re->Save();
        print_r($re);
    }
}
exit();




/*
$b->receipt_status = 'open';
$b->created_datetime = date("Y-m-d H:i:s");
$b->created_by = $_SESSION['dms_user']['civ_contact_id'];
$b->office_id = $_SESSION['dms_user']['office_id'];
$b->Save();
*/



$jsonReturn['result'][] = array('request'=>'civicrm_dms_receipt_header_extension::Save()','result'=>$b);
$jsonReturn['message'] = "Batch created";
$jsonReturn['receipt_id'] = (!empty($b->id)) ? $b->id:0;

print json_encode($jsonReturn);