<?php

/**
 * @description
 * This script inserts a new budget into the dms_budget table.
 * 
 * @author      Chezre Fredericks
 * @date_created 07/11/2013
 * @Changes
 * 
 */

#   BOOSTRAP
require("inc/globals.php");

#   PERMISSIONS & CONFIG CHECK IF EDIT IS ALLOWED
$xmlConfig = simplexml_load_file("inc/config.xml");
$isUserAuthorized = $_SESSION['dms_user']['authorisation']->canInsert;
if (!$isUserAuthorized||(string)$xmlConfig->budget['allowEdit']=='false') {
    header('location:budget.php');
    exit();   
}

#   INSERT RECORD
$p = $_POST;
unset($p['bud_id']);
$b = new budget;
foreach ($p as $k=>$v) $b->$k = $v;
$b->bud_dateinserted    = date("Y-m-d H:i:s");
$b->bud_insert_user    = $_SESSION['dms_user']['userid'];
$b->Save();

#   KEEP BUDGET AND DEPARTMENT IN SESSION VARIABLE
$_SESSION['dmsBudget']['bud_region']     = $b->bud_region;
$_SESSION['dmsBudget']['bud_department'] = $b->bud_department;

header("location:budget.php");