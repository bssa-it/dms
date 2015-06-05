<?php

/**
 * @description
 * This displays unacknowledged contributions.  The user will select 
 * contributions that the he/she would like to create an acknowledgement for.  
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = $title = 'Choose Contributions';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   LOAD USER'S ACKNOWLEDGEMENT CONFIGURATIONS
$userFilename = 'acknowledgement/'.$_SESSION['dms_user']['userid'] . '.acknowledgement.xml';
$xmlFile = simplexml_load_file("widgets/$userFilename");
/* temporary department selector */
$myDepartments = $_SESSION['dms_user']['config']['departments'];
$departments = '?';
if (!empty($myDepartments)) {
    $departments = '';
    foreach ($myDepartments as $k=>$v) {
        if (!empty($departments)) $departments .= ",";
        $departments .= "'".trim($k)."'";   
    }
}
$fDate = (!empty($xmlFile->fromDate)) ? $xmlFile->fromDate:date('Y-m-d'); 
#   LOAD UNACKNOWLEDGED CONTRIBUTION RECORDS FROM THE DMS DATABASE BASED ON USER CONFIG
$data = $GLOBALS['functions']->GetUnAcknowledgedContributions($departments,$fDate);

#   PREPARE EXTRACTED DATA FOR DISPLAY
$enabledDates = array();
$categories = array();
$doctypes = array();
$regions = array();
$dpts = array();
$languages = array();
$motivationCodes = array();

$strEnabledDates = date("d-m-Y");
$defaultDate = date("d-m-Y");
$categoryChkBoxs = '';
$departmentCheckBoxes = '';
$docTypeChkBoxs = '';
$regionCheckBoxes = '';
$languageRadio = '';

