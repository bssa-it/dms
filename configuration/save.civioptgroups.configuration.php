<?php

/**
 * @description
 * This script updates the civicrm option groups settings in the system wide config file.
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   UPDATE THE CIVICRM OPTION GROUP SETTINGS
$config->civiOptionGroups->phonetypes = $_POST['phoneTypesOptGroup'];
$config->civiOptionGroups->languageCiviOptGroupId = $_POST['languageOptGroup'];
$config->civiOptionGroups->titles = $_POST['titlesOptGroup'];
$config->civiOptionGroups->gender = $_POST['genderOptGroup'];
$config->defaultPreferences->communicationMethodCiviOptGroupId = $_POST['communicationOptGroup'];


#   SAVE THE NEW SETTINGS
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK TO THE CONFIGURATION PAGE
header("location:index.php");