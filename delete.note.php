<?php 

/**
 * @description
 * This script removes a note from the CiviCRM system.
 * 
 * @author      Chezre Fredericks
 * @date_created 12/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$apiParams['version'] = 3;
$apiParams['id'] = $_POST['id'];
$result = civicrm_api('Note','delete',$apiParams);

echo "<pre> \n";
print_r($result);
echo "</pre>";