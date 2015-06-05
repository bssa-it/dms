<?php

/**
 * @description
 * This script saves changes for the user config file.
 * 
 * @author      Chezre Fredericks
 * @date_created 26/05/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

#   SAVE USER CONFIGURATION
$myConfigFilename = "users/".$_SESSION['dms_user']['userid'].".user.xml";
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

$myConfig->donorSearch->defaultDonorDeleted = $_POST['defaultDonorDeleted'];
$myConfig->donorSearch->defaultDatabase = $_POST['defaultDatabase'];
$defaultBamOnly = (!empty($_POST['defaultBamOnly'])) ? 'Y':'N';
$myConfig->donorSearch->defaultBamOnly = $defaultBamOnly;

// save the new config
$myConfigSaveResult = file_put_contents($myConfigFilename,$myConfig->asXML());

#   GO BACK TO THE DASHBOARD
header("location:myconfig.php");