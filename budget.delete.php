<?php

/**
 * @description
 * This script deletes a budget from the dms_budget table.
 * 
 * @author      Chezre Fredericks
 * @date_created 21/08/2012
 * @Changes
 * 
 */
#   BOOTSTRAP
require("inc/globals.php");

#   PERMISSION & CONFIG CHECK IF EDIT IS ALLOWED
$isUserAuthorized = $_SESSION['dms_user']['authorisation']->canDelete;
$xmlConfig = simplexml_load_file("inc/config.xml");
if (!$isUserAuthorized||(string)$xmlConfig->budget['allowEdit']=='false') {
    header('location:budget.php');
    exit();   
}

#   DELETE RECORD FROM dms_budget TABLE
$b = new budget;
$b->Load($_POST['bud_id']);
if ($isUserAuthorized) $b->delete();

#   GO BACK TO BUDGET PAGE
header("location:budget.php");