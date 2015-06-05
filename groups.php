<?php

/**
 * @description
 * This script loads the groups dashboard
 * 
 * @author      Chezre Fredericks
 * @date_created 15/04/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');

$menu = $GLOBALS['functions']->createMenu();
$pageHeading = $title = 'Groups';

$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
require('html/'.$curScript.'.htm');