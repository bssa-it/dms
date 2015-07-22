<?php

/**
 * @description
 * This script loads the form for editing/creating a new contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 17/06/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$curScript = basename(__FILE__, '.php');
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = $title = 'Create Contact';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   SANITIZE POSTED VARIABLES
if(!empty($_POST)) $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$contactTypeOpts = '';
$contactTypes = $GLOBALS['functions']->getCiviContactTypes();
if (!empty($contactTypes)) {
    foreach ($contactTypes as $t) $contactTypeOpts .= '<option value="'.$t['label'].'">'.$t['label'].'</option>';
}

$titleOptGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->titles;
$titleOptsValues = $GLOBALS['functions']->getCiviOptionValues($titleOptGroupId);
$titleOpts = '';
if (!empty($titleOptsValues)) {
    foreach ($titleOptsValues as $t) {
        $titleOpts .= '<option value="'.$t['value'].'">'.$t['label'].'</option>';
    }
}

$departmentOpts = $denominationOpts = '';
$myDepartment = (empty($_SESSION['dms_user']['config']['impersonate'])) ? '0':$_SESSION['dms_user']['config']['impersonate'];
$department = new civicrm_dms_department_extension($myDepartment);
$depColor = (empty($department->dep_chart_color)) ? '#254B7C' : $department->dep_chart_color;

$allDepartments = $GLOBALS['functions']->GetDepartmentList();
$departmentOpts = '';
if (!empty($allDepartments)) {
    foreach ($allDepartments as $d) {
        $selected = ($myDepartment==$d['dep_id']) ? ' SELECTED':'';
        $departmentOpts .= '<option value="' . $d['dep_id'] . '"'.$selected.'>' . $d['dep_id'] .' - '.$d['dep_name'].'</option>';   
    }
}
$denominations = $department->getDistinctDenominations();
$denominationOpts = $organisationName = '';
$denomination = '00';
if (!empty($denominations)) {
    foreach ($denominations as $dn) {
        $selected = ($dn['den_id']==$denomination) ? ' SELECTED':'';
        $denominationOpts .= "\n" .'<option value="'.$dn['den_id'].'"'.$selected.'>'.$dn['den_id'].' - '.$dn['den_name'].'</option>';
    }
}

$organisation = new civicrm_dms_organisation_extension($myDepartment . '0000000');
$organisationName = (empty($organisation->org_name)) ? 'Unknown' : $organisation->org_name . ' (' .$organisation->org_region . ')';

$categories = $GLOBALS['functions']->GetCategoryList();
$categoryOpts = '';
foreach ($categories as $c) {
    $categoryOpts .= '<option value="'.$c['cat_id'].'">'.str_pad($c['cat_id'],4,'0',STR_PAD_LEFT).' - '.$c['cat_name'].'</option>';
}


#   ADD TEMPLATE
require('html/'.$curScript.'.htm');