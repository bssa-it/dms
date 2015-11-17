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

#   SAVE XML CHANGES
$filename = $_SESSION['dms_user']['userid'].".cricket.xml";
$xml = simplexml_load_file($filename);
$xml->matchId = $_POST['id'];
file_put_contents($filename, $xml->asXML());