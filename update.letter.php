<?php

/**
 * @description
 * This script updates the XML file in the unevaluated directory for a specific letter
 * 
 * @author      Chezre Fredericks
 * @date_created 27/03/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
/*
error_reporting(E_ALL);
ini_set('display_errors', '1');*/

#   SAVE XML LETTER DETAILS
$xml = new SimpleXMLElement('<xml/>');
$letterSettings = $xml->addChild('letter');
$letterSettings->addChild('tpl_marginLeft', $_POST['tpl_marginLeft']);
$letterSettings->addChild('tpl_marginTop', $_POST['tpl_marginTop']);
$letterSettings->addChild('tpl_marginRight', $_POST['tpl_marginRight']);
$letterSettings->addChild('tpl_marginBottom', $_POST['tpl_marginBottom']);
$letterSettings->addChild('filename',$_POST['htmlFilename']);
$letterSettings->addChild('ready',$_POST['ready']);
$letterSettings->addChild('email',$_POST['email']);
$letterSettings->addChild('method',$_POST['method']);
$letterSettings->addChild('emailname',$_POST['emailname']);
$letterSettings->addChild('subject',$_POST['subject']);
$letterSettings->addChild('impersonate',$_POST['impersonate']);
$letterSettings->addChild('department',$_POST['department']);
$letterSettings->addChild('contact_id',$_POST['contact_id']);
$letterSettings->addChild('contribution_id', $_POST['contribution_id']);
$letterSettings->addChild('region', $_POST['region']);
$letterSettings->addChild('addSignature','N');
file_put_contents($_POST['xmlFilename'],$xml->asXML());

#   SAVE HTML LETTER
file_put_contents($_POST['htmlFilename'], urldecode($_POST['letter']));