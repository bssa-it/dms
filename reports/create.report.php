<?php

/**
 * @description
 * This script creates the body of the report.
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
include("../inc/globals.php");
if (empty($_POST['filename'])) {
    echo "No Report selected";
    exit();
}
if (!empty($_SESSION['dms_report'])) unset($_SESSION['dms_report']);
$reportId = $_POST['filename'];
$config = simplexml_load_file($reportId);
$javascript = $css = $graphSelected = $reportContent = '';
$bodySelected = 'class="selectedNav"';

if (count($config->javascriptIncludes->children())>0) {
    foreach ($config->javascriptIncludes->children() as $j) {
        $javascript .= "\n" . '<script type="text/javascript" lang="javascript" src="scripts/' . $j['filename'] .'"></script>';
    }
}
if (count($config->cssIncludes->children())>0) {
    foreach ($config->cssIncludes->children() as $j) {
        $css .= "\n" . '<link rel="stylesheet" type="text/css" href="css/' . $j['filename'] .'" />';
    }
}

$chartPhp = '';
if (!empty($config->chartPhp)) {
    $graphSelected = 'class="selectedNav"';
    $bodySelected = '';
    $chartPhp = dirname($_POST['filename']) . '/'. $config->chartPhp;
}

$bodyPhp = dirname($_POST['filename']) . '/'. $config->tablePhp;
include('html/report.frame.htm');