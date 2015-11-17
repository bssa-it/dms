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

if ($_POST['srch_bamOnly']=='Y') $bam = "INNER JOIN `civicrm_membership` m ON c.id = m.contact_id AND m.membership_type_id = $bamMembershipTypeId ";
$donorNo = $contactId = $_POST['qck_search'];
$searchName = (preg_match('/\*/i',$_POST['qck_search'])) ? preg_replace('/\*/','%',$_POST['qck_search']):'%'.$_POST['qck_search'].'%';
$deleted = ($_POST['srch_donorDeleted']=='Y') ? 1:0;
$whereDeleted = ($_POST['srch_donorDeleted']=='A') ? '':"AND is_deleted = $deleted";
if (is_numeric($_POST['qck_search'])) {
    $where =  "(external_identifier = $donorNo OR c.id = $contactId)";
    $whereDeleted = '';
} else {
    $where =  "sort_name like '$searchName'";
}



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
        FROM 
            `civicrm_contact` c 
            left JOIN `civicrm_phone` p ON c.`id` = p.`contact_id` and p.is_primary = 1
            left JOIN `civicrm_address` a ON c.`id` = a.`contact_id` and a.is_primary = 1
            left JOIN `civicrm_email` e ON c.`id` = e.`contact_id` and e.is_primary = 1
            left JOIN `civicrm_dms_contact_reporting_code` r ON c.`id` = r.`contact_id`
            left JOIN `civicrm_dms_organisation` org ON org.org_id = r.organisation_id
        WHERE
            $where $whereDeleted
        ORDER BY sort_name;";
$GLOBALS['functions']->showSql($sql);
$_SESSION['dmsDonorSearchResultset'] = $GLOBALS['functions']->GetCiviDataFromSQL($sql);
$_SESSION['dmsDonorSearchCriteria'] = $_POST;
$_SESSION['dmsDonorSearchCriteria']['srch_database'] = 'civicrm';