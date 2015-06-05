<?php

/**
 * @description
 * This page loads the donor's memberships edit form.
 * 
 * @author      Chezre Fredericks
 * @date_created 15/07/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if (!isset($_GET['d'])||empty($_GET['d'])) {
    echo 'not found';
    exit();
}

#   SET VITAL VARIABLES
$dnrNo = $_GET['d'];
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
if (!$dnr) $GLOBALS['functions']->goToIndexPage();
$contactId = $dnr['contact_id'];

$memberships = $GLOBALS['functions']->getCiviContactMemberships($contactId);
$membershipTypes = $GLOBALS['functions']->getAllCiviMembershipTypes();
$membershipsSelect = '';
$membershipDivs = '';
$bamMembershipTypeId = (string)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;
$membershipTypesToUseInSelect = $membershipTypes;  
$jqueryBindings = '';
$userIsAdmin = ($_SESSION['dms_user']['authorisation']->isAdmin);
if (!empty($memberships)) {
    $membershipStatuses = $GLOBALS['functions']->getAllCiviMembershipStatuses();
    foreach ($memberships as $m) {
        if (!empty($membershipTypesToUseInSelect)) 
            foreach ($membershipTypesToUseInSelect as $k=>$mttuis) 
                if ($mttuis['id']==$m['membership_type_id']) unset($membershipTypesToUseInSelect[$k]);
                
        $isBam = ($bamMembershipTypeId==$m['membership_type_id']);
        $statusOpts = '';
        foreach ($membershipStatuses as $ms) {
            $selected = ($ms['name']==$m['status']) ? ' SELECTED':'';
            $statusOpts .= '<option value="'.$ms['id'].'"'.$selected.'>'.$ms['label'].'</option>';
        }
        
        $membershipDivs .= '<div class="contactMembership">';
        $membershipDivs .= '<form action="save.donorMembership.php" method="POST" id="frmEditMembership-'.$m['id'].'" name="frmNewMembership-'.$m['id'].'">';
        $membershipDivs .= '<input type="hidden" name="upd_contact_id" value="'.$contactId.'" />';
        $membershipDivs .= '<input type="hidden" name="id" value="'.$m['id'].'" />';
        $deleteMem = ($isBam&&!$userIsAdmin) ? '':'<img src="/dms/img/delete.png" style="float: right;" width="24" height="24" title="delete"  id="imgDeleteMembership-'.$m['id'].'" />';
        $membershipDivs .= "\n\t" .'<div class="memHeading"><input type="hidden" id="membership_name-'.$m['id'].'" name="membership_name" value="'.$m['membership_name'].'" />'.$m['membership_name'].$deleteMem.'</div>';
        $membershipDivs .= "\n\t" . '<table width="100%" cellpadding="3" cellspacing="0" id="tblMembership-'.$m['id'].'" class="editFormTbl">';
        $membershipDivs .= "\n\t\t" . '<tr><td><label for="status_id-'.$m['id'].'">Membership Status</label></td><td><select id="status_id-'.$m['id'].'" name="status_id" style="display: none">'.$statusOpts.'</select><div class="overrideDiv" id="overrideDiv-'.$m['id'].'">'.$m['status'].'</div> <div class="lblOverride"><input type="checkbox" name="is_override" id="is_override-'.$m['id'].'" /> <label for="is_override-'.$m['id'].'"> override status</label></div></td></tr>';
        if ($isBam)  {
            $certOpts = '';
            for ($i=0;$i<=1;$i++) {
                $selected = ($m['bam_certificate_printed']==$i) ? ' SELECTED':'';
                $values = array('0'=>'No','1'=>'Yes');   
                $certOpts .= '<option value="'.$i.'"'.$selected.'>'.$values[$i].'</option>';
            }
            $pdfCertificateExists = file_exists('bam/'.$m['id'].'-bam-certificate.pdf');
            $certificate = ($m['bam_certificate_printed']==1&&$pdfCertificateExists) ? '<img src="/dms/img/pdf.png" width="24" height="24" title="print certificate" id="bamCertificate" mid="'.$m['id'].'" />':'';
            $membershipDivs .= "\n\t\t" . '<tr class="bamRefNo"><td><label for="custom_15-'.$m['id'].'">BAM Ref No</label></td><td><input type="text" id="custom_15-'.$m['id'].'" name="custom_15" value="'.$m['bam_ref_no'].'" /></td></tr>';  
            $membershipDivs .= "\n\t\t" . '<tr><td><label for="custom_16-'.$m['id'].'">BAM Certificate Printed</label></td><td><select id="custom_16-'.$m['id'].'" name="custom_16">'.$certOpts.'</select>'.$certificate.'</td></tr>';
        }
        $membershipDivs .= "\n\t\t" . '<tr><td><label for="join_date-'.$m['id'].'">Join Date</label></td><td><input type="date" id="join_date-'.$m['id'].'" name="join_date" value="'.date("Y-m-d",strtotime($m['join_date'])).'" /></td></tr>';
        $sDate = (empty($m['start_date'])) ? '':date("Y-m-d",strtotime($m['start_date']));
        $membershipDivs .= "\n\t\t" . '<tr><td><label for="start_date-'.$m['id'].'">Start Date</label></td><td><input type="date" id="start_date-'.$m['id'].'" name="start_date" value="'.$sDate.'" /></td></tr>';
        $eDate = (empty($m['end_date'])) ? '':date("Y-m-d",strtotime($m['end_date']));
        if (!$isBam)
            $membershipDivs .= "\n\t\t" . '<tr><td><label for="end_date-'.$m['id'].'">End Date</label></td><td><input type="date" id="end_date-'.$m['id'].'" name="end_date" value="'.$eDate.'" /></td></tr>';
        $membershipDivs .= ($isBam&&!$userIsAdmin) ? '<tr><td colspan="2">&nbsp;</td></tr>':'<tr><td>&nbsp;</td><td><div class="btn" id="btnSaveMembership-'.$m['id'].'">Save</div></td></tr>';
        $membershipDivs .= "\n\t" . '</table>';
        $membershipDivs .= '</form>';
        $membershipDivs .= '</div>';
        
        $jqueryBindings .= "\n" . '$("#btnSaveMembership-'.$m['id'].'").bind("click",validateMembershipFrm);';
        $jqueryBindings .= "\n" . '$("#imgDeleteMembership-'.$m['id'].'").bind("click",deleteMembership);';
        $jqueryBindings .= "\n" . '$("#is_override-'.$m['id'].'").bind("click",showStatus);';
        
    }
}

if (!empty($membershipTypesToUseInSelect)) {
    $membershipsSelect = '<select name="membership_type_id" id="membership_type_id"><option value="">-- New Membership --</option>';
    foreach ($membershipTypesToUseInSelect as $mttuis) {
        $isBam = ($bamMembershipTypeId==$mttuis['id']);
        $membershipsSelect .= ($isBam&&!$userIsAdmin) ? '' : '<option value="'.$mttuis['id'].'">'.$mttuis['name'].'</option>';
    }
    $membershipsSelect .= '</select>';
}

#   SHOW THE HTML
require('html/'.$curScript.'.htm');
