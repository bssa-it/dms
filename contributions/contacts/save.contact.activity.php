<?php 

/**
 * @description
 * This file saves a note into the CiviCRM database for a contact.
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
$apiParams['source_contact_id'] = $_SESSION['dms_user']['civ_contact_id'];
$apiParams['activity_type_id'] = $_POST['activity_type_id'];
$apiParams['custom_21'] = date("Y-m-d H:i:s");
$idCode = 'id-'.$_POST['activity_type_id'];

if (!empty($_POST[$idCode])) {
    $apiParams['id'] = $_POST[$idCode];
    $GLOBALS['functions']->removeCiviActivityContacts($_POST[$idCode]);
}

switch ($apiParams['activity_type_id']) {
    case 1:
    case 49:
        #   MEETING
        $apiParams['activity_date_time'] = $_POST['activity_date_time'];
        $apiParams['location'] = $_POST['location'];
        $apiParams['subject'] = $_POST['subject'];
        $apiParams['details'] = $_POST['details'];
        if (!empty($_POST['status_id'])) $apiParams['status_id'] = $_POST['status_id'];
        break;
    case 2:
        #   PHONE CALL
        $apiParams['details'] = $_POST['details_phone'];
        $apiParams['activity_date_time'] = $_POST['activity_date_time_phone'];
        $apiParams['phone_id'] = $_POST['phone_id'];
        if (!empty($_POST['phone_no'])) {
            $apiParams['phone_no'] = $_POST['phone_no'];
            $apiParams['phone_id'] = $GLOBALS['functions']->insertCiviContactPhoneNo($_POST['contact_id'],$_POST['phone_no']);
            $apiParams['subject'] = $_POST['phone_no'];   
        } else {
            $phoneNos = $GLOBALS['functions']->getCiviContactPhoneNos($_POST['contact_id']);
            foreach ($phoneNos as $p) {
                if ($p['id']==$_POST['phone_id']) {
                    $apiParams['subject'] = $p['phone'];
                    break;
                }
            }
        }
        if (!empty($_POST['status_id_phone'])) $apiParams['status_id'] = $_POST['status_id_phone'];
        break;
    case 3:
        #   EMAIL 
        $apiParams['details'] = $_POST['details_email'];
        $apiParams['activity_date_time'] = $_POST['activity_date_time_email'];
        if (!empty($_POST['email_address'])) {
            $GLOBALS['functions']->insertCiviContactEmail($_POST['contact_id'],$_POST['email_address']);
            $apiParams['subject'] = $_POST['email_address'];   
        } else {
            $emails = $GLOBALS['functions']->getCiviContactEmailAddresses($_POST['contact_id']);
            foreach ($emails as $e) {
                if ($e['id']==$_POST['email_id']) {
                    $apiParams['subject'] = $e['emailAddress'];
                    break;
                }
            }
        }
        if (!empty($_POST['status_id_email'])) $apiParams['status_id'] = $_POST['status_id_email'];
        break;
}

#   STATUS ID
if (empty($apiParams['status_id'])) {
    $today = new DateTime();
    $activityDateTime = new DateTime($apiParams['activity_date_time']);
    $apiParams['status_id'] = ($activityDateTime>$today) ? 1:2;
}

$result = civicrm_api('Activity','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

#  Attach contact information to activity
if (!empty($result['id'])) {
    $jsonReturn['message'] = "Activity Saved";
    $recordTypes = $GLOBALS['xmlConfig']->activityconfig->civiActivityRecordTypeIds->typeId;
    foreach ($recordTypes as $r) {
        if ($r['desc']=='with_contact_id') $withContactId = $r['value'];
        if ($r['desc']=='assigned_to') $assignedToId = $r['value'];   
    }
    
    if (!empty($_POST['assigned_to'])) {
        foreach ($_POST['assigned_to'] as $as) {
            $c = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($as);
            $GLOBALS['functions']->addCiviActivityContact($result['id'],$c['contact_id'],$assignedToId);   
        }   
    }
    if (!empty($_POST['addZimbraCalendar'])||!empty($_POST['addZimbraTask'])) {
        if (substr($_POST['zimbraCalendarId'],0,1)=='S'||substr($_POST['taskListId'],0,1)=='S') {
            $d = new department;
            $d->Load($_SESSION['dms_user']['config']['impersonate']);
            $GLOBALS['functions']->addCiviActivityContact($result['id'],$d->dep_contact_id,$assignedToId);
        }
    }   
    if (!empty($_POST['with_contact_id'])) {
        foreach ($_POST['with_contact_id'] as $wc) {
            $c = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($wc);
            $GLOBALS['functions']->addCiviActivityContact($result['id'],$c['contact_id'],$withContactId);   
        }
    }
    $jsonReturn['activity_id'] = $result['id'];  
    
    #   Log contact last modified
    $GLOBALS['functions']->logContactChange($_POST['contact_id']);
    echo json_encode($jsonReturn);

}