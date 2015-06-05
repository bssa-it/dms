<?php

/**
 * @description
 * This script builds the sql and retrieves the contacts from the Archive DB.
 * 
 * @author      Chezre Fredericks
 * @date_created 06/03/2015
 * @Changes
 * 
 */

$where  = '';
$sowerGroupId = (string)$GLOBALS['xmlConfig']->civiGroups->sower;
if (count($searchVariables)>0) {
    $ordersJoin = '';
    $remarksJoin = '';
    $bamJoin = '';
    if (isset($searchVariables['srch_donorNumber'])) {
        $where = ' WHERE dnr_no = ' . $searchVariables['srch_donorNumber'];
    } elseif (isset($searchVariables['srch_civiId'])) {
        $where = ' WHERE civ_contact_id = ' . $searchVariables['srch_civiId'];
    } elseif (isset($searchVariables['srch_recordId'])) {
        $where = ' WHERE civ_contact_id = ' . $searchVariables['srch_recordId'] .' OR dnr_no = ' . $searchVariables['srch_recordId'];
    } else {
        $where =  ' WHERE ';
        foreach ($searchVariables as $k=>$v) {
            switch ($k) {
                case 'srch_firstName':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "dnr_inits LIKE '%$v%'";
                    break;
                case 'srch_lastName':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "dnr_name LIKE '%$v%'";
                    break;
                case 'srch_title':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "dnr_title LIKE '%$v%'";
                    break;
                case 'srch_AddressLine1':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(dnr_addr LIKE '%$v%' OR ";
                    $where .= "add_addr LIKE '%$v%')";
                    break;
                case 'srch_AddressLine2':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(dnr_addr LIKE '%$v%' OR ";
                    $where .= "add_addr LIKE '%$v%')";
                    break;
                case 'srch_AddressLine3':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(dnr_addr LIKE '%$v%' OR ";
                    $where .= "add_addr LIKE '%$v%')"; 
                    break;
                case 'srch_City':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(dnr_addr LIKE '%$v%' OR ";
                    $where .= "add_addr LIKE '%$v%')"; 
                    break;
                case 'srch_PostalCode':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(dnr_post_cd LIKE '%$v%' OR add_post_cd LIKE '%$v%')"; 
                    break;
                case 'srch_donorName':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(dnr_name LIKE '%$v%' OR dnr_inits LIKE '%$v%')";
                    break;
                case 'srch_contactNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "(con_faxNo LIKE '%$cleanNumber%' OR con_telNo LIKE '%$cleanNumber%' OR con_cellNo LIKE '%$cleanNumber%')";
                    break;
                case 'srch_cellNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "con_cellNo LIKE '%$cleanNumber%'";
                    break;
                case 'srch_telNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "con_telNo LIKE '%$cleanNumber%'";
                    break;
                case 'srch_faxNo':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $cleanNumber = $GLOBALS['functions']->cleanContactNumber($v);
                    $where .= "con_faxNo LIKE '%$cleanNumber%'";
                    break;
                case 'srch_email':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "e_mail LIKE '%$v%'";
                    break;
                case 'srch_donorDeleted':
                    if ($v=='A') break;
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "dnr_deleted = '$v'";   
                    break;
                case 'srch_reminderMonth':
                    if (empty($v)) break;
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "dnr_rmnd_mnth = '$v'";   
                    break;
                case 'srch_region':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "org_subregion = '$v'";   
                    break;
                case 'srch_remarks':
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "drm_text like '%$v%'";
                    $remarksJoin = ' LEFT JOIN dms_remark ON drm_dnr_no = dnr_no';   
                    break; 
                case 'srch_bamRefNo':
                    if ($where != ' WHERE ') $where .= ' AND ';                    
                    $where .= "bam_ref_no like '%$v%'";
                    $bamJoin = ' INNER JOIN dms_bam ON bam_dnr_no = dnr_no';   
                    break;
                case 'srch_bamOnly':
                    $bamJoin = ' INNER JOIN dms_bam ON bam_dnr_no = dnr_no';   
                    break;
                case 'srch_bamCertificatePrinted':
                    break;
                case 'srch_categoryId':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "dnr_cntr_tp = '$v'";
                    break;
                case 'srch_orgId':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "dnr_org_id = '$v'";
                    break;
                case 'srch_contactType':
                    break;
                case 'srch_language':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $language = ($v!='af_ZA') ? 'E':'A';
                    $where .= "dnr_lang = '$v'";
                    break;
                case 'srch_allAddressLines':
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "(dnr_addr LIKE '%$v%' OR ";
                    $where .= "add_addr LIKE '%$v%')"; 
                    break;
                case 'srch_birthday':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "dnr_birth_date LIKE '%$v%'";
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
                    $where .= "dnr_last_date <= '$v'";
                    break;
                case 'start_srch_lastContributionDate':
                    if ($v=='') break;
                    if ($where != ' WHERE ') $where .= ' AND ';
                    $where .= "dnr_last_date >= '$v'";
                    break;
                case 'srch_group':
                    if (empty($v)) break;
                    foreach ($v as $g) {
                        if ($g==$sowerGroupId) {
                            if ($where != ' WHERE ') $where .= ' AND ';
                            $where .= "dnr_sower = 'Y'";
                        }
                    }
                    break;  
                   
            }
        }
    }
    $sql = "
SELECT 
        DISTINCT org_subregion `region`, 
        civ_contact_id `contact_id`, 
        dnr_no `dnr_no`, 
        TRIM(CONCAT(IFNULL(dnr_name,''),',',IFNULL(dnr_inits,''))) `display_name`,  
        TRIM(CONCAT('t: ',IFNULL(tel_no,''),' f: ',IFNULL(fax_no,''),' c: ',IFNULL(cell_no,''))) `phone`, 
        e_mail `email`, 
        dnr_deleted `is_deleted`, 
        dnr_addr `address`  
FROM 
    `dms_donor` 
    LEFT JOIN `dms_profile` ON `add_dnr_no` = `dnr_no`  
    LEFT JOIN `dms_cleanContactNumbers` ON `con_dnr_no` = `dnr_no`  
    LEFT JOIN dms_orgunit O on rep_org_id = org_id  
    $remarksJoin $bamJoin $ordersJoin
$where 
ORDER BY `dnr_name`,`dnr_inits`;";

    $donors = $GLOBALS['db']->select($sql);
}