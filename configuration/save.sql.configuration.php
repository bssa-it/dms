<?php

/**
 * @description
 * This script updates the sql settings in the system wide config file  
 * 
 * @author      Chezre Fredericks
 * @date_created 20/01/2015
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   UPDATE THE SQL SETTINGS
$config->showSql['value'] = $_POST['showSql'];

#   SAVE THE NEW SETTINGS
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK THE CONFIGURATION PAGE
header("location:index.php");