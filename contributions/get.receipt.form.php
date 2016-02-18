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

$receiptTypes = civicrm_dms_receipt_type_extension::getAll();
$bOpts = '';
foreach ($receiptTypes as $t) $bOpts .= '<option value="' . $t['id'] . '">'.$t['description'].'</option>';

#   ADD THE HTML
require('html/get.receipt.form.htm');