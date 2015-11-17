<?php

/**
 * @description
 * This script returns the advanced search form.
 * 
 * @author      Chezre Fredericks
 * @date_created 13/05/2014
 * @Changes
 * 04/03/2015 - Chezre Fredericks:
 * Added -
 *      Organisation Id
 *      Category Id
 *      BAM Certificate Printed
 *      Language
 *      Contact Type
 * 
 */
 
 #   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CREATE MENU, PAGE HEADING & TITLE
$menu = new menu;
$pageHeading = 'Advanced Search';
$title = $pageHeading;
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   LOAD FILEDS FROM search.items.xml
$xmlFields = new SimpleXMLElement('inc/search.items.xml', null, true);
$searchFields = '';
foreach ($xmlFields as $item) 
    $allFields[(string)$item->displayGroup][(string)$item['id']] = array((string)$item->displayName,(string)$item->widgetDefault);

$dnrDeletedValues = array(''=>'-- select --','A'=>'All','Y'=>'Yes','N'=>'No');
$dnrDeletedOpts = '';
foreach ($dnrDeletedValues as $k=>$v) {
    $dnrDeletedOpts .= '<option value="'.$k.'">'.$v.'</option>';
}

$regions = $GLOBALS['functions']->GetRegionList();
$regionOpts = '';
if (!empty($regions)) {
    foreach ($regions as $r) {
        $regionOpts .= '<option value="'.$r['region_id'].'">'.$r['region_id']. ' ' .$r['region_name'].'</option>';
    }
}

$contactTypes = $GLOBALS['functions']->getCiviContactTypes();
$contactTypeOpts = '<option value="">-- select --</option>';
if (!empty($contactTypes)) {
    foreach ($contactTypes as $t) {
        $contactTypeOpts .= '<option value="'.$t['label'].'">'.$t['label'].'</option>';
    }
}

$langCiviGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->languageCiviOptGroupId;
$languages = $GLOBALS['functions']->getCiviOptionValues($langCiviGroupId);
$prefLangOpts = '<option value="">-- select --</option>';
if (!empty($languages)) {
    foreach ($languages as $l) $prefLangOpts .= "\n" . '<option value="' . $l['name'] . '">' . $l['label'] . '</option>';
}

$groups = $GLOBALS['functions']->getGroups();
$groupInputs = '';
foreach ($groups['values'] as $g) {
    $groupInputs .= '<div><input type="checkbox" name="srch_group[]" id="g-'.$g['id'].'" value="'.$g['id'].'"><label for="g-'.$g['id'].'" class="lblGroup">'.$g['title'].'</label></div>';
}

$monthOpts = '';
foreach (range(0,12) as $m) {
    if ($m===0) {
        $monthOpts .= '<option value="'.$m.'">-- No Reminder --</option>';
    } else {
        $monthOpts .= '<option value="'.$m.'">'. date("F",strtotime(date("Y-").str_pad($m,2,'0',STR_PAD_LEFT)."-01")) .'</option>';
    }
}

$exclude ="/(srch_recordId|srch_donorName|srch_contactNo|srch_donorDeleted)/i";
$cnt = 0;
foreach ($allFields as $k=>$group) {
    $cnt++;
    $searchFields .= '<div class="searchGroupingDiv">';
    $searchFields .= "\n\t".'<div class="searchGroupingHeadingDiv">'.$k.'</div>';
    $tblId = "tbl" . preg_replace('/ /','',$k) . $cnt;
    $searchFields .= '<table width="100%" cellpadding="0" cellspacing="1" align="center" class="tblFields" id="'.$tblId.'">';
    foreach ($group as $fieldId=>$fieldDesc) {
        if (preg_match($exclude,$fieldId)) continue;
        $searchFields .= "\n\t" . '<tr><td><label for="'.$fieldId.'">'.$fieldDesc[0].'</label></td><td>';
        switch ($fieldId) {
            case "srch_donorDeleted":
                $searchFields .= '<select name="srch_donorDeleted" id="srch_donorDeleted">'.$dnrDeletedOpts.'</select>';  
                break;
            case "srch_region":
                $searchFields .= '<select name="srch_region" id="srch_region"><option value="">-- select --</option>';
                $searchFields .= $regionOpts.'</select>';
                break;
            case "srch_contactType":
                $searchFields .= '<select name="srch_contactType" id="srch_contactType">'.$contactTypeOpts.'</select>';
                break;
            case "srch_reminderMonth":
                $searchFields .= '<select name="srch_reminderMonth" id="srch_reminderMonth">'.$monthOpts.'</select>';
                break;
            case "srch_language":
                $searchFields .= '<select name="srch_language" id="srch_language">'.$prefLangOpts.'</select>';
                break;
            case "srch_bamCertificatePrinted":
                $searchFields .= '<select name="srch_bamCertificatePrinted" id="srch_bamCertificatePrinted">';
                $searchFields .= '<option value="">All</option>';
                $searchFields .= '<option value="1">Yes</option>';
                $searchFields .= '<option value="0">No</option>';
                $searchFields .= '</select>';
                break;
            case "srch_group":
                $searchFields .= $groupInputs;
                break;
            case "srch_lastContributionDate":
                $searchFields .= '<div class="dateDiv"><div><label for="start_srch_lastContributionDate">Start date</label></div><input type="date" id="start_srch_lastContributionDate" name="start_srch_lastContributionDate" style="width: 150px;" /></div>';
                $searchFields .= '<div class="dateDiv"><div><label for="end_srch_lastContributionDate">End date</label></div><input type="date" id="end_srch_lastContributionDate" name="end_srch_lastContributionDate" style="width: 150px;" /></div>';
                break;
            default:
                $searchFields .= '<input type="text" name="'.$fieldId.'" id="'.$fieldId;
                $searchFields .= '" onkeypress="enterDonorNumber(event)" value="" />';        
                break;
        }
        $searchFields .= '</td></tr>';
    }
    $searchFields .= '</table>';
    $searchFields .= '</div>';
}

# LOAD USER PREFERENCES
$isBamOnly = '';
$isCiviChecked = ' CHECKED';
$isArchChecked = '';  

$userConfig = simplexml_load_file("../users/" . $_SESSION['dms_user']['userid'] . ".user.xml");
if (!empty($userConfig->donorSearch->defaultBamOnly)) {
    $isBamOnly = ((string)$userConfig->donorSearch->defaultBamOnly=='Y') ? ' CHECKED':'';
}

if (!empty($userConfig->donorSearch->defaultDatabase)) {
    $isCiviChecked = ((string)$userConfig->donorSearch->defaultDatabase=='civiDb') ? ' CHECKED':'';
    $isArchChecked = ((string)$userConfig->donorSearch->defaultDatabase=='archDb') ? ' CHECKED':'';
}

$dnrDeletedValues = array('A'=>'All','Y'=>'Yes','N'=>'No');
$donorDeletedOpts = '';
foreach ($dnrDeletedValues as $k=>$v) {
    $selected = ($_SESSION['dms_user']['config']['donorSearch']['defaultDonorDeleted']==$k) ? ' SELECTED':'';  
    $donorDeletedOpts .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}

#   SHOW HTML
require('html/'.$curScript.'.htm');