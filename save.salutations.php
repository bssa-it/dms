<?php

/**
 * @description
 * Script that saves the salutation rule
 * 
 * @author      Chezre Fredericks
 * @date_created 09/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

$p = $_POST;
$s = new salutations();
if (!empty($_POST['sal_id'])) $s->Load($_POST['sal_id']);
foreach ($p as $k=>$v) $s->$k = $v;

if (empty($_POST['sal_id'])) {
    $s->sal_active = 'Y';
    $s->sal_created_date = date("Y-m-d H:i:s");
    $s->sal_created_user_id = $_SESSION['dms_user']['userid'];
}

$s->Save();
header("location:salutations.php");