<?php

/**
 * @description
 * This script builds the sql for group contact retrieval
 * 
 * @author      Chezre Fredericks
 * @date_created 15/04/2015
 * @Changes
 * 
 */
$groupId = $_GET['group'];
$sowerGroupId = (string)$GLOBALS['xmlConfig']->civiGroups->sower;
$sowerLocationTypeId = (string)$GLOBALS['xmlConfig']->sowerLocationTypeId['id'];
$addressOn = ($groupId==$sowerGroupId) ? 'AND a.location_type_id = '.$sowerLocationTypeId : 'AND a.is_primary = 1';
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
LEFT JOIN `civicrm_address` a ON c.`id` = a.`contact_id` $addressOn
LEFT JOIN `civicrm_email` e ON c.`id` = e.`contact_id` AND e.is_primary = 1
LEFT JOIN `civicrm_dms_contact_reporting_code` r ON c.`id` = r.`contact_id`
LEFT JOIN `civicrm_dms_organisation` org ON org.org_id = r.organisation_id
WHERE group_id = $groupId AND `status` = 'Added'
        ORDER BY sort_name;";
$GLOBALS['functions']->showSql($sql);
$_SESSION['dmsDonorSearchResultset'] = $GLOBALS['functions']->GetCiviDataFromSQL($sql);
$_SESSION['dmsDonorSearchCriteria'] = $_GET;
$_SESSION['dmsDonorSearchCriteria']['srch_database'] = 'civicrm';