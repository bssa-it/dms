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
$authorised = $_SESSION['dms_user']['authorisation']->isHo;
if (!$authorised) $GLOBALS['functions']->goToIndexPage();

$p = $_POST;
$d = new civicrm_dms_department($p['id']);
foreach ($p as $k=>$v) if ($k!='id') $d->$k = $v;

$d->Save();
header("location:department.php");