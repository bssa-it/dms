<?php

/**
 * @description
 * This script saves the search builder changes to the user's widget, widget config file and user config file.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/05/2014
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

#   SAVE DONOR SEARCH FIELDS TO WIDGET CONFIG FILE
$oldXml = new SimpleXMLElement($w->wid_xmlFilename, null, true);
$prompts = new SimpleXMLElement('<prompts/>');
$xmlFields = new SimpleXMLElement('../../contacts/inc/search.items.xml', null, true);
foreach ($_POST['dnrSearchFields'] as $k=>$v) {
    foreach ($xmlFields as $sItem) 
        if ($sItem['id']==$v) $dispName = (string)$sItem->displayName;   
    $item = $prompts->addChild('item');
    $item->addAttribute('id',$v);
    $item->addChild('displayName',$dispName);
    $item->addChild('widgetDefault','Y');
}
$result = file_put_contents($w->wid_xmlFilename,$prompts->asXML());

#   SAVE USER CONFIGURATION TO CONFIG FILE
$myConfigFilename = "../../users/".$_SESSION['dms_user']['userid'].".user.xml";
$myConfig = simplexml_load_file($myConfigFilename); 
$myConfig->donorSearch->defaultDonorDeleted = $_POST['dnrDeleted'];
$myConfig->donorSearch->defaultDatabase = $_POST['db'];
$defaultBamOnly = (!empty($_POST['bamOnly'])) ? 'Y':'N';
$myConfig->donorSearch->defaultBamOnly = $defaultBamOnly;
$myConfigSaveResult = file_put_contents($myConfigFilename,$myConfig->asXML());

#   UPDATE SESSION VARIABLES
$_SESSION['dms_user']['config']['donorSearch']['defaultDatabase'] = (string)$myConfig->donorSearch->defaultDatabase;
$_SESSION['dms_user']['config']['donorSearch']['defaultBamOnly'] = (string)$myConfig->donorSearch->defaultBamOnly;
$_SESSION['dms_user']['config']['donorSearch']['defaultDonorDeleted'] = (string)$myConfig->donorSearch->defaultDonorDeleted;

#   GO BACK TO DASHBOARD
header('location:/dms');