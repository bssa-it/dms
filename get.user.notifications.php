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

echo $GLOBALS['functions']->getUserNotifications();