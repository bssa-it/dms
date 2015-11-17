<?php

/**
 * @description
 * This script loads the 'new user' form
 * 
 * @author      Chezre Fredericks
 * @date_created 31/03/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("../inc/globals.php");
$menu = new menu;
$pageHeading = $title = 'Create New User';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
$settings = simplexml_load_file('user.config.xml');


$userTypeOpts = '<option value="">-- select --</option>';
foreach ($settings->userTypes->uType as $t) {
    $userTypeOpts .= '<option value="'.$t['value'].'">'.$t['value'].'</option>';
}

$allDepartments = $GLOBALS['functions']->GetDepartmentList();
$departmentChks = '';
foreach ($allDepartments as $k=>$v) {
    $departmentChks .= '<div class="dptChckDiv"><input type="checkbox" name="departments[]" id="dp'.$v['dep_id'];
    $departmentChks .= '" alt="'.$v['dep_id']. ' - '.$v['dep_name'].'" value="'.$v['dep_id'];
    $departmentChks .= '" onchange="addDepartmentToImpersonate()" />';
    $departmentChks .= '<label for="dp'.$v['dep_id'].'"> '.$v['dep_id']. ' - '.$v['dep_name'].'</label>&nbsp;</div>';
}

$widOpts = '<option value="">-- select --</option>';
$widJavaObj = 'var widgetList = []';
$allWidgets = $GLOBALS['functions']->getAllWidgetTemplates();
foreach ($allWidgets as $k=>$v) {
    $widOpts .= '<option value="'.$v['wid_directory'].'">'.$v['wid_directory'].'</option>';
    $widJavaObj .= "\n". 'widgetList.push("'.$v['wid_directory'].'");';
}

$officeOpts = '';
$allOffices = civicrm_dms_office_extension::getAll();
foreach ($allOffices as $o) {
    $officeOpts .= '<option value="'.$o['id'].'">'.$o['name'].'</option>';
}

require('../html/user.htm'); 