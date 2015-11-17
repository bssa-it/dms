<?php

/**
 * @description
 * This script saves changes for the announcements.
 * 
 * @author      Chezre Fredericks
 * @date_created 18/05/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include '../../inc/globals.php';
$w = new widget();
$w->Load($_POST['wid_id']);
$w->wid_name = $_POST['wid_name'];
$w->Save();

#   SAVE XML CHANGES
$filename = "otd.connection.xml";
$xml = simplexml_load_file($filename);
$ignore = array('wid_name','wid_id');
foreach ($_POST as $k=>$v) if (!in_array($k, $ignore)) $xml->$k = $v;

file_put_contents($filename, $xml->asXML());
header('location: /dms/index.php');