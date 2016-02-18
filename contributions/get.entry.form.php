<?php

/**
 * @description
 * This is the notes for the contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$contributionConfig = simplexml_load_file('inc/config.xml');

$motivationCodes = civicrm_dms_motivation_extension::getMotivationCodes();
$mOpts = '';
$defualtMotivationId = (string)$contributionConfig->defaults->motivation_id;
foreach ($motivationCodes as $t) {
    $selected = ($defualtMotivationId==$t['motivation_id']) ? ' SELECTED':'';
    $mOpts .= '<option value="' . $t['motivation_id'] . '"'.$selected.'>'.$t['description'].' - ' . str_pad($t['motivation_id'], '4','0',STR_PAD_LEFT) . '</option>';
}

define("NEW_IND","&#42;&#42;&#42; new &#42;&#42;&#42;");
$receiptId = (!empty($_GET['receipt_id'])) ? $_GET['receipt_id']:null;

$b = new civicrm_dms_receipt_header_extension($receiptId);
$receiptType = (!empty($b->receipt_type_id)) ? civicrm_dms_receipt_type_extension::getName($b->receipt_type_id):NEW_IND;
$receiptDate = (!empty($b->created_datetime)) ? $b->created_datetime:NEW_IND;
$status = (!empty($b->receipt_status)) ? $b->receipt_status:NEW_IND;


#   ADD THE HTML
require('html/get.entry.form.htm');