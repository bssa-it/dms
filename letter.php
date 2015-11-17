<?php

/**
 * @description
 * This file manages the letter templates in the system.
 * 
 * @author      Chezre Fredericks
 * @date_created 26/11/2013
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   CREATE MENU
$menu = new menu;
$pageHeading = $title = 'Template Manager';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   MERGE FIELDS
$java   = 'var mergeGroups = [];';
$mr = new mergerecord;
$mergeFields = $mr->getMergeRecordHeadings();
$java .= "\n\t\tmergeGroups.push(['acknowledgements','Acknowledgements']);";
$java .= "\n\t\t".'var acknowledgements = [];';
foreach ($mergeFields as $k=>$v) {
    $fname = $k;
    $fkey = '##'.preg_replace('/ /','',strtolower($k)).'##';
    $java .= "\n\t\tacknowledgements.push(['$fkey', '$fname', '$fname']);";   
}
$java .= "\n\t\tacknowledgements.push(['".date("d F Y")."', 'Today', 'Today']);";    


$hiddenTrxns = '';
if (!empty($_POST['trxns'])) {
    foreach ($_POST['trxns'] as $k=>$v) $hiddenTrxns .= '<input type="hidden" name="trxns[]" value="'.$v.'" />'."\n";    
    $pageHeading = 'Select Template';
}

#   ACCESS OPTIONS
$accessOptions = '';
foreach ($GLOBALS['accessLevels'] as $k=>$v) $accessOptions .= '<option value="'.$k.'">'.$v.'</option>';

#   LOAD TEMPLATES
$myTemplates = $GLOBALS['functions']->getMyTemplates();
$templateOptions = '';
$myTemplatesShown = false;
$java .= "\n\t\tvar tpl = [];";
foreach ($myTemplates as $t) {
    $style = ($t['canUpdate']=='N') ? ' style="background-color: #ddd;"':''; 
    $templateOptions .= '<option value="'.$t['tpl_id'].'"'.$style.'>'.$t['tpl_name'].'</option>';
    $java .= "\n\t\ttpl.push([".$t['tpl_id'].",'".$t['canUpdate']."','".$t['tpl_name']."','".$t['tpl_accessLevel']."',";
    $java .= $t['tpl_marginLeft'].",".$t['tpl_marginTop'].",".$t['tpl_marginRight'].",".$t['tpl_marginBottom']."]);";
} 

#   MARGINS
$xmlConfig = simplexml_load_file("inc/config.xml");
$marginLeft = (string)$xmlConfig->acknowledgementconfig->marginLeft;
$marginTop = (string)$xmlConfig->acknowledgementconfig->marginTop;
$marginRight = (string)$xmlConfig->acknowledgementconfig->marginRight;
$marginBottom = (string)$xmlConfig->acknowledgementconfig->marginBottom;

#   DISPLAY HTML
require('html/'.$curScript.'.htm');