// if there are no unacknowledged contributions this row will display on the screen
$acknowledgementRows = '<tr><td colspan="13">No Acknowledgements outstanding</td></tr>';
if (!empty($data)) {
    // Eureka!!!  Data has been found clear the $acknowledgementRows variable
    $acknowledgementRows = '';
    $lang = array('af_ZA'=>'Afr','en_GB'=>'Eng','en_ZA'=>'Eng');
    foreach ($data as $k=>$v) {
        //  add hyperlinks to the CiviCRM contact id and the DMS donor id
        $dnrNo = '<a href="load.contact.php?s=civicrm&d='.trim($v['trns_dnr_no']).'">'.trim($v['trns_dnr_no'])."</a>";
        
        //  build arrays for the user filter: 
        if (!in_array($v['trns_date_received'],$enabledDates)) $enabledDates[] = $v['trns_date_received'];
        if (!in_array(str_pad($v['trns_category_id'],4,'0',STR_PAD_LEFT),$categories)) $categories[] = str_pad($v['trns_category_id'],4,'0',STR_PAD_LEFT);
        if (!in_array($v['trns_receipt_type'],$doctypes)) $doctypes[] = $v['trns_receipt_type'];
        if (!in_array($v['trns_region_id'],$regions)) $regions[] = $v['trns_region_id'];
        if (!in_array(substr($v['trns_organisation_id'],0,1),$dpts)) $dpts[] = substr($v['trns_organisation_id'],0,1); 
        if (!in_array($lang[$v['preferred_language']],$languages)) $languages[] = $lang[$v['preferred_language']];
        if (!in_array($v['trns_motivation_id'],$motivationCodes)) $motivationCodes[] = str_pad($v['trns_motivation_id'],4,'0',STR_PAD_LEFT);
        
        //  populate the $acknowledgementRows variable.
        $acknowledgementRows .= "\n\t<tr>";
        $acknowledgementRows .=  "\n\t\t" . '<td><input type="checkbox" value="'.$v['trns_id'].'" class="chkBox" name="trxns[]" onclick="countSelected();" /></td>';
        $acknowledgementRows .=  "\n\t\t<td>" . trim(substr($v['trns_organisation_id'],0,1)) . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . $v['display_name'] . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . $lang[$v['preferred_language']] . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . str_pad($v['trns_category_id'],4,'0',STR_PAD_LEFT) . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . date("d-m-Y",strtotime($v['trns_date_received'])) . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . trim($v['trns_receipt_no']) . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . trim($v['trns_receipt_type']) . '</td>';
        $acknowledgementRows .=  "\n\t\t" . '<td align="right">R ' . trim($v['trns_amount_received']) . '</td>';
        $acknowledgementRows .=  "\n\t\t" . '<td class="dnrLink">' . $dnrNo . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . trim($v['trns_region_id']) . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . trim($v['dms_is_first_trxn']) . '</td>';
        $acknowledgementRows .=  "\n\t\t<td>" . $v['dms_mtd_trxn_count'] . '</td>';
        $acknowledgementRows .= '<td style="display:none">' . $v['apr_preferred_method'] . '</td>';
        $acknowledgementRows .= '<td style="display:none">' . str_pad($v['trns_motivation_id'],4,'0',STR_PAD_LEFT) . '</td>';
        $acknowledgementRows .= "\n\t</tr>";
    }
    
    // populate the filter variables
    $firstDate = true;
    $strEnabledDates = '';
    foreach ($enabledDates as $k=>$v) {
        $strEnabledDates .= ($firstDate) ? '':',';
        $strEnabledDates .= '"'.date("d-m-Y",strtotime($v)).'"';
        $firstDate = false;
    }
    $defaultDate = date("d-m-Y",strtotime($enabledDates[0]));

    asort($categories);
    foreach ($categories as $k=>$v) {
        $categoryChkBoxs .= '<input type="checkbox" id="cat_'.$v.'" value="'.$v.'" class="chkBox" name="chkCategories" onclick="selectFromFilter();" /><label for="cat_'.$v.'">';
        $categoryChkBoxs .= $v.'</label>';
    }
    
    asort($dpts);
    foreach ($dpts as $k=>$v) {
        $departmentCheckBoxes .= '<input type="checkbox" value="'.$v.'" id="dep_'.$v.'" class="chkBox" name="chkDepartments" onclick="selectFromFilter();" /><label for="dep_'.$v.'">'.$v.'</label>';
    }
    
    asort($doctypes);
    foreach ($doctypes as $k=>$v) {
        $docTypeChkBoxs .= '<input type="checkbox" value="'.$v.'" id="doc_'.$v.'" class="chkBox" name="chkDocTypes" onclick="selectFromFilter();" /><label for="doc_'.$v.'">'.$v.'</label>';
    }
    
    asort($regions);
    foreach ($regions as $k=>$v) {
        $regionCheckBoxes .= '<input type="checkbox" value="'.$v.'" id="reg_'.$v.'" class="chkBox" name="chkRegions" onclick="selectFromFilter();" /><label for="reg_'.$v.'">'.$v.'</label>';
    }
    
    asort($languages); 
    #$langOpts = array('af_ZA'=>'Afr','en_ZA'=>'Eng');
    foreach ($languages as $k=>$v){
        $languageRadio .= '<input type="checkbox" value="'.$v;
        $languageRadio .= '" class="chkBox" onclick="selectFromFilter()"  name="chkLanguages" id="lang_';
        $languageRadio .= strtoupper($v) .'" /><label for="lang_'.strtoupper($v).'">'.$v.'</label>';
    }
    
    $motivationCheckBoxes = '';
    if (!empty($motivationCodes)) {
        asort($motivationCodes);
        foreach ($motivationCodes as $k=>$v) {
            if ($v==9000||$v=='0000') continue;
            $motivationCheckBoxes .= '<input type="checkbox" value="'.$v.'" id="moti_'.$v.'" class="chkBox" name="chkMotivations" onclick="selectFromFilter();" /><label for="moti_'.$v.'">'.$v.'</label>';
        }
    }
}

#   SET THE MINIMUM AND MAXIMUM DATES FOR THE USER FILTER
$year = (date("M")>10) ? date("Y"):date("Y")-1;
$minDate = (!empty($enabledDates)) ? date("d-m-Y",strtotime($enabledDates[0])):date("d-m-Y",strtotime($year.'-11-01'));
$maxDate = date("d-m-Y");

#   ADD HTML
require('html/'.$curScript.'.htm');

?>