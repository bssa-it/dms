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

#   SAVE WIDGET CHANGES
$w = new widget();
$w->Load($_POST['wid_id']);
$w->wid_name = $_POST['wid_name'];
$w->Save();

#   GO BACK TO THE DASHBOARD
header('location:/dms');