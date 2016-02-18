<?php

/**
 * @description
 * This script retrieves the group totals
 * 
 * @author      Chezre Fredericks
 * @date_created 15/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (empty($_GET['receipt_id'])) {
    $result['message'] = "Invalid receipt number";
    $result['allocations'] = 0;
    print json_encode($result);
    exit();
}

#   BOOTSTRAP
include("../inc/globals.php");
### WRITE THE FOLLOWING FUNCTIONS:
$entries = civicrm_dms_receipt_entry_extension::getAllocations($_GET['receipt_id']);
$result['message'] = 'Entries Loaded';
$result['allocations'] = (!$entries) ? 0:$entries;

print json_encode($result);