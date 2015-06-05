<?php

/**
 * @description
 * This script adds a contact to a group.
 * 
 * @author      Chezre Fredericks
 * @date_created 11/02/2015
 * @Changes
 * 4.1.1 - 11/03/2015 - Chezre Fredericks:
 * Add sower address if contact is added to the sower group
 * 
 * 5.0.1 - 10/04/2015 - Chezre Fredericks:
 * Added E-SOWER PROCEDURE
 */
 
#   SKELETON BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

# E-SOWER PROCEDURE
if (!empty($_POST['email'])) {
    $parms['version'] = 3;
    $parms['contact_id'] = $_POST['contact_id'];
    $parms['email'] = $_POST['email'];
    $result = civicrm_api('email','get',$parms);
    $jsonReturn['result'][] = array('request'=>$parms,'result'=>$result);
    if ($result['count']==0) {
        $result = civicrm_api('email','create',$parms);
        $jsonReturn['result'][] = array('request'=>$parms,'result'=>$result);
    }
    
    $mcAdminContactId = (string)$GLOBALS['xmlConfig']->emailconfig->mailchimpAdministrator['contact_id'];
    $mcAdmin = $GLOBALS['functions']->getCiviContact($mcAdminContactId);
    if (!empty($mcAdmin['email'])) {
        $toAddress = $mcAdmin['email'];
        $toName = $mcAdmin['display_name'];
    } else {
        $toAddress = (string)$GLOBALS['xmlConfig']->emailconfig->address;
        $toName = (string)$GLOBALS['xmlConfig']->emailconfig->name;
    }
    
    $group = '';
    foreach ($GLOBALS['xmlConfig']->sower->mailchimpLists->list as $mcl) { 
        if (!empty($group)) continue;
        if ((int)$_POST['group_id']==(int)$mcl['groupId']) $group = $mcl['desc'];
    }
    $mandrill = new dmsMandrill(); 
    $message['subject'] = 'Add Contact to ' . $group . ' mailing list';
    $patterns = array('/##group##/','/##toname##/','/##fromname##/','/##contactname##/','/##contactemail##/');
    $replacements = array($group,$toName,$_SESSION['dms_user']['fullname'],$_POST['display_name'],$_POST['email']);
    $message['html'] = preg_replace($patterns, $replacements, file_get_contents('/var/www/joomla/dms/email/esower.html'));
    $message['to'] = array(array('email' => $toAddress, 'name' => $toName));
    $message['from_email'] = $_SESSION['dms_user']['username'].(string)$GLOBALS['xmlConfig']->emailconfig->domain;
    $message['from_name'] = $_SESSION['dms_user']['fullname'];
    $mandrill->messages->send($message);
    
    $jsonReturn['message'] = "Request has been sent.";
    echo json_encode($jsonReturn);
    exit();
}

#  IF NOT SOWER EMAIL ADDDRESS
$group['version'] = 3;
$group = array_merge($group,$_POST);
$result = civicrm_api('GroupContact', 'create', $group);

$jsonReturn['result'][] = array('request'=>$group,'result'=>$result);

#   Add Sower address if in the sower group
if ((int)$_POST['group_id']===(int)$GLOBALS['xmlConfig']->civiGroups->sower) {
    $result = $GLOBALS['functions']->addSowerAddress($_POST['contact_id']);
    $jsonReturn['result'][] = array('request'=>"\$GLOBALS['functions']->addSowerAddress(\$_POST['contact_id'])",'result'=>$result);
} 

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
echo json_encode($jsonReturn);