<?php

/**
 * @description
 * This script inserts a budget into the dms_budget table
 * 
 * @author      Chezre Fredericks
 * @date_created 07/11/2013
 * @Changes
 * 
 */

#   BOOTSTRAP
require("inc/globals.php");

#   PERMISSION & CONFIG CHECK IF EDIT IS ALLOWED
$xmlConfig = simplexml_load_file("inc/config.xml");
$updateAuthorization = $_SESSION['dms_user']['authorisation']->canUpdate;
if (!$updateAuthorization||(string)$xmlConfig->budget['allowEdit']=='false') {
    header('location:budget.php');
    exit();   
}
 
#  EDIT BUDGET
$p = $_POST;
$b = new budget;
$b->Load($p['bud_id']);
foreach ($p as $k=>$v) $b->$k = $v;
$b->bud_datelastupdated     = date("Y-m-d H:i:s");
$b->bud_update_user         = $_SESSION['dms_user']['userid'];
$b->Save();

#   KEEP BUDGET AND DEPARTMENT IN SESSION VARIABLE
$_SESSION['dmsBudget']['bud_region']     = $b->bud_region;
$_SESSION['dmsBudget']['bud_department'] = $b->bud_department;

#   REDIRECT TO BUDGET PAGE
header("location:budget.php");