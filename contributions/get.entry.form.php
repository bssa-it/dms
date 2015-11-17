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

$paymentTypes = civicrm_dms_batch_entry_extension::getPaymentInstruments();
$pOpts = '';
foreach ($paymentTypes as $t) $pOpts .= '<option value="' . $t['value'] . '">'.$t['label'].'</option>';

#   ADD THE HTML
require('html/get.entry.form.htm');