<?php

/**
 * @description
 * This script retrieves a contacts email addresses and parses it back as json objects.
 * 
 * @author      Chezre Fredericks
 * @date_created 23/03/2015
 * @Changes
 * 
 */
include("inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$emailAddresses = $GLOBALS['functions']->getCiviContactEmailAddresses($_GET['contact_id']);
echo json_encode($emailAddresses);