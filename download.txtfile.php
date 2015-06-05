<?php

/**
 * @description
 * Description of script
 * 
 * @author      Chezre Fredericks
 * @date_created 01/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");

if (empty($_GET['f'])&&!file_exists($_GET['f'])) exit();

$txtFileContent = file_get_contents($_GET['f']);
$filename = basename($_GET['f']);
header("Content-Type:text/plain");
header('Content-Disposition: attachment; filename='.$filename);
header("Pragma: no-cache"); 
header("Expires: 0");
echo $txtFileContent;