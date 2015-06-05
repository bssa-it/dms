<?php

/**
 * @description
 * This script deletes an unevaluated letter.
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
$u = $_SESSION['dms_user']['userid'];
if (preg_match("/$u/",$_POST['xmlFilename'])) {
    unlink($_POST['xmlFilename']);
}

if (preg_match("/$u/",$_POST['htmlFilename'])) {
    unlink($_POST['htmlFilename']);
}