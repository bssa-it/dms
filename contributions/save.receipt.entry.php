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
include("inc/class.contribution.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

if (is_array($_POST['id'])) {
    $limit = count($_POST['id'])-1;
    foreach(range(0,$limit) as $i) {
        $re = new contribution($_POST['id'][$i]);
        $re->receipt_id = $_POST['receipt_id'];
        $re->received_datetime = date("Y-m-d H:i:s",strtotime($_POST['received_datetime'][$i]));
        $re->received_by = $_SESSION['dms_user']['civ_contact_id'];
        $re->receipt_amount = $_POST['allocated_amount'][$i];
        $re->contact_id = $_POST['contact_id'][$i];
        $re->motivation_id = $_POST['motivation'][$i];
        $re->is_deleted = $_POST['is_deleted'][$i];
        $re->receipt_no = (!empty($_POST['receipt_no'][$i])) ? $_POST['receipt_no'][$i]:'';
        $re->Save();
        
        $jsonReturn['result'][] = array('request'=>'contribution::Save()','result'=>$re);
    }
}

$jsonReturn['message'] = "Entries Added";
print json_encode($jsonReturn);