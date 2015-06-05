<?php

/**
 * @description
 * This script updates the classes loaded by the bootstrap.  The class file names are stored 
 * in the config.xml file.
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   ADD/UPDATE THE NEW CLASS
$className          = preg_replace('/ /','',$_POST['className']);
$classFilename      = preg_replace('/ /','',$_POST['classFilename']);
if (isset($config->classes->$className)) {
    $config->classes->$className = $classFilename;
} else {
    $config->classes->addChild($className,$classFilename);   
}

#   SAVE THE CHANGES 
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK TO THE CONFIGURATION PAGE
header("location:index.php");