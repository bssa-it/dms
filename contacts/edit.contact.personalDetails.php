<?php

/**
 * @description
 * This page loads the donor personal details edit form.
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
$isDeleted = ($dnr['contact_is_deleted']==1);
$contactSuperType = $dnr['contact_type'];
if ($contactSuperType=='Individual') { 
    $showOrgName = 'display: none';
    $showIndDetails = '';
} else {
    $showOrgName = '';
    $showIndDetails = 'display: none';
}


$contactType = (!empty($dnr['contact_sub_type'])) ? strtolower($dnr['contact_sub_type'][0]) : strtolower($dnr['contact_type']);
$donorOtherDetail = $GLOBALS['functions']->getCiviDmsDonorOtherDetail($contactId);
$customValues = $GLOBALS['functions']->getCiviContactCustomValues($contactId);
$contactTypeOpts = '';
$javaContactTypeArray = "\n" . 'var ct = [];';
$contactTypes = $GLOBALS['functions']->getCiviContactTypes();
if (!empty($contactTypes)) {
    foreach ($contactTypes as $t) {
        $selected = (strtolower(trim($t['label']))==trim($contactType)) ? ' SELECTED':'';
        $contactTypeOpts .= '<option value="'.$t['label'].'"'.$selected.'>'.$t['label'].'</option>';
        $parent = (!empty($t['parent_id'])) ? $contactTypes[$t['parent_id']]['label']:$t['label'];
        $javaContactTypeArray .= "\n\t" . ' ct.push([{ name: "' . $t['label']  .'", superType: "' . $parent . '" }]);';
    }
}

$titleOptGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->titles;
$titleOptsValues = $GLOBALS['functions']->getCiviOptionValues($titleOptGroupId);
$contactTitle = $customValues[12]['latest'];
$titleOpts = '';
if (!empty($titleOptsValues)) {
    $showTitleHelp = (empty($dnr['prefix_id']));
    foreach ($titleOptsValues as $t) {
        $selected = ($dnr['prefix_id']==$t['value']) ? ' SELECTED':'';
        $titleOpts .= '<option value="'.$t['value'].'"'.$selected.'>'.$t['label'].'</option>';
    }
}

$genderOptGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->gender;
$genderOptValues = $GLOBALS['functions']->getCiviOptionValues($genderOptGroupId);
$genderOpts = '';
if (!empty($genderOptValues)) {
    foreach ($genderOptValues as $g) {
        $selected = ($g['value']==$dnr['gender_id']) ? ' SELECTED':'';
        $genderOpts .= '<option value="'.$g['value'].'"'.$selected.'>'.$g['label'].'</option>';
    }
}

$firstName = $dnr['first_name'];
$middleName = $dnr['middle_name'];
$lastName = $dnr['last_name'];
$nickName = $dnr['nick_name'];

$editDetails = $GLOBALS['functions']->getDonorsEditDetailsFromCivi($contactId);
$createdByUser = '';
$modifiedByUser = '';

$showAgeDisplay = 'style="display:none;"';
$yearBadge = '??';
if (!empty($dnr['birth_date'])) {
    $endDate = (!empty($dnr['deceased_date'])) ? $dnr['deceased_date']:null;
    $age = $GLOBALS['functions']->calculate_age($dnr['birth_date'],$endDate);
    $yearBadge = $age->y;
    $showAgeDisplay = '';
}

if ($dnr['is_deceased']==='1') {
    $showDeceasedDate = '';
    $showDeceasedIndicate = 'style="display:none"';
} else {
    $showDeceasedDate = 'style="display:none"';
    $showDeceasedIndicate = '';
}

if ($isDeleted) {
    $showIsDeleted = '';
    $showNotDeleted = 'display:none;';
} else {
    $showIsDeleted = 'display:none;';
    $showNotDeleted = '';
}
        
#   SHOW THE HTML
require('html/'.$curScript.'.htm');