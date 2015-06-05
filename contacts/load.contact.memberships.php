<?php

/**
 * @description
 * This script loads the memberships for the contact.
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
if (!isset($_GET['d'])||empty($_GET['d'])) $GLOBALS['functions']->goToIndexPage();

#   SET VITAL VARIABLES
$dnrNo = $_GET['d'];
$membershipId = (!empty($_GET['m'])) ? $_GET['m']:'';   // <- THIS VARIABLE IS ONLY SET WHEN PROVIDED
$contributionRows = '';

#   SET THE CONTACT ID
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
if (!$dnr) $GLOBALS['functions']->goToIndexPage();
$contactId = $dnr['contact_id'];

#   SEARCH FOR MEMBERSHIPS FOR THE CONTACT WITH THE CIVI API
$memParams['version'] = 3;
$memParams['contact_id'] = $contactId;
$memApiRecord = civicrm_api('Membership', 'Get', $memParams);
if ($memApiRecord['count']>0) {
    #   IF MEMBERSHIPS ARE FOUND THEN DISPLAY THEM
    
    #   FIRST POPULATE THE DROPDOWN BOX WITH ALL THE MEMBERSHIPS THE CONTACT IS LINKED TO
    $opts = ''; 
    foreach ($memApiRecord['values'] as $k=>$mv) {
        $selected = (empty($membershipId)||$membershipId==$mv['id']) ? ' SELECTED':'';
        $opts .= '<option value="'.$mv['id'].'"'.$selected.'>'.$mv['membership_name'].'</option>';
        #   WHILE LISTING THE MEMBERSHIPS SET THE STATUS AND JOIN DATE FOR THE CURRENTLY DISPLAYED MEMBERSHIP
        if (empty($membershipId)) {
            $membershipId = $mv['id'];
            $statusId = $mv['status_id'];
            $joinDate = $mv['join_date'];
        } else {
            if ($membershipId==$mv['id']) {
                $statusId = $mv['status_id'];
                $joinDate = $mv['join_date'];   
            }
        }
    }
    $opts .= '<option value="">-- add/edit --</option>';
    
    #   GET THE STATUS DESCRIPTION FROM THE STATUS ID WITH THE CIVI API
    $ms['version'] = 3;
    $ms['id'] = $statusId;
    $msResult = civicrm_api('MembershipStatus','getsingle',$ms);
    $membershipStatus = $msResult['label']; 
    
    #   GET THE CUSTOM VALUES FOR THE BAM MEMBERSHIP
    $mCustom['version'] = 3;
    $mCustom['entity_id'] = $membershipId;
    $mCustom['return.custom_15'] = 1;
    $mCustom['return.custom_16'] = 1;
    $mCustomData = civicrm_api('CustomValue', 'Get', $mCustom);
    if ($mCustomData['count']>0) {
        $bamClubDetails = $mCustomData['values'];
        $pdfCertificateExists = file_exists('bam/'.$membershipId.'-bam-certificate.pdf');
        $certificate = ($bamClubDetails[16]['latest']=='1') ? 'Y':'N';
        $certificate = ($bamClubDetails[16]['latest']=='1'&&$pdfCertificateExists) ? '<img id="imgCertificate" src="/dms/img/pdf.png" width="16" height="16" mid="'.$membershipId.'" />':$certificate;
        $bamClubDiv = '<div class="detailContainerDiv" style="margin-top: 0px;">
                <div class="detailContainerHeading">Other Detail</div>
            <table cellpadding="3" cellspacing="0" width="100%" class="detailContainerTable"><tr><td>BAM Club no</td><td align="right">'.$bamClubDetails[15]['latest'].'</td></tr><tr><td>Certificate Printed</td><td align="right">'.$certificate.'</td></tr></table>
        </div>';
    } else {
        #   IF NOT THE BAM CLUB MEMBERSHIP THEN SHOW NOTHING
        $bamClubDiv = '';
    }
    
    #   LOAD THE PAYMENTS LINKED TO THE MEMBERSHIP
    $totContributions = 0;
    $fdonation = 0;
    $fdate = date("Y-m-d 23:59:59");
    $ldonation = 0;
    $ldate = '1900-01-01';
    
    $mp['version'] = 3;
    $mp['membership_id'] = $membershipId;
    $mp['options']['limit'] = 1000;
    $mp['options']['sort'] = ' contribution_id DESC';
    $mpContributions = civicrm_api('MembershipPayment','get',$mp);
    if ($mpContributions['count']>0) {
        
        foreach ($mpContributions['values'] as $k=>$p) {
            #   GET THE CONTRIBUTION DATA
            $cParam['version'] = 3;
            $cParam['id'] = $p['contribution_id'];
            $v = civicrm_api('Contribution','Getsingle',$cParam);
            $amnt = ($v['contribution_status_id']==7) ? $v['total_amount']*-1:$v['total_amount'];
            
            #   GET THE ACKNOWLEDGEMENT DATA FOR THE CONTRIBUTION
            $acknowledgement = $GLOBALS['functions']->GetAcknowledgmentsForCiviContribution($v['id']);
            if (empty($acknowledgement)) {
                $contributionDate = (empty($v['thankyou_date'])) ? '&nbsp;':'SCO ' . date("d-m-Y",strtotime($v['thankyou_date']));
                $method = (empty($v['thankyou_date'])) ? '&nbsp;':'SCO';
                $acknowledgement = array("ack_method"=>$method,"ack_document"=>'&nbsp;',"ack_date"=>$contributionDate);
                $document = '&nbsp;';
                $userFullname = array('name'=>'&nbsp;');   
            } else {
                $contributionDate = (is_null($acknowledgement['ack_date'])) ? '&nbsp;' : 'DMS ' . date("d-m-Y H:i:s",strtotime($acknowledgement['ack_date']));
                $method = $acknowledgement['ack_method'];
                $document = (strpos($acknowledgement['ack_document'],'.pdf')) ? '<a href="acklists/'.$acknowledgement['ack_document'].'">'.$acknowledgement['ack_document'].'</a>':$acknowledgement['ack_document'];
                $userFullname = $GLOBALS['functions']->GetJoomlaUserDetails($acknowledgement['ack_usr_id']);
            }
            
            #   GET THE DMS DATA FOR THE CONTRIBUTION
            $dmsDetail = $GLOBALS['functions']->getCiviDmsTransactionDetail($v['id']);
            $motivation = ($dmsDetail['motivation_id']==9000) ? '':$dmsDetail['motivation_id'] . ' - '.$GLOBALS['functions']->getMotivationCodeDescription($dmsDetail['motivation_id']);
            
            #   POPULATE THE DISPLAY TABLE
            $row = array();
            $row[] = date("Y-m-d",strtotime($v['receive_date']));
            $row[] = $v['trxn_id'];
            $row[] = $v['payment_instrument'];
            $row[] = 'R ' . number_format((float)$amnt,2,'.',',');
            $row[] = $motivation;
            $row[] = str_pad($dmsDetail['category_id'],4,'0',STR_PAD_LEFT);
            $row[] = $dmsDetail['region_id'];
            $row[] = $dmsDetail['organisation_id'];
            $row[] = $contributionDate;
            $row[] = $userFullname['name'];
            $row[] = $method;
            $row[] = $document;
            $allRows[] = $row;
            
            #   UPDATE THE CONTRIBUTION SUMMARY FOR THE MEMBERSHIP
            $totContributions += $amnt;
            if (strtotime($fdate)>strtotime($v['receive_date'])) {
                $fdate = date("Y-m-d",strtotime($v['receive_date']));
                $fdonation = $amnt;
            }
            if (strtotime($ldate)<strtotime($v['receive_date'])) {
                $ldate = date("Y-m-d",strtotime($v['receive_date']));
                $ldonation = $amnt;
            }
        }
        
        #   POPULATE THE DISPLAY TABLE
        rsort($allRows);
        foreach ($allRows as $k=>$v) {
            $contributionRows .= '<tr>';
            foreach ($v as $key=>$val) {
                $contributionRows .= ($key==3) ? '<td align="right" style="padding-right: 15px;">':'<td>';
                $contributionRows .= $val . '</td>';   
            }
            $contributionRows .= '</tr>';
        }
    } else {
        #   IF THERE ARE NO CONTRIBUTIONS LINKED TO THE MEMBERSHIP INFORM THE USER
        $contributionRows = '<tr><td colspan="12">No contributions found for this Membership</td></tr>';
        $fdate = '--';
        $ldate = '--';
    } 
    require('html/'.$curScript.'.htm');
} else {
    #   IF NO MEMBERSHIPS ARE FOUND, INFORM THE USER
    echo '<div style="margin-left: 20px;margin-right: 20px;"><div style="clear:both">There are no memberships linked to this donor.</div><div class="btn" style="clear:both;margin-top: 10px;" id="donorMembershipsBtn" onclick="btnAddMembershipClick();">Add Membership</div></div>';
}