<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include("inc/class.mailchimp.php");
include("inc/class.functions.php");
include("inc/class.db.php");
include("inc/class.contact.php");
$GLOBALS['functions'] = new functions();
$GLOBALS['xmlConfig'] = simplexml_load_file('inc/config.xml');
$connDetails = $GLOBALS['functions']->xml2array($GLOBALS['xmlConfig']->civiConnection);
$GLOBALS['civiDb'] = new database($connDetails);

require_once (string)$GLOBALS['xmlConfig']->civicrmApi->crmConfigFile;
require_once (string)$GLOBALS['xmlConfig']->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

$session = new CRM_Core_Session;
$session->set('userID',2);

file_put_contents('logs/mailchimp.log.htm',date('Y-m-d H:i:s').'<br />');
$mc = new dmsMailchimp();
$output = '';
$allLists = $mc->lists->getList();
foreach ($GLOBALS['xmlConfig']->sower->mailchimpLists->list as $mcl) {
    $total = 0;
    $loop = 0;
    foreach ($allLists['data'] as $l) if ((string)$mcl['id']==$l['id']) $listMemberTotal = $l['stats']['member_count'];
    deleteContactsFromGroup((int)$mcl['groupId']);
    $pageNo = 0;
    $limit = 100;
    $pages = floor($listMemberTotal/$limit);
    foreach (range($pageNo,$pages) as $p) {
        $members = $mc->lists->members((string)$mcl['id'],'subscribed',array("start"=>$p,"limit"=>$limit));
        $total += checkBatch($members,(int)$mcl['groupId']);
        $pg = $p +1;
        file_put_contents('logs/mailchimp.log.htm', $total . ' records so far (pg '.$pg.')<br />',FILE_APPEND);
        $loop++;
    }
    $output .= $total . " records inserted, group id " . $mcl['groupId'] . ', loops: ' . $loop. '<br />';
    file_put_contents('logs/mailchimp.log.htm', $total . " records inserted, group id " . $mcl['groupId'] . ', loops: ' . $loop. '<br />',FILE_APPEND);
}
echo $output;

function deleteContactsFromGroup($groupId){
    $sql = "DELETE FROM civicrm_group_contact WHERE group_id = $groupId;";
    $delete = $GLOBALS['civiDb']->execute($sql);
    $sql = "DELETE FROM civicrm_subscription_history WHERE group_id = $groupId;";
    $delete = $GLOBALS['civiDb']->execute($sql);
}

function checkBatch($members,$sowerGroupId) {
    $cnt = 0;
    if (!empty($members['data'])) {
        foreach ($members['data'] as $m) {
            $emailRecord = emailExists($m['email']);
            if (!$emailRecord) $emailRecord = addContact($m['merges']['FNAME'], $m['merges']['LNAME'], $m['email']);
            
            if (!empty($emailRecord)) {
                $contactId = $emailRecord['contact_id'];
                $emailId = $emailRecord['id'];
                if ($contactId>0) {
                    addContactToGroup($contactId, $sowerGroupId,$emailId);
                    $cnt++;
                }
            }
        }
    }
    return $cnt;
}

function emailExists($email) {
    $sql = "SELECT id, contact_id FROM civicrm_email WHERE email = '$email';";
    $result = $GLOBALS['civiDb']->select($sql);
    if (!$result) {
        return false;
    } else {
        return (!empty($result[0]['contact_id'])) ? $result[0]:false;
    }
}

function addContact($firstName,$lastName,$email) {
    $c = new contact();
    if (empty($firstName)&&empty($lastName)) {
        $c->civicrmApiObject['first_name'] = $email;
    } else {
        $c->civicrmApiObject['first_name'] = $firstName;
        $c->civicrmApiObject['last_name'] = $lastName;
    }
    $c->civicrmApiObject['api.email.create'] = array('email'=>$email,'is_primary'=>1);
    $result = $c->createNewContact();
    if ($result['is_error']>0) {
        $output .= "<pre />";
        $output .= print_r($result,true);
        $output .= print_r($c,true);
        return false;
    } else {
        $return = array('contact_id'=>$result['id'],'id'=>$result['values'][$result['id']]['api.email.create']['id']);
        return $return;
    }
}

function addContactToGroup($contact_id,$groupId,$emailId) {
    $sql = "INSERT INTO civicrm_group_contact (contact_id,group_id,email_id,`status`) VALUES ($contact_id,$groupId,$emailId,'Added');";
    $result = $GLOBALS['civiDb']->execute($sql);
    $sql = "INSERT INTO `civicrm_subscription_history` (contact_id,group_id,`date`,`method`,`status`) VALUES ($contact_id,$groupId,'".date("Y-m-d H:i:s")."','MC Sync','Added');";
    $result = $GLOBALS['civiDb']->execute($sql);
    return $result;
}