<?php

/**
 * @description
 * This page loads the donor contact details edit form.
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
$sowerLocationTypeId = (string)$GLOBALS['xmlConfig']->sowerLocationTypeId['id'];
$includeSowerLocation = true;

#Addresses
$addresses = 'There are no addresses linked to this contact.  Please add one.';
$civiContactAddresses = $GLOBALS['functions']->getCiviContactAddresses($contactId);
$cnt = 0;
if (!empty($civiContactAddresses)) {
    $addresses = '';
    foreach ($civiContactAddresses as $a) {
        $cnt++;
        $tblClass = ($a['isPrimary']==='1') ? 'addressPrimaryTbl':'addressTbl';
        $is_Primary =  ($a['isPrimary']==='1') ? ' checked':'';
        $is_Sower = ($a['locationTypeId']==$sowerLocationTypeId);
        $imgDelete = (!$is_Sower) ? '<img src="/dms/img/delete.png" onclick="deleteDetail('.$a['id'].',\'address\')" title="delete" width="16" height="16" />':'';
        if ($includeSowerLocation&&$is_Sower)  $includeSowerLocation = false;
        $clearLeft = (($cnt%3) == 1) ? 'clear:left':'';
        $addresses .= '<table class="'.$tblClass.'" cellpadding="5" cellspacing="0" style="'.$clearLeft.'">';
        $addresses .= '<thead><tr><td>Location Type: <span id="addressLocationType-'.$a['id'].'">'.$a['locationType'].'</span> <input type="radio" style="float: right" name="address_is_primary" value="'.$a['id'].'" '.$is_Primary.' onclick="saveNewPrimary(this);" id="addressId-'.$a['id'].'" /><img src="/dms/img/edit-16x16.png" title="edit" width="16" height="16" onclick="editAddress('.$a['id'].');" /> '.$imgDelete.'</td></tr></thead>';
        $addresses .= '<tbody>';
        if (!empty($a['address1'])) $addresses .= '<tr><td id="address1-'.$a['id'].'">'.$a['address1'].'</td></tr>';
        if (!empty($a['address2'])) $addresses .= '<tr><td id="address2-'.$a['id'].'">'.$a['address2'].'</td></tr>';
        if (!empty($a['address3'])) $addresses .= '<tr><td id="address3-'.$a['id'].'">'.$a['address3'].'</td></tr>';
        if (!empty($a['address4'])) $addresses .= '<tr><td id="address4-'.$a['id'].'">'.$a['address4'].'</td></tr>';
        if (!empty($a['city'])) $addresses .= '<tr><td id="city-'.$a['id'].'">'.$a['city'].'</td></tr>';
        if (!empty($a['postalCode'])) $addresses .= '<tr><td><span id="postal_code-'.$a['id'].'">'.str_pad($a['postalCode'],4,'0',STR_PAD_LEFT).'</span></td></tr>';
        $addresses .= '</tbody>';
        $addresses .= '</table>';
    }
}

#phone numbers
$phoneNos = $GLOBALS['functions']->getCiviContactPhoneNos($contactId);
$phoneNumbers = '<tr><td colspan="5">Please add a phone number.</td></tr>';
if (!empty($phoneNos)) {
    $phoneNumbers = '';
    foreach ($phoneNos as $p) {
        $phoneNumbers .= "\n" . '<tr>';
        $phoneNumbers .= "\n\t" . '<td id="phone_type_id-'.$p['id'].'">' . $p['phoneType'] . '</td>';
        $phoneNumbers .= "\n\t" . '<td id="location_type_id-'.$p['id'].'">' . $p['locationType'] . '</td>';
        $phoneNumbers .= "\n\t" . '<td><span id="phone-'.$p['id'].'">' . $p['phone'] . '</span></td>';
        $is_Primary = ($p['isPrimary']==='1') ? ' checked':'';
        $phoneNumbers .= "\n\t" . '<td><input type="radio" name="phone_is_primary" id="phoneId-'.$p['id'].'" value="'.$p['id'].'" ' . $is_Primary . ' onclick="saveNewPrimary(this);" /></td>';
        $phoneNumbers .= "\n\t" . '<td class="manageDetail"><img src="/dms/img/delete.png" onclick="deleteDetail('.$p['id'].',\'phone\')" title="delete" width="16" height="16" /> <img src="/dms/img/edit-16x16.png" title="edit" width="16" height="16" onclick="editPhoneNo('.$p['id'].');" /></td>';
        $phoneNumbers .= "\n" . '</tr>';
    }
}

$optGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->phonetypes;
$phoneTypes = $GLOBALS['functions']->getCiviOptionValues($optGroupId);
$phoneTypeOpts = '';
if (!empty($phoneTypes)) {
    foreach ($phoneTypes as $ptype) {
        $phoneTypeOpts .= '<option value="'.$ptype['value'].'">'.$ptype['label'].'</option>';
    }
}

$locationTypes = $GLOBALS['functions']->getCiviLocationTypes();
$locationTypeOpts = '';
$locationTypeList = 'var locationTypes = [];';
if (!empty($locationTypes)) {
    foreach ($locationTypes as $ltype) {
        $locationTypeList .= "\n" . 'locationTypes.push({val: ' . $ltype['id'] . ', name: "' . $ltype['name'] . '"});';
        //if ($ltype['id']==$sowerLocationTypeId&&!$includeSowerLocation) continue;
        $locationTypeOpts .= '<option value="'.$ltype['id'].'">'.$ltype['name'].'</option>';
    }
}

#email addresses
$emailAddresses = '<tr><td colspan="4">Please add an email address.</td></tr>';
$contactEmailAddresses = $GLOBALS['functions']->getCiviContactEmailAddresses($contactId);
if (!empty($contactEmailAddresses)) {
    $emailAddresses = '';
    foreach ($contactEmailAddresses as $e) {
        $emailAddresses .= "\n" . '<tr>';
        $emailAddresses .= "\n\t" . '<td id="location_type_id-'.$e['id'].'">' . $e['locationType'] . '</td>';
        $emailAddresses .= "\n\t" . '<td id="email-'.$e['id'].'">' . $e['emailAddress'] . '</td>';
        $is_Primary = ($e['isPrimary']==='1') ? ' checked':'';
        $emailAddresses .= "\n\t" . '<td><input type="radio" name="email_is_primary" id="emailId-'.$e['id'].'" value="'.$e['id'].'" ' . $is_Primary . ' onclick="saveNewPrimary(this);" /></td>';
        $emailAddresses .= "\n\t" . '<td class="manageDetail"><img src="/dms/img/delete.png" onclick="deleteDetail('.$e['id'].',\'email\')" title="delete" width="16" height="16" /> <img src="/dms/img/edit-16x16.png" title="edit" width="16" height="16" onclick="editEmail('.$e['id'].')" /></td>';
        $emailAddresses .= "\n" . '</tr>';
    }
}
        
#   SHOW THE HTML
require('html/'.$curScript.'.htm');