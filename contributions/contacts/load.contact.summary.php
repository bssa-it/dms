<?php

/**
 * @description
 * This is the summary of the contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 13/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if ((empty($_SESSION['dmsDonorSearchCriteria']['srch_database'])&&empty($_GET['s']))
        ||!isset($_GET['d'])||empty($_GET['d'])) $GLOBALS['functions']->goToIndexPage();

#   SET VITAL VARIABLES
$database = (!empty($_GET['s'])) ? $_GET['s'] : $_SESSION['dmsDonorSearchCriteria']['srch_database'];
$dnrNo = $_GET['d'];

#   LOAD DONOR DETAIL
$groupsDiv = '';
$privacyRows = '';
switch ($database) {
    case "archive":
        $dnr = new donor();
        if (!$dnr->Load($dnrNo)) $GLOBALS['functions']->goToIndexPage();
        $contactType = 'archive';
        $contactSuperType = 'archive';
        $personalDetailsHeading = 'Archive Record Details';
        $isDeleted = ($dnr->dnr_deleted=='Y');
        #  Contact Details
        $a = trim(substr($dnr->dnr_addr,0,30));
        $contactDetailsArray['Address'] = (!empty($a)) ? $a:'';
        $a = trim(substr($dnr->dnr_addr,30,30));
        if (!empty($a)) $contactDetailsArray['Address'] .= ($contactDetailsArray['Address'] != '') ? '<br />'.$a:$a;    
        $a = trim(substr($dnr->dnr_addr,60,30));
        if (!empty($a)) $contactDetailsArray['Address'] .= ($contactDetailsArray['Address'] != '') ? '<br />'.$a:$a;    
        $a = trim(substr($dnr->dnr_addr,90,30));
        if (!empty($a)) $contactDetailsArray['Address'] .= ($contactDetailsArray['Address'] != '') ? '<br />'.$a:$a;    
        if (!empty($dnr->dnr_post_cd)) $contactDetailsArray['Address'] .= '<br />'.trim($dnr->dnr_post_cd);
        $dnrProfile = new profile();
        if ($dnrProfile->Load($dnrNo)) {
            $a = trim(substr($dnrProfile->add_addr,0,30));
            $contactDetailsArray['Alt Address'] = (!empty($a)) ? $a:'';
            $a = trim(substr($dnrProfile->add_addr,30,30));
            if (!empty($a)) $contactDetailsArray['Alt Address'] .= ($contactDetailsArray['Alt Address'] != '') ? '<br />'.$a:$a;    
            $a = trim(substr($dnrProfile->add_addr,60,30));
            if (!empty($a)) $contactDetailsArray['Alt Address'] .= ($contactDetailsArray['Alt Address'] != '') ? '<br />'.$a:$a;    
            $a = trim(substr($dnrProfile->add_addr,90,30));
            if (!empty($a)) $contactDetailsArray['Alt Address'] .= ($contactDetailsArray['Alt Address'] != '') ? '<br />'.$a:$a;    
            if (!empty($dnrProfile->add_post_cd)) $contactDetailsArray['Address'] .= '<br />'.trim($dnrProfile->add_post_cd);
            if (!empty($dnrProfile->cell_no)) $contactDetailsArray['Cellphone'] = $dnrProfile->cell_no;
            if (!empty($dnrProfile->tel_no)) $contactDetailsArray['Telephone'] = $dnrProfile->tel_no;
            if (!empty($dnrProfile->fax_no)) $contactDetailsArray['Fax'] = $dnrProfile->fax_no;
            if (!empty($dnrProfile->e_mail)) $contactDetailsArray['Email'] = $dnrProfile->e_mail;
        }
        
        #   Personal Details
        $personalDetailsArray['Contact Type'] = 'Archive Record';
        if (!empty($dnr->dnr_birth_date)) $personalDetailsArray['Birthday'] = $dnr->dnr_birth_date;
        if (!empty($dnr->dnr_id)) $personalDetailsArray['ID Number'] = $dnr->dnr_id;
        if ($isDeleted) $personalDetailsArray['Date Deleted'] = $dnr->dnr_del_date;
        
        #   Department Details
        if (!empty($dnr->rep_org_id)) { 
            $orgId = $dnr->rep_org_id;
            $departmentDetailsArray['Organisation Id'] = $orgId;
            $department = substr($orgId,0,1);
            $departmentDetailsArray['Department'] = substr($orgId,0,1) . ' - ' . $GLOBALS['functions']->GetDepartmentName(substr($orgId,0,1));
            $reportDetailsArray['Department'] = $departmentDetailsArray['Department'];
            $reportDetailsArray['Organisation Id'] = $orgId;
            $departmentDetailsArray['Denomination'] = substr($orgId,1,2) . ' - ' . $GLOBALS['functions']->GetDenominationName(substr($orgId,1,2));
            $departmentDetailsArray['Synod'] = substr($orgId,3,1) . ' - ' . $GLOBALS['functions']->GetSynodName($orgId);
            $departmentDetailsArray['Circuit'] = substr($orgId,4,2) . ' - ' . $GLOBALS['functions']->GetCircuitName($orgId);
            $departmentDetailsArray['Congregation'] = substr($orgId,6,2) . ' - '. $GLOBALS['functions']->getCongregationName($orgId);
        }
        
        #   Report Details
        if (!empty($dnr->dnr_cntr_tp)) $reportDetailsArray['Category'] = str_pad($dnr->dnr_cntr_tp,4,'0',STR_PAD_LEFT);
        if (!empty($dnr->dnr_cntr_tp)) $reportDetailsArray['Category'] .= ' '.$GLOBALS['functions']->GetCategoryName($dnr->dnr_cntr_tp);
        if (!empty($dnr->dnr_tax_certf)) $reportDetailsArray['DMS Indicator'] = $dnr->dnr_tax_certf;
        if (!empty($dnr->dnr_sower)) $reportDetailsArray['Sower'] = $dnr->dnr_sower;
        if (!empty($dnr->dnr_bible_cl)) $reportDetailsArray['Bible Club'] = $dnr->dnr_bible_cl;
        if (!empty($dnr->civ_contact_id)) $reportDetailsArray['CiviCRM Id'] = $dnr->civ_contact_id;
        
        #   Communication Preferences
        if (!empty($dnr->dnr_thank)) $communicationPreferenceArray['Thank You'] = $dnr->dnr_thank;
        if (!empty($dnr->dnr_salut)) $communicationPreferenceArray['Salutation'] = trim($dnr->dnr_salut);
        if (!empty($dnr->dnr_lang)) {
            switch($dnr->dnr_lang) {
                case 'A':
                    $communicationPreferenceArray['Language'] = 'Afrikaans';
                    break;
                default:
                    $communicationPreferenceArray['Language'] = 'English';
            }
        }
        break;
    case "civicrm":
        $dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
        if (!$dnr) $GLOBALS['functions']->goToIndexPage();
        $contactId = $dnr['contact_id'];
        $isDeleted = ($dnr['contact_is_deleted']==1);
        $contactSuperType = $dnr['contact_type'];
        $personalDetailsHeading = $contactSuperType . ' Details';
        
        #   Contact Details
        #Addresses
        $addresses = $GLOBALS['functions']->getCiviContactAddresses($contactId);
        if (!empty($addresses)) {
            foreach ($addresses as $a) {
                if ($a['isPrimary']==='0') continue;
                $locationName = $a['locationType'] . ' Address';
                $contactDetailsArray[$locationName] = '';
                if (!empty($a['address1'])) $contactDetailsArray[$locationName] .= $a['address1'];
                if (!empty($a['address2'])) 
                    $contactDetailsArray[$locationName] .= ($contactDetailsArray[$locationName] != '') ? '<br />'.$a['address2']:$a['address2'];
                if (!empty($a['address3'])) 
                    $contactDetailsArray[$locationName] .= ($contactDetailsArray[$locationName] != '') ? '<br />'.$a['address3']:$a['address3'];
                if (!empty($a['address4'])) 
                    $contactDetailsArray[$locationName] .= ($contactDetailsArray[$locationName] != '') ? '<br />'.$a['address4']:$a['address4'];
                if (!empty($a['city'])) 
                    $contactDetailsArray[$locationName] .= ($contactDetailsArray[$locationName] != '') ? '<br />'.$a['city']:$a['city'];
                if (!empty($a['postalCode'])) 
                    $contactDetailsArray[$locationName] .= ($contactDetailsArray[$locationName] != '') ? '<br />'.str_pad($a['postalCode'],4,'0',STR_PAD_LEFT):$a['postalCode'];
            }
        }
        #phone numbers
        $phones = $GLOBALS['functions']->getCiviContactPhoneNos($contactId);
        if (!empty($phones)&&empty($dnr['do_not_phone'])) {
            foreach ($phones as $p) {
                if ($p['isPrimary']==='0') continue;
                $locationName = $p['locationType'] . ' ' . $p['phoneType'];
                $contactDetailsArray[$locationName] = $p['phone'];
            }
        }
        #email addresses
        $emails = $GLOBALS['functions']->getCiviContactEmailAddresses($contactId);
        if (!empty($emails)&&empty($dnr['do_not_email'])) {
            foreach ($emails as $e) {
                if ($e['isPrimary']==='0') continue;
                $locationName = $e['locationType'] . ' Email';
                $contactDetailsArray[$locationName] = $e['emailAddress'];
            }
        }
        
        #   Get Custom Civi Data
        $searchCustomParams['version'] = 3;
        $searchCustomParams['entity_id'] = $contactId;
        $customData = civicrm_api('CustomValue', 'Get', $searchCustomParams);
        $c = $customData['values'];
        
        #   Personal Details
        $donorOtherDetail = $GLOBALS['functions']->getCiviDmsDonorOtherDetail($contactId);
        $contactType = (!empty($dnr['contact_sub_type'])) ? strtolower($dnr['contact_sub_type'][0]) : strtolower($dnr['contact_type']);
        $personalDetailsArray['Contact Type'] = $contactType;
        $dnrLanguage = $GLOBALS['functions']->getDonorsLanguageFromCivi($contactId);
        $personalDetailsArray['Language'] = $dnrLanguage['language']; 
        if (!empty($dnr['birth_date'])&&$dnr['contact_type']=='Individual') {
            $personalDetailsArray['Birthday'] = $dnr['birth_date'];
            $endDate = (!empty($dnr['deceased_date'])) ? $dnr['deceased_date']:null;
            $age = $GLOBALS['functions']->calculate_age($dnr['birth_date'],$endDate);
            $personalDetailsArray['Age'] = $age->y . ' years, ' . $age->m . ' months, ' . $age->d . ' days'; 
        }
        if (!empty($donorOtherDetail['id_number'])&&$dnr['contact_type']=='Individual') $personalDetailsArray['ID Number'] = $donorOtherDetail['id_number'];
        $personalDetailsArray['Is deceased?'] = ($dnr['is_deceased']=='1') ? 'Yes':'No'; 
        if ($dnr['is_deceased']=='1') $personalDetailsArray['Date Deceased'] = $dnr['deceased_date'];
        if (!empty($dnr['gender'])&&$dnr['contact_type']=='Individual') $personalDetailsArray['Gender'] = $dnr['gender'];
        
        #   Relationships
        $relationships = $GLOBALS['functions']->getCiviContactRelationships($contactId);
        $relationshipTypes = $GLOBALS['functions']->getAllCiviRelationshipTypes();
        if (!empty($relationships)) {
            foreach ($relationships as $r) {
                $relType = ($contactId==$r['contact_id_a']) ? 'label_a_b':'label_b_a';
                $relationship = 'Unknown';
                foreach ($relationshipTypes as $type) if ($type['id']==$r['relationship_type_id']) $relationship = $type[$relType];
                $otherContactId = ($contactId==$r['contact_id_a']) ? $r['contact_id_b']:$r['contact_id_a'];
                $otherContact = $GLOBALS['functions']->getCiviContact($otherContactId);
                $relationshipsArray[$relationship][] = '<a href="load.contact.php?s=civicrm&d='.$otherContact['external_identifier'].'" target="_blank" class="relationshipLink">' . $otherContact['display_name'] . '</a>'; 
            }
        }
        
        #   Department Details
        $donorReportDetail = $GLOBALS['functions']->getCiviDmsDonorReportDetail($contactId);
        if (!empty($donorReportDetail['organisation_id'])) { 
            $orgId = $donorReportDetail['organisation_id'];
            $department = substr($orgId,0,1);
            $departmentDetailsArray['Organisation Id'] = $orgId;
            $departmentDetailsArray['Department'] = substr($orgId,0,1) . ' - ' . $GLOBALS['functions']->GetDepartmentName(substr($orgId,0,1));
            $reportDetailsArray['Department'] = $departmentDetailsArray['Department'];
            $reportDetailsArray['Organisation Id'] = $orgId;
            $departmentDetailsArray['Denomination'] = substr($orgId,1,2) . ' - ' . $GLOBALS['functions']->GetDenominationName(substr($orgId,1,2));
            $departmentDetailsArray['Synod'] = substr($orgId,3,1) . ' - ' . $GLOBALS['functions']->GetSynodName($orgId);
            $departmentDetailsArray['Circuit'] = substr($orgId,4,2) . ' - ' . $GLOBALS['functions']->GetCircuitName($orgId);
            $departmentDetailsArray['Congregation'] = substr($orgId,6,2) . ' - '. $GLOBALS['functions']->getCongregationName($orgId);
        }
        
        #   Report Details
        $orgDetail = $GLOBALS['functions']->getCiviDmsOrganisation($donorReportDetail['organisation_id']);
        if (!empty($orgDetail['org_region'])) $reportDetailsArray['Region'] = $orgDetail['org_region'] . ' ' . $GLOBALS['functions']->GetRegionName($orgDetail['org_region']);
        if (!empty($donorReportDetail['category_id'])) $reportDetailsArray['Category'] = str_pad($donorReportDetail['category_id'],4,'0',STR_PAD_LEFT) . ' '. $GLOBALS['functions']->GetCategoryName($donorReportDetail['category_id']);
        if (!empty($c[14]['latest'])) $reportDetailsArray['DMS Indicator'] = $c[14]['latest'];
        
        #   Privacy
        
        $privacyDetailsArray['Can Bulk Email?'] = (!empty($dnr['is_opt_out'])) ? 'No':'Yes';
        $privacyDetailsArray['Can email?'] = (!empty($dnr['do_not_email'])) ? 'No':'Yes';
        $privacyDetailsArray['Can snail mail (postal mail)?'] = (!empty($dnr['do_not_mail'])) ? 'No':'Yes';
        $privacyDetailsArray['Can phone?'] = (!empty($dnr['do_not_phone'])) ? 'No':'Yes';
        $privacyDetailsArray['Can SMS?'] = (!empty($dnr['do_not_sms'])) ? 'No':'Yes';
        
        
        
        #   Acknowledgements
        if (!empty($donorOtherDetail['do_not_thank'])) $communicationPreferenceArray['Thank You'] = ($donorOtherDetail['do_not_thank']==1) ? 'N':'Y';
        $prefMethod = $GLOBALS['functions']->getPreferredCommunicationMethod($contactId);
        $communicationPreferenceArray['Preferred Method'] = $prefMethod['preferred_Method_Description'];
        
        #   Bam club details
        $isBam = $GLOBALS['functions']->isCiviBamMember($contactId);
        if ($isBam) {
            $memberships = $GLOBALS['functions']->getCiviContactMemberships($contactId);
            foreach ($memberships as $b) {
                if ($b['isBam']=='Y') {
                    $bamDetailsArray['BAM Club No'] = $b['bam_ref_no'];
                    $bamDetailsArray['Certificate Printed'] = ($b['bam_certificate_printed']=='1') ? 'Y':'N';
                }
            }   
        }
        
        #   Groups
        $gParams['version'] = 3;
        $gParams['contact_id'] = $contactId;
        $groups = civicrm_api('GroupContact', 'get',$gParams);
        if ($groups['count']>0) {
            $groupsDiv .= ($isDeleted) ? '
        <div class="deletedDetailContainerDiv">
        <div class="deletedDetailContainerHeading">Groups<div class="edit"><img src="/dms/img/pencil-32.png" width="16" height="16" class="newToolTip" title="Edit Contact Details" onclick="showEditForm(\'groups\');" /></div></div>
        <table cellpadding="3" cellspacing="0" width="100%" class="deletedDetailContainerTable">':'
        <div class="detailContainerDiv">
        <div class="detailContainerHeading">Groups<div class="edit"><img src="/dms/img/pencil-32.png" width="16" height="16" class="newToolTip" title="Edit Contact Details" onclick="showEditForm(\'groups\');" /></div></div>
        <table cellpadding="3" cellspacing="0" width="100%" class="detailContainerTable">';
            foreach ($groups['values'] as $gk=>$gv) {
                $groupsDiv .= '<tr><td>'.$gv['title'].'</td><td style="text-align:right">Joined: '.date("d-m-Y",strtotime($gv['in_date'])).'</td></tr>';
            }
            $groupsDiv .= '</table></div>';
        }
        
        if (!empty($dnr['is_opt_out'])) {
            $privacyRows .= '<tr><td class="stop" colspan="2"> DO NOT CONTACT</td><tr>';
        } else {
            $privacyRows .= (!empty($dnr['do_not_email'])) ? '<tr><td class="stop" colspan="2"> DO NOT EMAIL</td><tr>':'';
            $privacyRows .= (!empty($dnr['do_not_mail'])) ? '<tr><td class="stop" colspan="2"> DO NOT SEND POSTAL MAIL</td><tr>':'';
            $privacyRows .= (!empty($dnr['do_not_phone'])) ? '<tr><td class="stop" colspan="2"> DO NOT PHONE</td><tr>':'';
            $privacyRows .= (!empty($dnr['do_not_sms'])) ? '<tr><td class="stop" colspan="2"> DO NOT SMS</td><tr>':'';
        }
        
        break;
}

$departmentColor = '';
if (!empty($department)) {
    $d = new department;
    if ($d->Load($department)) {
       $departmentColor = $d->dep_chartColor;
    } 
}

#   POPULATE THE DATA DISPLAY TABLES
if (!empty($contactDetailsArray)) {
    $contactDetailsRows ='';
    foreach ($contactDetailsArray as $k=>$v) {
        $contactDetailsRows .= '<tr><td>';
        $contactDetailsRows .= $k.'</td><td>';
        $contactDetailsRows .= $v.'</td></tr>';
    }
    $contactDetailsRows .= $privacyRows;
} else {
    $contactDetailsRows ='<tr><td>No Contact Details found.</td></tr>';
}

if (!empty($personalDetailsArray)) {
    $personalDetailsRows = '';
    foreach ($personalDetailsArray as $k=>$v) {
        $personalDetailsRows .= '<tr><td>';
        $personalDetailsRows .= $k.'</td><td>';
        $personalDetailsRows .= $v.'</td></tr>';
    }
} else {
    $personalDetailsRows ='<tr><td>No Personal Details found.</td></tr>';
}

if (!empty($reportDetailsArray)) {
    $reportDetailsRows = '';
    foreach ($reportDetailsArray as $k=>$v) {
        $reportDetailsRows .= '<tr><td width="110">';
        $reportDetailsRows .= $k.'</td><td>';
        $reportDetailsRows .= $v.'</td></tr>';
    }
} else {
    $reportDetailsRows ='<tr><td>No Report Details found.</td></tr>';
}

if (!empty($communicationPreferenceArray)) {
    $communicationPreferenceRows = '';
    foreach ($communicationPreferenceArray as $k=>$v) {
        $communicationPreferenceRows .= '<tr><td>';
        $communicationPreferenceRows .= $k.'</td><td>';
        $communicationPreferenceRows .= $v.'</td></tr>';
    }
} else {
    $communicationPreferenceRows ='<tr><td>No Communication Preferences found.</td></tr>';
}

if (!empty($departmentDetailsArray)) {
    $departmentDetailsRows = '';
    foreach ($departmentDetailsArray as $k=>$v) {
        $departmentDetailsRows .= '<tr><td>';
        $departmentDetailsRows .= $k.'</td><td>';
        $departmentDetailsRows .= $v.'</td></tr>';
    }
} else {
    $departmentDetailsRows ='<tr><td>No Department Details found.</td></tr>';
}

if (!empty($privacyDetailsArray)) {
    $privacyDetailRows = '';
    foreach ($privacyDetailsArray as $k=>$v) {
        $privacyDetailRows .= '<tr><td>';
        $privacyDetailRows .= $k.'</td><td>';
        $privacyDetailRows .= $v.'</td><tr>';
    }
} else {
    $privacyDetailRows ='<tr><td>No Privacy Details found.</td></tr>';
}

if (!empty($bamDetailsArray)) {
    $bamDetailsRows = '';
    foreach ($bamDetailsArray as $k=>$v) {
        $bamDetailsRows .= '<tr><td>';
        $bamDetailsRows .= $k.'</td><td>';
        $bamDetailsRows .= $v.'</td><tr>';
    }
} else {
    $bamDetailsRows = '<tr><td>No BAM Club Details found.</td></tr>';
}

if (!empty($relationshipsArray)) {
   $relationshipsRows = '';
    foreach ($relationshipsArray as $k=>$v) {
        foreach ($v as $t=>$r) {
            $relationshipsRows .= '<tr><td>';
            $relationshipsRows .= $k.'</td><td>';
            $relationshipsRows .= $r.'</td><tr>';
        }
    } 
} else {
    $relationshipsRows = '<tr><td>No Relationships found.</td></tr>';
}

#   SHOW THE HTML
require('html/'.$curScript.'.htm');