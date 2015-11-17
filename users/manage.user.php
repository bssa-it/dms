<?php

/**
 * @description
 * This script shows all the users and allows you to edit a user.
 * 
 * @author      Chezre Fredericks
 * @date_created 01/04/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

$menu = new menu;
$pageHeading = $title = 'User Manager';
$settings = simplexml_load_file('user.config.xml');

$userRows = '';
$allUsers = $GLOBALS['functions']->getAllUsers();
$userJavaArray = "\n" .'var users = [];';
foreach ($allUsers as $u) {
    
    if (!file_exists($u['usr_id'].'.user.xml')) continue;
    $emailAddress = $fullname = 'unknown';
    $userConfig = simplexml_load_file($u['usr_id'].'.user.xml');
    $userType = (string)$userConfig->userType;
    if (!empty($u['usr_contact_id'])) {
        $contact = $GLOBALS['functions']->getCiviContact($u['usr_contact_id']);
        $fullname = $contact['display_name'];
        $email = $GLOBALS['functions']->getCiviContactEmailAddresses($u['usr_contact_id'],true);
        $emailAddress = $email[0]['emailAddress'];
    }
    
    $userRows .= "\n" . '<tr uid="'.$u['usr_id'].'">';
    $userRows .= '<td>'. $userType .'</td>';
    $userRows .= '<td>'. $emailAddress .'</td>';
    $userRows .= '<td>'. $fullname .'</td>';
    $userRows .= '<td>'. $u['usr_id'] .'</td>';
    $userRows .= '<td>'. $u['usr_contact_id'] .'</td>';
    $userRows .= '<td>'. (string)$userConfig->officeId .'</td>';
    $userRows .= '</tr>';
    
    $userJavaArray .= "\n" . 'users.push({u: '.$u['usr_id'];
    foreach (range(1,4) as $q) {
        if (empty($u['usr_q'.$q.'_wid_id'])) {
            $val = "0";
        } else {
          $w = new widget();
          $w->Load($u['usr_q'.$q.'_wid_id']);
          $val = $w->wid_directory;
        }
        $userJavaArray .= ',q'.$q.': "'.$val.'"';
    }
    $userJavaArray .= ',permissions: {';
    $first = true;
    foreach ($userConfig->permissions->children() as $p) {
        $userJavaArray .= ($first) ? '':',';
        $canView = ($p->view=='Y') ? 'true':'false';
        $canSave = ($p->save=='Y') ? 'true':'false';
        $userJavaArray .= $p->getName() . ": {view: $canView,save: $canSave}";
        $first = false;
    }
    $userJavaArray .= '}';
    $userJavaArray .= '});';
}
$userTypeOpts = '<option value="">-- select --</option>';
foreach ($settings->userTypes->uType as $t) {
    $userTypeOpts .= '<option value="'.$t['value'].'">'.$t['value'].'</option>';
}
$widOpts = '<option value="">-- select --</option>';
$widJavaObj = "\n" . 'var widgetList = [];';
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

$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
require('../html/'.$curScript.'.htm');