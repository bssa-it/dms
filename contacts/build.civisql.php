<?php

/**
 * @description
 * This file builds an sql query for retrieving contacts from the CiviCRM database.
 * 
 * @author      Chezre Fredericks
 * @date_created 06/03/2015
 * @Changes
 * 4.2.1 - 17/03/2015 - Chezre Fredericks:
 * Added debit order account search temporarily from the dms DB
 * 
 */


$where  = '';
if (count($searchVariables)>0) {
    $ordersJoin = '';
    $remarksJoin = '';
    $membershipsJoin = '';
    $groupJoin = '';
    $phoneOnlyPrimary = '(p.`is_primary` = 1 OR p.is_primary IS NULL)';
    $addressOnlyPrimary = '(a.`is_primary` = 1 OR a.is_primary IS NULL)';
    $emailOnlyPrimary = '(e.`is_primary` = 1 OR e.is_primary IS NULL)';
    if (isset($searchVariables['srch_donorNumber'])) {
        $where = ' WHERE external_identifier = ' . $searchVariables['srch_donorNumber'];
    } elseif (isset($searchVariables['srch_civiId'])) {
        $where = ' WHERE c.id = ' . $searchVariables['srch_civiId'];
    } elseif (isset($searchVariables['srch_recordId'])) {
        $where = ' WHERE c.id = ' . $searchVariables['srch_recordId'] .' OR external_identifier = ';
        $where .= $searchVariables['srch_recordId'];
    } else {
        $where =  ' WHERE ';
        foreach ($searchVariables as $k=>$v) {
            switch ($k) {
                case 'srch_firstName':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "first_name LIKE '%$v%'";
                    break;
                case 'srch_lastName':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "last_name LIKE '%$v%'";
                    break;
                case 'srch_nickName':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "nick_name LIKE '%$v%'";
                    break;
                case 'srch_title':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "title_12 LIKE '%$v%'";
                    break;
                case 'srch_AddressLine1':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "a.street_address LIKE '%$v%'";
                    $addressOnlyPrimary = '';
                    break;
                case 'srch_AddressLine2':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "a.supplemental_address_1 LIKE '%$v%'";
                    $addressOnlyPrimary = '';
                    break;
                case 'srch_AddressLine3':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "a.supplemental_address_2 LIKE '%$v%'";
                    $addressOnlyPrimary = '';
                    break;
                case 'srch_City':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "a.city LIKE '%$v%'"; 
                    $addressOnlyPrimary = '';
                    break;
                case 'srch_PostalCode':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "a.postal_code LIKE '%$v%'";
                    $addressOnlyPrimary = ''; 
                    break;
                case 'srch_donorName':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "display_name LIKE '%$v%'";
                    break;
                case 'srch_contactNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "(phone LIKE '%$cleanNumber%')";
                    $phoneOnlyPrimary = '';
                    break;
                case 'srch_cellNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "(phone LIKE '%$cleanNumber%' AND phone_type_id = 2)";
                    $phoneOnlyPrimary = '';
                    break;
                case 'srch_telNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "(phone LIKE '%$cleanNumber%' AND phone_type_id = 1)";
                    $phoneOnlyPrimary = '';
                    break;
                case 'srch_faxNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "(phone LIKE '%$cleanNumber%' AND phone_type_id = 3)";
                    $phoneOnlyPrimary = '';
                    break;
                case 'srch_email':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "e.email LIKE '%$v%'";
                    $emailOnlyPrimary = '';
                    break;
                case 'srch_donorDeleted':
                    if ($v=='A') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $deletedFlag = ($v=='Y') ? 1:0;                    
                    $where .= "is_deleted = $deletedFlag";   
                    break;
                case 'srch_region':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "org_region = '$v'";   
                    break;
                case 'srch_group':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= '(';
                    $firstGroup = true;
                    foreach ($v as $g) {
                        $where .= "group_id = '$g'";
                        $where .= ($firstGroup) ? '':' OR ';
                        $firstGroup = false;
                    }
                    $where .= ')';
                    $groupJoin = " INNER JOIN `civicrm_group_contact` GC on GC.contact_id = c.id  and GC.status = 'Added' ";   
                    break;
                case 'srch_remarks':
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "n.note like '%$v%'";
                    $remarksJoin = " LEFT JOIN `civicrm_note` n 
                                        ON c.`id` = n.`entity_id` AND n.entity_table = 'civicrm_contact'";   
                    break;
                case 'srch_bamRefNo':
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "mo.bam_club__15 LIKE '%$v%'";
                    $membershipsJoin = " INNER JOIN `civicrm_membership` m 
                                            ON c.id = m.contact_id AND m.membership_type_id = $bamMembershipTypeId
                                        INNER JOIN `civicrm_value_membership_custom_data_4` mo ON mo.entity_id = m.id";   
                    break;
                case 'srch_bamOnly':
                    $membershipsJoin = " INNER JOIN `civicrm_membership` m 
                                            ON c.id = m.contact_id AND m.membership_type_id = $bamMembershipTypeId
                                        INNER JOIN `civicrm_value_membership_custom_data_4` mo ON mo.entity_id = m.id";   
                    break;
                case 'srch_bamCertificatePrinted':
                    if (empty($v)&&$v!=='0') break;
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "mo.bam_certificate_printed_16 = $v";
                    $membershipsJoin = " INNER JOIN `civicrm_membership` m 
                                            ON c.id = m.contact_id AND m.membership_type_id = $bamMembershipTypeId
                                        INNER JOIN `civicrm_value_membership_custom_data_4` mo ON mo.entity_id = m.id";
                    break;
                case 'srch_categoryId':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "category_id = '$v'";
                    break;
                case 'srch_orgId':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $searchOrg = (preg_match('/\*/i',$v)) ? preg_replace('/\*/','%',$v):'%'.$v.'%';
                    $where .= "organisation_id LIKE '$searchOrg'";
                    break;
                case 'srch_contactType':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(contact_type = '$v' OR contact_sub_type LIKE '%$v%')";
                    break;
                case 'srch_language':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "preferred_language = '$v'";
                    break;
                case 'srch_reminderMonth':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "reminder_month = '$v'";
                    break;
                case 'srch_allAddressLines':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(a.street_address LIKE '%$v%' OR a.supplemental_address_1 LIKE '%$v%' OR a.supplemental_address_2 LIKE '%$v%' OR a.city LIKE '%$v%')";
                    $addressOnlyPrimary = '';
                    break;
                case 'srch_birthday':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "birth_date LIKE '%$v%'";
                    break;
                case 'srch_acct_no':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "ordr_acct_no LIKE '%$v%'";
                    $ordersJoin = " INNER JOIN `dms`.`dms_orders` `dmsOrders` ON c.external_identifier = `ordr_dnr_no`";
                    break;
                case 'srch_bnk_id':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "ordr_bnk_id LIKE '%$v%'";
                    $ordersJoin = " INNER JOIN `dms`.`dms_orders` `dmsOrders` ON c.external_identifier = `ordr_dnr_no`";
                    break;
                case 'srch_ref':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "ordr_ref LIKE '%$v%'";
                    $ordersJoin = " INNER JOIN `dms`.`dms_orders` `dmsOrders` ON c.external_identifier = `ordr_dnr_no`";
                    break;
                case 'end_srch_lastContributionDate':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "last_contribution_date <= '$v'";
                    break;
                case 'start_srch_lastContributionDate':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "last_contribution_date >= '$v'";
                    break;
            }
        }
    }

    if (!empty($phoneOnlyPrimary)) {
        if ($where != ' WHERE ') $where .= ' AND ';
        $where .= $phoneOnlyPrimary; 
    }

    if (!empty($addressOnlyPrimary)) {
        if ($where != ' WHERE ') $where .= ' AND ';
        $where .= $addressOnlyPrimary; 
    }

    if (!empty($emailOnlyPrimary)) {
        if ($where != ' WHERE ') $where .= ' AND ';
        $where .= $emailOnlyPrimary; 
    }

    $sql = "
SELECT 
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
    LEFT JOIN `civicrm_phone` p ON c.`id` = p.`contact_id`
    LEFT JOIN `civicrm_address` a ON c.`id` = a.`contact_id`
    LEFT JOIN `civicrm_email` e ON c.`id` = e.`contact_id`
    LEFT JOIN `civicrm_dms_contact_reporting_code` r ON c.`id` = r.`contact_id`
    LEFT JOIN `civicrm_dms_organisation` org ON org.org_id = r.organisation_id
    LEFT JOIN `civicrm_dms_contact_other_data` o ON c.`id` = o.`contact_id`
    $remarksJoin 
    $membershipsJoin 
    $ordersJoin
    $groupJoin
$where
ORDER BY sort_name;";
    $donors = $GLOBALS['functions']->GetCiviDataFromSQL($sql);
    
    
} 