<?php

/**
 * @description
 * Description of script
 * 
 * @author      Chezre Fredericks
 * @date_created 01/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

$menu = new menu;


if (empty($_GET['batch_id'])&&empty($_POST['batch_id'])) {
    header('location:receipt.batches.php');
    exit();
}
    
$bid = (!empty($_GET['batch_id'])) ? $_GET['batch_id']:null;
if (!empty($_POST['batch_id'])) $bid = $_POST['batch_id'];

$title = 'Batch &#35;' . str_pad($bid, 5, '0',STR_PAD_LEFT);

$b = new civicrm_dms_batch_header_extension($bid);
$pageHeading = $title . ' <span style="font-size: 15pt">(' . $b->batch_title . ')</span>';

$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
require('html/'.$curScript.'.htm');