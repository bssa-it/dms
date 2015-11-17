<?php

/**
 * @description
 * This script searches for contacts and puts them into the user's SESSION.
 * 
 * @author      Chezre Fredericks
 * @date_created 12/12/2013
 * @Changes
 * 5.0.1 - 13/04/2015 - Chezre Fredericks:
 * everything contact related moved to the contacts/ directory
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$curScript = basename(__FILE__, '.php');
$menu = new menu;
$pageHeading = $title = 'Contact Search Results';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
$sql = '';

#sanitize the posted variables
if(!empty($_POST)) $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

#   FIND THE DONORS/CONTACTS AND LOAD THE DATA ARRAY
$donors = array();
#   FIRST CHECK IF SEARCH CRITERIA CHANGED
if (
        (isset($_SESSION['dmsDonorSearchCriteria'])&&isset($_POST)&&$_POST != $_SESSION['dmsDonorSearchCriteria']&&!empty($_POST))
        ||(!isset($_SESSION['dmsDonorSearchCriteria'])&&!isset($_POST)&&!empty($_POST))
        ||(!isset($_SESSION['dmsDonorSearchCriteria'])&&isset($_SESSION['dmsDonorSearchResultset']))
        ||(isset($_SESSION['dmsDonorSearchCriteria'])&&!isset($_SESSION['dmsDonorSearchResultset']))
    ) unset($_SESSION['dmsDonorSearchResultset'],$_SESSION['dmsDonorSearchCriteria']);

$bamMembershipTypeId = (string)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;     
if (!empty($_POST['qck_search'])) {
    include 'build.quicksearchsql.php';
}

if (!empty($_GET['group'])) {
    unset($_SESSION['dmsDonorSearchResultset'],$_SESSION['dmsDonorSearchCriteria']);
   include 'build.groupsql.php';
}

if (empty($_GET['format'])&&empty($_POST['format'])) {
    $format = 'html';
} else {
    $format = (empty($_GET['format'])) ? $_POST['format']:$_GET['format'];
}

#   THEN GET DATASET
if (isset($_SESSION['dmsDonorSearchResultset'])) {
    $donors = $_SESSION['dmsDonorSearchResultset'];
    $postedVariables = $_SESSION['dmsDonorSearchCriteria'];
} else {
    #   GET SEARCH CRITERIA FROM SESSION OR POST SUPERGLOBALS
    if (!isset($_SESSION['dmsDonorSearchCriteria'])) {
        if (!isset($_POST)||is_null($_POST)||empty($_POST)) $GLOBALS['functions']->goToIndexPage();
        $_SESSION['dmsDonorSearchCriteria'] = $_POST;
    }
    $postedVariables = $_SESSION['dmsDonorSearchCriteria'];
    
    foreach ($postedVariables as $k=>$v) if (!empty($v)) $searchVariables[$k] = $v;
    unset($searchVariables['srch_database']);
    switch ($postedVariables['srch_database']) {
        ###     ARCHIVE SEARCH
        case "archive":         
            if (count($searchVariables)==1&&isset($searchVariables['srch_donorDeleted'])&&$searchVariables['srch_donorDeleted']=='A') 
                break;
            include 'build.archivesql.php'; 
            break;
        ###     SEARCHING CIVICRM
        case "civicrm":
            if (count($searchVariables)==1&&isset($searchVariables['srch_donorDeleted'])&&$searchVariables['srch_donorDeleted']=='A') 
                break;
            include 'build.civisql.php';
            break;         
    }
    $_SESSION['dmsDonorSearchResultset'] = $donors;
    $GLOBALS['functions']->showSql($sql);
}

########
#
#   SQL HAS BEEN CREATED AND EXECUTED IN THE CIVICRM OR DMS DATABASES RESPECTIVELY.
#   ONCE THE DATA HAS BEEN LOADED IT IS COPIED INTO THE $_SESSION['dmsDonorSearchResultset'] VARIABLE
#
########

#   FILL TABLE ROWS
$searchResultRows = '<tr><td colspan="8" style="padding-left: 10px">No Results found</td></tr>';
if (!empty($donors)) {
    if (count($donors)==1&&$format=='html') {
        header('location:load.contact.php?d='.$donors[0]['dnr_no']);
        exit();
    }
    
    $searchResultRows =  '';
    foreach ($donors as $dn=>$d) {
        $onclick = ($format=='sateliteHtml') ? 'contactId='.$d['contact_id']:'onclick="window.location=\'load.contact.php?d='.$d['dnr_no'].'\'"';
        $searchResultRows .= '<tr '.$onclick.'>';
        $searchResultRows .= '<td>'.$d['region'].'</td>';
        $searchResultRows .= '<td>'.$d['contact_id'].'</td>';
        $searchResultRows .= '<td>'.$d['dnr_no'].'</td>';
        $searchResultRows .= '<td>'.preg_replace('/  /','',$d['display_name']).'</td>';
        $searchResultRows .= '<td>'.preg_replace('/  /','',$d['phone']).'</td>';
        $searchResultRows .= '<td>'.$d['email'].'</td>';
        $searchResultRows .= '<td>'.$d['is_deleted'].'</td>';
        $searchResultRows .= '<td>'.preg_replace('/  /','',$d['address']).'</td>';
        $searchResultRows .= '</tr>';
    }
    $pageHeading .= '<div style="font-size:12pt"> ('.count($donors).' records found in '.$postedVariables['srch_database'];
    $pageHeading .= ' DB)</div>';
} else {
    $pageHeading .= '<div style="font-size:12pt"> (0 records found in '.$postedVariables['srch_database'].' DB)</div>';
}      

#   SHOW WHICH PARAMATERS WERE USED TO EXECUTE THE RETRIEVE THE DATA
$xmlFields = new SimpleXMLElement('inc/search.items.xml', null, true);
foreach ($xmlFields as $item) $allFields[(string)$item['id']] = (string)$item->displayName;
$searchParamsRowsArray['Database'] = $postedVariables['srch_database'];
foreach ($postedVariables as $k=>$v) if (isset($allFields[$k])) $searchParamsRowsArray[$allFields[$k]] = $v;
$searchParamsRows = '';
foreach ($searchParamsRowsArray as $k=>$v) {
    if (!empty($v)) $searchParamsRows .= '<tr><td>'.$k.'</td><td>'.$v.'</td><td>&nbsp;</td></tr>';
}

#   ADD THE RESULTSET TO THE EXPORT SESSION VARIABLE SO THAT THE USER CAN DOWNLOAD IT LATER.
$_SESSION['export']['filename'] = 'donor_search_results_'.date("Y-m-d-h-i-s").'.txt';

#   RETURN REQUESTED FORMAT:
switch ($format) {
    case 'json':
        echo json_encode($donors);
        break;
    case 'sateliteHtml':
        require('html/satelite.result.htm');
        break;
    default :
        require('html/'.$curScript.'.htm');
        break;
}