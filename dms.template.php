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

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');

$menu = new menu;
$pageHeading = $title = 'Page Heading';

$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
require('html/'.$curScript.'.htm');