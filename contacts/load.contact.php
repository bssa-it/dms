<?php

/**
 * @description
 * This is the framework for loading the contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 09/01/2014
 * @Changes
 * 5.0.1 - 13/04/2015 - Chezre Fredericks:
 * Update for version 5.0.1
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$curScript = basename(__FILE__, '.php');
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if ((empty($_SESSION['dmsDonorSearchCriteria']['srch_database'])&&empty($_GET['s']))
        ||!isset($_GET['d'])||empty($_GET['d'])) $GLOBALS['functions']->goToIndexPage();

#   SET VITAL VARIABLES
$database = (!empty($_GET['s'])) ? $_GET['s'] : $_SESSION['dmsDonorSearchCriteria']['srch_database'];
$dnrNo = $_GET['d'];
$cid = '';

if (!empty($_GET['a'])) {
    $loadActivity = '
        $("#screenProtectorDiv").show();
        $("#editDonorDiv").show();
        $("#activity").click();
        $( "#frmDiv" ).empty().append(\'<img src="/dms/img/loading.gif" style="vertical-align: middle" /> loading activity...\');
        $.get( "edit.donorActivity.php", { d: '.$dnrNo.',a: '.$_GET['a'].' })
            .done(function( data ) {
            $("#frmDiv").empty().append( data );
            getEditDetail();
        });';
} else {
    $loadActivity = '';
}

#   SUBMENU NAVIGATION
if (!empty($_SESSION['dmsDonorSearchResultset'])) {
    $searchResultsRecordCount = (isset($_SESSION['dmsDonorSearchResultset']['count'])) ? 
        $_SESSION['dmsDonorSearchResultset']['count']:count($_SESSION['dmsDonorSearchResultset']);   
} else {
    $searchResultsRecordCount = 1;
}
if ($searchResultsRecordCount==1) {
    $searchResultDestination = 'index.php';
    $searchResultMessage = 'Go back to the Dashboard';
} else {
    $searchResultDestination = 'find.contact.php';
    $searchResultMessage = 'Go back to search results';
}
$searchResultLink = '<a href="'.$searchResultDestination;
$searchResultLink .= '"><img src="/dms/img/search-white-32.png" width="32" height="32" title="'.$searchResultMessage;
$searchResultLink .= '" class="masterTooltip" /></a>';

#   CREATE MENU
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = ''; 

#   LOAD DONOR RECORD DETAILS
switch ($database) {
    case "archive":
        $dnr = new donor();
        if (!$dnr->Load($dnrNo)) $GLOBALS['functions']->goToIndexPage();
        
        $donorName = '';
        $donorName .= (!empty($dnr->dnr_title)) ? trim($dnr->dnr_title):'';
        $donorName .= ($donorName=='') ? '':' ';
        $donorName .= (!empty($dnr->dnr_inits)) ? trim($dnr->dnr_inits):'';
        $donorName .= ($donorName=='') ? '':' ';
        $donorName .= (!empty($dnr->dnr_name)) ? trim($dnr->dnr_name):'';
        $donorName .= ($donorName=='') ? 'No Name':'';
        $pageHeading = '<img src="/dms/img/archive.png" id="imgPageHeading" style="float:left;vertical-align: top" height="80" width="80" />';
        $pageHeading .= '<div style="float:left;"><div id="divDonorName">'.$donorName.'</div><div style="font-size: 12pt">Contact/Donor No ' . $dnrNo;
        #$pageHeading .= (empty($dnr->civ_contact_id)) ? ' (No CiviCRM id)': ' (CiviCRM id '.$dnr->civ_contact_id.')';
        $isDeleted = ($dnr->dnr_deleted=='Y');
        $pageHeading .= '</div></div>';
        $bottomNavItems = array('Transactions','Remarks');
        
        #  Contribution Summary
        $contributionSummary = '<div id="contributionSummaryImageDiv">
            <img src="/dms/img/archive.png" width="32" height="32" style="vertical-align:middle" />
        </div>';
        #$contributionSummary .= '<div id="contributionSummaryHeadingDiv">Archive Donor Record</div>';
        $contributionSummary .= '<div id="totalcontributionDiv">Total Contributions 
            <br /><span style="font-size: 16pt;">R ' . number_format($dnr->dnr_tot,2,'.',',') .'</span></div>';
        $contributionSummary .= '<div id="lastContributionDiv">Most Recent<br /><span style="font-size: 11pt;font-weight:bold">R ' . number_format($dnr->dnr_last_amnt,2,'.',',') .'</span><br />' . date('d-m-Y',strtotime($dnr->dnr_last_date)).'</div>';
        $contributionSummary .= '<div id="firstContributionDiv">1<sup>st</sup> Donation<br /><span style="font-size: 11pt;font-weight:bold">R ' . number_format($dnr->dnr_first_amnt,2,'.',',') .'</span><br />' . date('d-m-Y',strtotime($dnr->dnr_first_date)).'</div>';
        break;
    case "civicrm":
        $dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
        if (!$dnr) $GLOBALS['functions']->goToIndexPage();
        $cid = $dnr['contact_id'];
        
        $p['version'] = 3;
        $p['entity_id'] = $dnr['id'];
        $customValues = civicrm_api('CustomValue','get',$p);
        $contactType = (!empty($dnr['contact_sub_type'])) ? strtolower($dnr['contact_sub_type'][0]) : strtolower($dnr['contact_type']);
        $donorName = (!empty($dnr['display_name'])) ? $dnr['display_name'] : '';
        $pageHeading = '<div id="dnrImg"><img src="/dms/img/'.$contactType.'.png" id="imgPageHeading" width="64" height="64" title="'.$contactType.'" /></div>';
        $pageHeading .= '<div id="divDonorName">'.$donorName.'</div>';
        $pageHeading .= '<div style="font-size: 12pt">Contact Id/Donor No '.$dnr['contact_id'] . '/' . $dnrNo . '</div>';
        #$pageHeading .= ' (CiviCRM id '.$dnr['contact_id'] .')</span>';
        $isDeleted = ($dnr['contact_is_deleted']==1);
        $bottomNavItems = array('Contributions','Notes','Memberships','Activity');
        
        # Contribution summary
        $totContributions = 0;
        $fdonation = 0;
        $fdate = date("Y-m-d");
        $ldonation = 0;
        $ldate = '1900-01-01';
        
        $contParams['contact_id'] = $dnr['contact_id'];
        $contParams['options']['limit'] = 100000;
        $contParams['options']['sort'] = ' receive_date DESC';
        $apiRecord = civicrm_api3('Contribution', 'Get', $contParams);
        if ($apiRecord['count']>0) {
            foreach ($apiRecord['values'] as $k=>$v) {
                $amnt = $v['total_amount'];
                $totContributions += $amnt;
                if (strtotime($fdate)>strtotime($v['receive_date'])) {
                    $fdate = $v['receive_date'];
                    $fdonation = $amnt;
                }
                if (strtotime($ldate)<strtotime($v['receive_date'])) {
                    $ldate = $v['receive_date'];
                    $ldonation = $amnt;
                }
            }
            $fdate = date('d-m-Y',strtotime($fdate));
            $ldate = date('d-m-Y',strtotime($ldate));
        } else {
            $fdate = $ldate = '';
        }   
        
        $contributionSummary = '<div id="contributionSummaryImageDiv">
            <img src="/dms/img/civi-logo.png" width="32" height="32" style="vertical-align:middle" />
        </div>';
        $contributionSummary .= '<div id="totalcontributionDiv">Total Contributions 
            <br /><span style="font-size: 16pt;">R ' . number_format($totContributions,2,'.',',') .'</span></div>';
        $contributionSummary .= '<div id="lastContributionDiv">Most Recent<br /><span style="font-size: 11pt;font-weight:bold">R ' . number_format($ldonation,2,'.',',') .'</span><br />' . $ldate.'</div>';
        $contributionSummary .= '<div id="firstContributionDiv">1<sup>st</sup> Donation<br /><span style="font-size: 11pt;font-weight:bold">R ' . number_format($fdonation,2,'.',',') .'</span><br />' . $fdate .'</div>';
        break;
}

#   LOAD BOTTOM NAVIGATION
$bottomNavList = '<li class="selectedNav" onclick="setFocusOnMe(this,'.$dnrNo.',\''.$database.'\')">Summary</li>';
foreach ($bottomNavItems as $b=>$i) {
    $bottomNavList .= '<li onclick="setFocusOnMe(this,'.$dnrNo.',\''.$database.'\')">'.$i.'</li>';
}
if ($database=='civicrm') $bottomNavList .= '<li onclick="showEditForm(\'personalDetails\');">Edit Contact <img src="/dms/img/pencil-32.png" width="16" height="16" title="Edit Preferences" style="float:right;margin-top:2px;margin-right: 3px;" /></li>';   

#   ADD TEMPLATE
require('html/'.$curScript.'.htm');