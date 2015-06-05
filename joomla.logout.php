<?php

/**
 * @description
 * This script logs the user out of the joomla system.
 * 
 * @author      Chezre Fredericks
 * @date_created 29/01/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");

$app = JFactory::getApplication();
$app->logout();

header('location:index.php');