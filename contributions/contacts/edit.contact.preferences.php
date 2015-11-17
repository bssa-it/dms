<?php

/**
 * @description
 * This page loads the donor's preferences edit form.
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
$commMethodCiviGroupId = (string)$GLOBALS['xmlConfig']->defaultPreferences->communicationMethodCiviOptGroupId;
$commMethods = $GLOBALS['functions']->getCiviOptionValues($commMethodCiviGroupId); 
$langCiviGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->languageCiviOptGroupId;
$languages = $GLOBALS['functions']->getCiviOptionValues($langCiviGroupId);

#   SET VITAL VARIABLES
$dnrNo = $_GET['d'];
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
if (!$dnr) $GLOBALS['functions']->goToIndexPage();
$contactId = $dnr['contact_id'];
$a = new acknowledgementpreferences();
$a->LoadByContactId($contactId);

if ($dnr['do_not_email']=='1') {
    $do_not_email = ' CHECKED';
    $email_ok = '';
} else {
    $do_not_email = '';
    $email_ok = ' CHECKED';
}

if ($dnr['do_not_phone']=='1') {
    $do_not_phone = ' CHECKED';
    $phone_ok = '';
} else {
    $do_not_phone = '';
    $phone_ok = ' CHECKED';
}

if ($dnr['do_not_mail']=='1') {
    $do_not_mail = ' CHECKED';
    $mail_ok = '';
} else {
    $do_not_mail = '';
    $mail_ok = ' CHECKED';
}

if ($dnr['do_not_sms']=='1') {
    $do_not_sms = ' CHECKED';
    $sms_ok = '';
} else {
    $do_not_sms = '';
    $sms_ok = ' CHECKED';
}

if ($a->apr_must_acknowledge=='N') {
    $do_not_acknowledge = ' CHECKED';
    $acknowledge_ok = '';
} else {
    $do_not_acknowledge = '';
    $acknowledge_ok = ' CHECKED';
}
$prefLangOpts = '';
foreach ($languages as $l) {
    $selected = ($l['name']==$dnr['preferred_language']) ? ' SELECTED':'';
    $prefLangOpts .= "\n" . '<option value="' . $l['name'] . '"' . $selected . '>' . $l['label'] . '</option>';
}

$prefCommMethodOpts = '';
foreach ($commMethods as $m) {
    $selected = ($m['id']==$dnr['preferred_communication_method'][0]) ? ' SELECTED':'';
    $prefCommMethodOpts .= "\n" . '<option value="' . $m['id'] . '"' . $selected . '>' . $m['label'] . '</option>';
}

$donorOtherDetail = $GLOBALS['functions']->getCiviDmsDonorOtherDetail($contactId);
$monthOpts = '';
foreach (range(0,12) as $m) {
    $selected = ($m==$donorOtherDetail['reminder_month']) ? ' SELECTED':'';
    if ($m===0) {
        $monthOpts .= '<option value="'.$m.'"'.$selected.'>-- No Reminder --</option>';
    } else {
        $monthOpts .= '<option value="'.$m.'"'.$selected.'>'. date("F",strtotime(date("Y-").str_pad($m,2,'0',STR_PAD_LEFT)."-01")) .'</option>';   
    }
}

$ackFrequencyOpts = '';
$frequencies = $GLOBALS['xmlConfig']->acknowledgementconfig->preferences->frequencies->frequency;
foreach ($frequencies as $f) {
    if (empty($a->apr_frequency)) {
        $selected = ($f['default']=='Y') ? ' SELECTED':'';
    } else {
        $selected = ($f['value']==$a->apr_frequency) ? ' SELECTED':'';   
    }
    $ackFrequencyOpts .= "\n" . '<option value="' . $f['value'] . '"' . $selected . '>' . $f['desc'] . '</option>';
}

$ackPrefMethodOpts = '';
foreach ($commMethods as $m) {
    $selected = ($m['label']==$a->apr_preferred_method) ? ' SELECTED':'';
    $ackPrefMethodOpts .= "\n" . '<option value="' . $m['label'] . '"' . $selected . '>' . $m['label'] . '</option>';
}

$salutation = (!empty($dnr['postal_greeting_custom'])) ? $dnr['postal_greeting_custom']:'';
        
#   SHOW THE HTML
require('html/'.$curScript.'.htm');