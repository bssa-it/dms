<?php

/**
 * @description
 * This script saves the authorisation settings for the system wide config file.
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   UPDATE THE AUTHORISATION SETTINGS
$config->authorisation->delete      = $_POST['aut_delete'];
$config->authorisation->update      = $_POST['aut_update'];
$config->authorisation->insert      = $_POST['aut_insert'];
$config->authorisation->select      = $_POST['aut_select'];
$config->authorisation->admin       = $_POST['aut_admin'];      

#   SAVE THE NEW SETTINGS 
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK TO THE CONFIGURATIONS PAGE
header("location:index.php");