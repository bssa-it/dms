<?php

/**
 * @description
 * This script saves the changes to the list of SCO import files in the system wide config file.
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   ADD/UPDATE THE IMPORT FILE NAME
$fileName          = preg_replace('/_/','',$_POST['newImportFile']);
if (isset($config->importFiles->$fileName)) {
    $config->importFiles->$fileName = $_POST['newImportFile'];
} else {
    $config->importFiles->addChild($fileName,$_POST['newImportFile']);   
}

#   SAVE THE CHANGES
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK TO THE CONFIGURATION PAGE
header("location:index.php");