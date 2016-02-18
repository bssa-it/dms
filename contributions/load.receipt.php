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
define("NEW_IND","&#42;&#42;&#42; new &#42;&#42;&#42;");

if (empty($_GET['receipt_id'])&&empty($_POST['receipt_id'])) {
    header('location:receipts.php');
    exit();
}
    
$bid = (!empty($_GET['receipt_id'])) ? $_GET['receipt_id']:null;
if (!empty($_POST['receipt_id'])) $bid = $_POST['receipt_id'];

$title = 'Receipt &#35;' . str_pad($bid, 5, '0',STR_PAD_LEFT);

$b = new civicrm_dms_receipt_header_extension($bid);
$pageHeading = $title . ' <span style="font-size: 15pt">(' . $b->receipt_title . ')</span>';

$receiptTotal = $b->receipt_total;
$receiptType = (!empty($b->receipt_type_id)) ? civicrm_dms_receipt_type_extension::getName($b->receipt_type_id):NEW_IND;

$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
require('html/'.$curScript.'.htm');