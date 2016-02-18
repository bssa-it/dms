<?php

/**
 * @description
 * This script retrieves the denomination list and returns a json object
 * 
 * @author      Chezre Fredericks
 * @date_created 19/06/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("../inc/globals.php");
$b = new civicrm_dms_receipt_header_extension();
$return = $b->getAllOpenReceipts();

print json_encode($return);