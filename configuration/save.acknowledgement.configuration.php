<?php

/**
 * @description
 * This script saves the system wide acknowledgement configuration
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   OPEN THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   INSERT THE NEW SETTINGS
$config->acknowledgementconfig->defaultemailsubject         = $_POST['acknowledgementSubject'];
$config->acknowledgementconfig->marginLeft                  = $_POST['marginLeft'];
$config->acknowledgementconfig->marginTop                   = $_POST['marginTop'];
$config->acknowledgementconfig->marginRight                 = $_POST['marginRight'];
$config->acknowledgementconfig->marginBottom                = $_POST['marginBottom'];  

$config->acknowledgementconfig->languages->language[0]['salutation'] = $_POST['afrDefaultSalutation'];
$config->acknowledgementconfig->languages->language[1]['salutation'] = $_POST['engDefaultSalutation'];
$config->acknowledgementconfig->languages->language[2]['salutation'] = $_POST['engDefaultSalutation'];

#   SAVE THE NEW SETTINGS
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK TO THE CONFIGURATION PAGE.
header("location:index.php");