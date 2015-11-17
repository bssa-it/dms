<?php

/**
 * @description
 * This script retrieves the user's notifications
 * 
 * @author      Chezre Fredericks
 * @date_created 06/02/2015
 * @Changes
 * 
 */
include("inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$userDetails['userid'] = $_SESSION['dms_user']['userid'];
$userDetails['username'] = $_SESSION['dms_user']['username'];
$userDetails['fullname'] = $_SESSION['dms_user']['fullname'];
$userDetails['ipaddress'] = $_SESSION['dms_user']['ipaddress'];
$userDetails['civ_contact_id'] = $_SESSION['dms_user']['civ_contact_id'];
$userDetails['office_id'] = $_SESSION['dms_user']['office_id'];
print json_encode($userDetails);