<?php

/**
 * @description
 * This script updates the civicrm and DMS version flag.
 * 
 * @author      Chezre Fredericks
 * @date_created 26/03/2015
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   UPDATE THE CIVICRM OPTION GROUP SETTINGS
$config->versions->civicrm = $_POST['civicrmVersion'];
$config->versions->dms = $_POST['dmsVersion'];

#   SAVE THE NEW SETTINGS
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK TO THE CONFIGURATION PAGE
header("location:index.php");