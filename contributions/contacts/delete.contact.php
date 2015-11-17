<?php

/**
 * @description
 * Script to delete a contact
 * 
 * @author      Chezre Fredericks
 * @date_created 22/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (empty($_GET['c'])&&empty($_POST['contact_id'])) {
    header('location:/dms/index.php');
    exit();
}

$jsonReturn['post'] = (empty($_POST)) ? array():$_POST;
$jsonReturn['get'] = (empty($_GET)) ? array():$_GET;
$contactId = (empty($_POST['contact_id'])) ? $_GET['c']:$_POST['contact_id'];

#   BOOTSTRAP
include("../inc/globals.php");
if (!empty($_SESSION['dms_user']['authorisation'])&&$_SESSION['dms_user']['authorisation']->isSuperUser) {
    $email = "DELETE FROM civicrm_email WHERE contact_id = $contactId";
    $result = $GLOBALS['civiDb']->execute($email);
    $jsonReturn['result'][] = array('request'=>$email,'result'=>$result);
    $address = "DELETE FROM civicrm_address WHERE contact_id = $contactId"; 
    $result = $GLOBALS['civiDb']->execute($address);
    $jsonReturn['result'][] = array('request'=>$address,'result'=>$result);
    $phones = "DELETE FROM civicrm_phone WHERE contact_id = $contactId";
    $result = $GLOBALS['civiDb']->execute($phones);
    $jsonReturn['result'][] = array('request'=>$phones,'result'=>$result);
    $groups = "DELETE FROM civicrm_group_contact WHERE contact_id = $contactId";
    $result = $GLOBALS['civiDb']->execute($groups);
    $jsonReturn['result'][] = array('request'=>$groups,'result'=>$result);

    $sql = "SELECT id FROM civicrm_contribution WHERE contact_id = $contactId";
    $trxns = $GLOBALS['civiDb']->select($sql);
    if (!empty($trxns)) {
        foreach ($trxns as $t) {
            $t['version'] = 3;
            $result = civicrm_api('contribution','delete',$t);
            $jsonReturn['result'][] = array('request'=>$t,'result'=>$result);
        }
    }

    $params['version'] = 3;
    $params['contact_id'] = $contactId;
    $params['skip_undelete'] = 1;
    $result = civicrm_api('Contact', 'delete', $params);
    $jsonReturn['result'][] = array('request'=>$params,'result'=>$result);

    $jsonReturn['message'] = (!empty($result['error_message'])) ?  $result['error_message'] : "Contact has been deleted.";
    echo json_encode($jsonReturn);
} else {
    $jsonReturn['message'] = "You are not authorised to delete a contact.";
    echo json_encode($jsonReturn);
}