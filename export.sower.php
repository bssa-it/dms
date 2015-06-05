<?php

/**
 * @description
 * This script exports any array stored in the $_SESSION['export'] variable
 * 
 * @author      Chezre Fredericks
 * @date_created 21/01/2014
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");

$filename = 'sower-'.date("YmdHis").'.txt';
$sql = "SELECT 
	DISTINCT org_region `region`, 
	c.id `contact_id`, 
	external_identifier `dnr_no`, 
	sort_name `display_name`, 
	p.phone `phone`, 
	e.email `email`, 
	CASE WHEN is_deleted=1 THEN 'Y' ELSE 'N' END `is_deleted`, 
	TRIM(
	CONCAT(
	    TRIM(IFNULL(a.street_address,'')),' ',
	    TRIM(IFNULL(a.supplemental_address_1,'')),' ',
	    TRIM(IFNULL(a.supplemental_address_2,'')),' ',
	    TRIM(IFNULL(a.city,'')),' ',
	    TRIM(IFNULL(a.postal_code,''))
	)
	) `address`
FROM civicrm_group_contact G
INNER JOIN civicrm_contact c ON c.id = G.`contact_id`
LEFT JOIN `civicrm_phone` p ON c.`id` = p.`contact_id` AND p.is_primary = 1
LEFT JOIN `civicrm_address` a ON c.`id` = a.`contact_id` AND a.is_primary = 1
LEFT JOIN `civicrm_email` e ON c.`id` = e.`contact_id` AND e.is_primary = 1
LEFT JOIN `civicrm_dms_contact_reporting_code` r ON c.`id` = r.`contact_id`
LEFT JOIN `civicrm_dms_organisation` org ON org.org_id = r.organisation_id
WHERE group_id = 1 AND `status` = 'Added'
ORDER BY sort_name;";
$result = $GLOBALS['civiDb']->select($sql);
if (!$result) {
    echo "an error occured retrieving data";
    echo "<pre />";
    print_r($sql);
} else {
    $GLOBALS['functions']->exportArrayToCSV($filename,$result,true);
}