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

$batchTypes = civicrm_dms_batch_type_extension::getAll();
$bOpts = '';
foreach ($batchTypes as $t) $bOpts .= '<option value="' . $t['id'] . '">'.$t['description'].'</option>';

#   ADD THE HTML
require('html/get.batch.form.htm');