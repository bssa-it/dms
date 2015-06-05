<?php

/**
 * @description
 * This script retrieves the user edit details for a contact
 * 
 * @author      Chezre Fredericks
 * @date_created 06/02/2015
 * 
 */
include("../inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (!empty($_GET['contact_id'])) {
    $editDetails = $GLOBALS['functions']->getDonorsEditDetailsFromCivi($_GET['contact_id']);
    echo $editDetails;
    exit();
}

echo "&nbsp;";