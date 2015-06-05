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
$myConfigFilename = "widgets/".$w->wid_directory."/".$w->wid_xmlFilename;
$myConfig = simplexml_load_file($myConfigFilename); 

// save the new config
$myConfigSaveResult = file_put_contents($myConfigFilename,$myConfig->asXML());

#   GO BACK TO THE DASHBOARD
header('location:/dms');