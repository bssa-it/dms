<?php

/**
 * @description
 * This script builds the quick search sql for retrieving contacts from the CiviCRM database.
 * 
 * @author      Chezre Fredericks
 * @date_created 06/03/2015
 * @Changes
 * 
 */

$birthday = $_POST['srch_birthday'];
if ($_POST['srch_orgId']==='9') {
    $department = $exists = '';
} else {
    $department = "AND substr(organisation_id,1,1) = '{$_POST['srch_orgId']}' ";
    $exists = 'NOT';
}
$phoneOnlyPrimary = '(p.`is_primary` = 1 OR p.is_primary IS NULL)';
$addressOnlyPrimary = '(a.`is_primary` = 1 OR a.is_primary IS NULL)';
$emailOnlyPrimary = '(e.`is_primary` = 1 OR e.is_primary IS NULL)';
$sql = "SELECT 
    DISTINCT org_region `region`, 
    C.id `contact_id`, 
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
    ) `address` "
        . " FROM civicrm_contact C "
        . "INNER JOIN civicrm_dms_contact_reporting_code R ON contact_id = C.id "
        . "LEFT JOIN `civicrm_dms_organisation` org ON org.org_id = R.organisation_id "
        . "LEFT JOIN `civicrm_phone` p ON C.`id` = p.`contact_id` "
        . "LEFT JOIN `civicrm_address` a ON C.`id` = a.`contact_id` "
        . "LEFT JOIN `civicrm_email` e ON C.`id` = e.`contact_id` "
        . "WHERE birth_date LIKE '%$birthday%' "
        . $department
        . ' AND '. $exists .' EXISTS (SELECT contact_id FROM `civicrm`.`civicrm_membership` WHERE `contact_id` = C.id and membership_type_id = 1) '
        . " AND $phoneOnlyPrimary AND $addressOnlyPrimary and $emailOnlyPrimary "
        . "ORDER BY sort_name;";
$GLOBALS['functions']->showSql($sql);
$_SESSION['dmsDonorSearchResultset'] = $GLOBALS['functions']->GetCiviDataFromSQL($sql);
$_SESSION['dmsDonorSearchCriteria'] = $_POST;
$_SESSION['dmsDonorSearchCriteria']['srch_database'] = 'civicrm';