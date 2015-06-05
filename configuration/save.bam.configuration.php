<?php

/**
 * @description
 * This script saves the BAM settings for the system wide config file.
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   UPDATE THE BAM SETTINGS
$config->bam->civiMembershipTypeId = $_POST['bamMemTypeId'];   

#   SAVE THE NEW SETTINGS
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK THE CONFIGURATION PAGE
header("location:index.php");