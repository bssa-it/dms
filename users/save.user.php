<?php

/**
 * @description
 * Description of script
 * 
 * @author      Chezre Fredericks
 * @date_created 01/04/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$sysUserConfigFile = 'user.config.xml';
$sysConfig = simplexml_load_file($sysUserConfigFile);


$jmlUserId = $_POST['usr_id'];
$userConfigFilename = $jmlUserId.".user.xml";
$userConfig = simplexml_load_file($userConfigFilename);
$userConfig->userType = $_POST['userType'];
$userConfig->officeId = $_POST['officeId'];
foreach ($userConfig->permissions->children() as $p) {
    foreach ($p->children() as $perm) $userConfig->permissions->{$p->getName()}->{$perm->getName()} = (!empty($_POST[$p->getName() . '_' . $perm->getName()])) ? 'Y':'N';
}
$configSaveResult = file_put_contents($userConfigFilename,$userConfig->asXML());

$u = new createUser();
$u->Load($jmlUserId);
$u->isHo = ($_POST['userType']=='Head Office');
$u->isSuperUser = ($_POST['userType']=='Super User');
$u->addJoomlaUserGroups($jmlUserId);

$widgets = array();
if (!empty($_POST['widgets'])) {
    foreach ($_POST['widgets'] as $k=>$v) {
        if (empty($v)) {
            $widgets[] = null;
        } else {
            $w = new widget();
            $q = $k+1;
            $widgetPosition = 'usr_q'.$q.'_wid_id';
            $widgetExists = $w->Load($u->$widgetPosition);
            if (!$widgetExists||$w->wid_directory!=$v) {
                $tmplFile = $GLOBALS['dms_base_path'].'widgets/'.$v.'/tmpl.'.$v.'.xml';
                $widgetFile = preg_replace('/tmpl/',$jmlUserId,$tmplFile);
                copy($tmplFile,$widgetFile);
                $w->copyTemplate($v, $jmlUserId);
            }
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
$u->Save();

# Add user to the dms user group
$gid = (int)$sysConfig->dmsUserGroupId;
$parms['version'] = 3;
$parms['group_id'] = $gid;
$parms['contact_id'] = $u->usr_contact_id;
civicrm_api('GroupContact','create',$parms);

header('location: manage.user.php');