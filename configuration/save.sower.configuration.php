<?php

/**
 * @description
 * This script saves the sower settings for the system
 * 
 * @author      Chezre Fredericks
 * @date_created 26/03/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$config = simplexml_load_file("../inc/config.xml");
$config->sowerLocationTypeId['id'] = $_POST['sowerAddressTypeId']; 
file_put_contents('../inc/config.xml',$config->asXML());

header("location:index.php");