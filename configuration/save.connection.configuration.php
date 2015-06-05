<?php

/**
 * @description
 * This script saves the connection settings for the DMS system.
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$config = simplexml_load_file("../inc/config.xml");

$config->databaseConnection->host         = $_POST['dms_host'];
$config->databaseConnection->password     = $_POST['dms_password'];
$config->databaseConnection->username     = $_POST['dms_username'];
$config->databaseConnection->database     = $_POST['dms_database'];
$config->joomlaConnection->host           = $_POST['jml_host'];
$config->joomlaConnection->password       = $_POST['jml_password'];
$config->joomlaConnection->username       = $_POST['jml_username'];
$config->joomlaConnection->database       = $_POST['jml_database'];
$config->civiConnection->host             = $_POST['civ_host'];
$config->civiConnection->password         = $_POST['civ_password'];
$config->civiConnection->username         = $_POST['civ_username'];
$config->civiConnection->database         = $_POST['civ_database']; 

$config->civicrmApi->crmConfigFile        = $_POST['apiCrmConfig'];
$config->civicrmApi->crmCoreConfigFile    = $_POST['apiCrmCoreConfig'];

file_put_contents('../inc/config.xml',$config->asXML());

header("location:index.php");