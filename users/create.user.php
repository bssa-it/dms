<?php

/**
 * @description
 * This script creates the user files
 * 
 * @author      Chezre Fredericks
 * @date_created 18/03/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("../inc/globals.php");
include("../inc/class.user.extension.php");
$sysUserConfigFile = 'user.config.xml';
$sysConfig = simplexml_load_file($sysUserConfigFile);

# Get/Create Joomlal detail
$u = new createUser();
$jmlUserId = $u->doesJoomlaUserExist($_POST['email']);
$isAdmin = ($_POST['userType']=='Head Office');
$isSuperUser = ($_POST['userType']=='Super User');
if (!$jmlUserId) {
    $jmlUserId = $u->createJoomlaUser($_POST['email'],$_POST['first_name'].' '.$_POST['last_name'],$isAdmin,$isSuperUser);
} else {
    $u->addJoomlaUserGroups($jmlUserId,$isAdmin,$isSuperUser);
}
$dmsUserExists = $u->Load($jmlUserId);

# create user settings file
$userConfigFilename = $jmlUserId.".user.xml";
if (!file_exists($userConfigFilename)) {
    $copyFile = copy('tmpl.user.xml',$userConfigFilename);
    $userConfig = simplexml_load_file($userConfigFilename);
    $userConfig->userType = $_POST['userType'];
    unset($userConfig->acknowledgement->departments);
    $newDepartments = $userConfig->acknowledgement->addChild('departments');
    foreach ($_POST['departments'] as $k=>$v) {
        $item = $newDepartments->addChild('department');
        $item->addAttribute('code',$v);
        $isDefaultEmail = ($_POST['primaryDepartment']==$v) ? 'Y':'N';
        $item->addAttribute('impersonate',$isDefaultEmail);
        $deptName = $GLOBALS['functions']->GetDepartmentName($v);
        $item->addChild('secretary',$deptName);
    }
    $configSaveResult = file_put_contents($userConfigFilename,$userConfig->asXML());
}

# First check if email exists:  
$email['version'] = 3;
$email['email'] = $_POST['email'];
$result = civicrm_api('Email', 'get', $email);




$createNewContact = true;
if ($result['count']==1) {
    $u->usr_contact_id = $result['values'][$result['id']]['contact_id'];
    $createNewContact = false;
} elseif ($result['count']>1) {
    foreach ($result['values'] as $k=>$v) {
        $contacts['version'] = 3;
        $contacts['id'] = $v['contact_id'];
        $result = civicrm_api('Contact','get',$contacts);
        if ($result['count']>0&&$createNewContact) {
            if (empty($result['values'][$result['id']]['contact_sub_type'][0])) continue;
            if ($result['values'][$result['id']]['contact_sub_type'][0]=='Staff') {
                $u->usr_contact_id = $result['values'][$result['id']]['contact_id'];
                $createNewContact = false;
            }
        }
    }
} 
if ($createNewContact) {
    # insert contact into CiviCRM
    $contact['version'] = 3;
    $contact['first_name'] = $_POST['first_name'];
    $contact['last_name'] = $_POST['last_name'];
    $contact['contact_type'] = 'Individual';
    $contact['contact_sub_type'] = 'Staff';
    $contact['external_identifier'] = $u->nextStaffNo;
    $result = civicrm_api('Contact','Create',$contact);
    if (!empty($result['id'])) {
        $u->usr_contact_id = $result['id'];
        $email['sequential'] = 1;
        $email['contact_id'] = $u->usr_contact_id;
        $email['location_type_id'] = (int)$GLOBALS['xmlConfig']->workLocationTypeId['id'];
        $saveEmailResult = civicrm_api('Email', 'create', $email);
        
        $sysConfig->nextStaffNo = $u->nextStaffNo+1;
        $configSaveResult = file_put_contents($sysUserConfigFile,$sysConfig->asXML());
    }
}

# create widget files
$widgets = array();
if (!empty($_POST['widgets'])) {
    foreach ($_POST['widgets'] as $k=>$v) {
        if (empty($v)) {
            $widgets[] = null;
        } else {
            $tmplFile = $GLOBALS['dms_base_path'].'widgets/'.$v.'/tmpl.'.$v.'.xml';
            $widgetFile = preg_replace('/tmpl/',$jmlUserId,$tmplFile);
            copy($tmplFile,$widgetFile);
            $w = new widget();
            $w->copyTemplate($v, $jmlUserId);
            $widgets[] = $w->wid_id;
        }
    }
}

if (!empty($widgets)) {
    foreach ($widgets as $k=>$v) {
        $q = $k+1;
        $widgetPosition = 'usr_q'.$q.'_wid_id';
        $u->$widgetPosition = $v;
    }    
}
if (!$dmsUserExists) {
    $u->Save($jmlUserId);
} else {
    $u->Save();
}

# Add user to the dms user group
$gid = (int)$sysConfig->dmsUserGroupId;
$parms['version'] = 3;
$parms['group_id'] = $gid;
$parms['contact_id'] = $u->usr_contact_id;
civicrm_api('GroupContact','create',$parms);
        
header('location: manage.user.php');