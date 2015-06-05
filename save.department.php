<?php

/**
 * @description
 * Script that saves the department changes made by the user
 * 
 * @author      Chezre Fredericks
 * @date_created 20/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

#   IS AUTHORISED
$authorised = $_SESSION['dms_user']['authorisation']->isAdmin;
if (!$authorised) $GLOBALS['functions']->goToIndexPage();

$p = $_POST;
$d = new department;
foreach ($p as $k=>$v) $d->$k = $v;

$d->Save();
header("location:department.php");