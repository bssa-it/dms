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
$config = simplexml_load_file("../inc/classes.config.xml");

#   ADD/UPDATE THE NEW CLASS
$className          = preg_replace('/ /','',$_POST['className']);
$classFilename      = preg_replace('/ /','',$_POST['classFilename']);

$block = (!empty($_POST['isExtension'])&&$_POST['isExtension']=='Y') ? 'extensions':'includes';

if (isset($config->$block->$className)) {
    $config->$block->$className = $classFilename;
    $return['result'] = 'class updated';
} else {
    $config->$block->addChild($className,$classFilename);
    $return['result'] = 'class added';
}

#   SAVE THE CHANGES 
$saveResult = file_put_contents('../inc/classes.config.xml',$config->asXML());
echo json_encode($return);