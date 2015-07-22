<?php

/**
 * @description
 * This page loads the donor report details edit form.
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

#   LOAD DONOR DETAIL
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
if (!$dnr) {
    echo 'not found';
    exit();
}
$contactId = $dnr['contact_id'];
$donorReportDetail = $GLOBALS['functions']->getCiviDmsDonorReportDetail($contactId);
$civiOrg = $GLOBALS['functions']->getCiviDmsOrganisation($donorReportDetail['organisation_id']);
$congregationName = (empty($civiOrg['org_name'])) ? 'Unknown':$civiOrg['org_name'];
$regionId = (empty($civiOrg['org_region'])) ? '' : $civiOrg['org_region'];
$department = substr($donorReportDetail['organisation_id'],0,1);
$departmentName = $GLOBALS['functions']->GetDepartmentName($department);
$allDepartments = $GLOBALS['functions']->GetDepartmentList();
$departmentOpts = '';
$javaColors = "\n" .'var dpColors = [];';
if (!empty($allDepartments)) {
    foreach ($allDepartments as $d) {
        $selected = ($department==$d['dep_id']) ? ' SELECTED':'';
        $departmentOpts .= '<option value="' . $d['dep_id'] . '"'.$selected.'>' . $d['dep_id'] .' - '.$d['dep_name'].'</option>';
        if ($department==$d['dep_id']&&empty($depColor)) $depColor = $d['dep_chart_color'];
        $javaColors .= "\n" . 'dpColors.push({dept: "'.$d['dep_id'].'",color: "' . trim($d['dep_chart_color']) . '"});';
    }
    if (empty($depColor)) $depColor = '#A7B526';
}
$denominations = $GLOBALS['functions']->getDistinctDepartmentDenominations();
$javaDenominations = "\n" . 'var denominations = [];';
$denominationOpts = '';
$denomination = substr($donorReportDetail['organisation_id'],1,2);
foreach ($denominations as $dn) {
    $javaDenominations .= "\n" .'denominations.push({ dep: "'.$dn['dep_id'].'", den: "'.$dn['den_id'].'", den_name: "'.$dn['den_name'].'" });';
    $selected = ($dn['den_id']==$denomination) ? ' SELECTED':'';
    $denominationOpts .= ($dn['dep_id']==$department) ?  "\n" .'<option value="'.$dn['den_id'].'"'.$selected.'>'.$dn['den_id'].' - '.$dn['den_name'].'</option>':'';
}

$congregations = $GLOBALS['functions']->getDistinctDenominationCongregations();
$javaCongregations = "\n" . 'var congregations = [];';
foreach ($congregations as $cn) {
    $cnRegion = (empty($cn['region'])) ? '0':$cn['region'];
    $javaCongregations .= "\n" . 'congregations.push({ den: "'.$cn['den'].'", con_id: "'.$cn['con_id'].'", con_name: "'.$cn['org_name'].'", region: '.$cnRegion.' });';
}

$categories = $GLOBALS['functions']->GetCategoryList();
$categoryOpts = '';
foreach ($categories as $c) {
    $selected = ($donorReportDetail['category_id']==$c['cat_id']) ? ' SELECTED':'';
    $categoryOpts .= '<option value="'.$c['cat_id'].'"'.$selected.'>'.str_pad($c['cat_id'],4,'0',STR_PAD_LEFT).' - '.$c['cat_name'].'</option>';
}
        
#   SHOW THE HTML
require('html/'.$curScript.'.htm');