<?php

/**
 * @description
 * This script updates the email settings in the system wide configuration file
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   LOAD THE CONFIG FILE
$config = simplexml_load_file("../inc/config.xml");

#   UPDATE THE EMAIL SETTINGS
$config->emailconfig->emailhost         = $_POST['emailHost'];
$config->emailconfig->address           = $_POST['emailAddress'];
$config->emailconfig->name              = $_POST['emailFromName'];
$config->emailconfig->password          = $_POST['emailPassword'];
$config->emailconfig->domain            = $_POST['emailDomain'];      
$config->emailconfig->zimbraPreAuthKey  = $_POST['zimbraPreAuthKey']; 
$config->emailconfig->mailchimpApiKey   = $_POST['mailchimpApiKey'];
$config->emailconfig->mandrill->host    = $_POST['mandrillHost'];
$config->emailconfig->mandrill->port    = $_POST['mandrillPort'];
$config->emailconfig->mandrill->smtpUsername = $_POST['mandrillUsername'];
$config->emailconfig->mandrill->smtpPassword = $_POST['mandrillApiKey'];

#   SAVE THE CHANGES
$saveResult = file_put_contents('../inc/config.xml',$config->asXML());

#   GO BACK TO THE CONFIGURATION PAGE
header("location:index.php");