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
$a = new acknowledgementpreferences();
foreach ($_POST as $k=>$v) {
    if ($k=='apr_id') $a->Load($v);
    if (substr($k,0,3)=='apr') {
        $a->$k = $v;
    } else {
        $apiParams[$k] = $v;
    }
}
$apiParams['modified_date'] = date("Y-m-d H:i:s");

if (!empty($_POST['salutation'])) {
    $apiParams['postal_greeting_id'] = 4;
    $apiParams['postal_greeting_display'] = $_POST['salutation'];
    $apiParams['postal_greeting_custom'] = $_POST['salutation']; 
    $apiParams['email_greeting_id'] = 4;
    $apiParams['email_greeting_display'] = $_POST['salutation'];
    $apiParams['email_greeting_custom'] = $_POST['salutation'];
    $apiParams['addressee_id'] = 4;
    $apiParams['addressee_display'] = $_POST['salutation'];
    $apiParams['addressee_custom'] = $_POST['salutation'];  
}

$apiParams['custom_20'] = $_SESSION['dms_user']['civ_contact_id'];
$result = civicrm_api('contact','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

# acknowledgment preferences
$result = $a->Save();
$jsonReturn['result'][] = array('request'=>$a,'result'=>$result);


# dms civi fields
$c = new civicrm_dms_contact_other_data_extension($apiParams['contact_id']);
$c->reminder_month = $_POST['reminder_month'];
$result = $c->Save();
$jsonReturn['result'][] = array('request'=>$c,'result'=>$result);

#   Log contact last modified
$GLOBALS['functions']->logContactChange($apiParams['contact_id']);
$jsonReturn['message'] = "Contact preferences updated";
echo json_encode($jsonReturn);