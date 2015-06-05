<?php

/**
 * @description
 * This script saves the BAM acknowledgement widget settings.
 * 
 * @author      Chezre Fredericks
 * @date_created 22/05/2014
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include '../../inc/globals.php';

#   UPDATE THE WIDGET DETAILS
$w = new widget;
$w->Load($_POST['wid_id']);
$w->wid_name = $_POST['wid_name'];
$w->Save();

#   UPDATE THE USER'S WIDGET CONFIG FILE
$filename = "../../widgets/". $w->wid_directory. '/' . $w->wid_xmlFilename;
$prompts = simplexml_load_file($filename);
$prompts->contributionFloorValue = $_POST['contributionFloorValue'];
$result = file_put_contents($filename,$prompts->asXML());

#   GO BACK TO THE DASHBOARD
header('location:/dms');