<?php

/**
 * @description
 * This script saves changes for the acknowledgement widget.
 * 
 * @author      Chezre Fredericks
 * @date_created 26/05/2014
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include '../../inc/globals.php';

#   SAVE WIDGET CHANGES
$w = new widget();
$w->Load($_POST['wid_id']);
$w->wid_name = $_POST['wid_name'];
$w->Save();

#   SAVE USER CONFIGURATION
$myConfigFilename = "../../users/".$_SESSION['dms_user']['userid'].".user.xml";
$myConfig = simplexml_load_file($myConfigFilename); 
// remove old departments
unset($myConfig->acknowledgement->departments);
// add the new departments
//   first clear the session user config.
$_SESSION['dms_user']['config']['departments'] = array();
$newDepartments = $myConfig->acknowledgement->addChild('departments');
foreach ($_POST['departments'] as $k=>$v) {
    $item = $newDepartments->addChild('department');
    $item->addAttribute('code',$v);
    $isDefaultEmail = ($_POST['impersonateDpt']==$v) ? 'Y':'N';
    $item->addAttribute('impersonate',$isDefaultEmail);
    $deptName = $GLOBALS['functions']->GetDepartmentName($v);
    $item->addChild('secretary',$deptName);
    $_SESSION['dms_user']['config']['departments'][$v] = $deptName;
}
$_SESSION['dms_user']['config']['impersonate'] = $_POST['impersonateDpt'];

/*echo "<pre />";
echo $myConfig->asXML();*/

// save the new config
$myConfigSaveResult = file_put_contents($myConfigFilename,$myConfig->asXML());

#   GO BACK TO THE DASHBOARD
header('location:/dms');