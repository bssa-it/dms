<?php

/**
 * @description
 * This script is a report of where contacts reside.
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = 'Donor Positioning System';
$title = $pageHeading;
$config = simplexml_load_file("inc/config.xml");
$apiKey = (string)$config->googleApiKey;
$java = "\n\t\t" . '';

#   byLocation
$sql = 'SELECT DISTINCT geo_code_1,geo_code_2 FROM `civicrm_address` WHERE `geo_code_1` IS NOT NULL AND `geo_code_2` IS NOT NULL';
$result = $GLOBALS['functions']->GetCiviDataFromSQL($sql);
if (!empty($result)) foreach ($result as $r) $java .= "\n\t\tbyLocation.push({lat: '".$r['geo_code_1']."',lon: '".$r['geo_code_2']."'});";

#   byDepartment
$sql = "SELECT DISTINCT SUBSTR(`organisation_id`,1,1) `Dept`, geo_code_1,geo_code_2 FROM `civicrm_address`  A
INNER JOIN `civicrm_dms_contact_reporting_code` R ON A.contact_id = R.contact_id
WHERE `geo_code_1` IS NOT NULL AND `geo_code_2` IS NOT NULL";
$departments = $GLOBALS['functions']->GetCiviDataFromSQL($sql);
if (!empty($departments)) foreach ($departments as $r) $java .= "\n\t\tbyDepartment.push({dep: '".$r['Dept']."',lat: '".$r['geo_code_1']."',lon: '".$r['geo_code_2']."'});";

#   byValue
$sql = "SELECT geo_code_1,geo_code_2,SUM(`trns_amount_received`) `total` FROM `dms`.`dms_transaction` AS `T`
INNER JOIN `civicrm_address` AS `A` ON `A`.`contact_id` = `T`.`civ_contact_id` AND `A`.`is_primary` = 1
WHERE `A`.`geo_code_1` IS NOT NULL AND `A`.`geo_code_2` IS NOT NULL
GROUP BY 1,2";
$value = $GLOBALS['functions']->GetCiviDataFromSQL($sql);
if (!empty($value)) foreach ($value as $r) $java .= "\n\t\tbyValue.push({lat: '".$r['geo_code_1']."',lon: '".$r['geo_code_2']."',total: '".$r['total']."'});";

#   ADD TEMPLATE
require('html/'.$curScript.'.htm');