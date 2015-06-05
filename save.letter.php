<?php 

/**
 * @description
 * This file saves the template in the database.
 * 
 * @author      Chezre Fredericks
 * @date_created 29/11/2013
 * @Changes
 */

#   BOOTSTRAP
include("inc/globals.php");

#   CREATE TEMPLATE 
$t = new template;
$p = $_POST;
unset($p['letterEditor']);
foreach ($p as $k=>$v) $t->$k = $v; 

$t->tpl_createdByUserId = $_SESSION['dms_user']['userid'];
$t->tpl_dateCreated = date("Y-m-d H:i:s");
$t->tpl_body = htmlentities($_POST['letterEditor']);

#   SAVE TEMPLATE
$t->save();

#   GO BACK...
header('location:letter.php');