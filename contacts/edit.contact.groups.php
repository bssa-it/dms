<?php

/**
 * @description
 * This page loads the donor's groups edit form.
 * 
 * @author      Chezre Fredericks
 * @date_created 15/07/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if (!isset($_GET['d'])||empty($_GET['d'])) {
    echo 'not found';
    exit();
}

#   SET VITAL VARIABLES
$dnrNo = $_GET['d'];

#   LOAD DONOR DETAIL
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
if (!$dnr) {
    echo 'not found';
    exit();
}
$contactId = $dnr['contact_id'];

$groups = $GLOBALS['functions']->getContactGroups($contactId);
$contactGroups = '<tr><td colspan="2">No Groups found</td></tr>';
$existingGroups = array();
if ($groups['count']>0) {
    $contactGroups = '';
    foreach ($groups['values'] as $gk=>$gv) {
        $contactGroups .= '<tr><td>'.$gv['title'].'</td><td>Joined: '.date("d-m-Y",strtotime($gv['in_date'])).' <img src="/dms/img/delete.png" width="16" height="16" class="delImg" gcid="'.$gv['id'].'" title="remove from '.$gv['title'].' group" /></td></tr>';
        $sowerEmail = $GLOBALS['functions']->getSowerEmail($contactId,$gv['group_id']);
        $contactGroups .= (!empty($sowerEmail)) ? '<tr><td colspan="2">'.$sowerEmail['email'].'</td></tr>':'';
        $existingGroups[] = $gv['group_id'];
    }
}

$parms['version'] = 3;
$parms['is_active'] = 1;
$parms['options']['sort'] = 'title';
$allGroups = civicrm_api('Group', 'get', $parms);
if ($allGroups['count']>0) {
    $groupOpts = '';
    foreach ($allGroups['values'] as $g) {
        $emailRequired = false;
        foreach ($GLOBALS['xmlConfig']->sower->mailchimpLists->list as $mcl) { 
            if ($emailRequired) continue;
            $emailRequired = ((int)$g['id']==(int)$mcl['groupId']);
        }
        $emailRequiredFlag = ($emailRequired) ? 'Y':'N';
        if (!in_array($g['id'], $existingGroups)) $groupOpts .=  '<option value="' . $g['id'] . '" emailRequired="'.$emailRequiredFlag.'">' . $g['title'] . '</option>';
    }
}

$emailAddresses = $GLOBALS['functions']->getCiviContactEmailAddresses($contactId);
$emailOpts = '';
if (!empty($emailAddresses)) {
    foreach ($emailAddresses as $e) {
        $emailOpts .= '<option value="'.$e['id'].'">'.$e['emailAddress'].'</option>';
    }
}

#   SHOW THE HTML
require('html/'.$curScript.'.htm');