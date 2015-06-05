<?php

/**
 * @description
 * Department management for Donor Reports
 * 
 * @author      Chezre Fredericks
 * @date_created 13/11/2013
 * @Changes
 * 20/01/2015 - Chezre Fredericks:
 * File updated for version 4.0.1 upgrade
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   IS AUTHORISED
$authorised = $_SESSION['dms_user']['authorisation']->isAdmin;
if (!$authorised) $GLOBALS['functions']->goToIndexPage();

#   CREATE MENU
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = $title = 'Department Manager';

$dp_options                 = '';
$departmentDetails          = '';
$budgetTotal                = 0;
$departmentList             = $GLOBALS['functions']->GetDepartmentList();
$isFirstData                = true;
$departmentOptions          = array();
$javascriptGraphData        = '';

foreach ($departmentList as $v) $budgetTotal += (!is_null($v['dep_budgetAllocation'])) ? $v['dep_budgetAllocation'] : 0;
foreach ($departmentList as $v) {
    
    $departmentOptions[trim($v['dep_id'])] = $v['dep_name'];  
    
    $allocatedBudget = (!is_null($v['dep_budgetAllocation'])) ? $v['dep_budgetAllocation'] : 0;
    $budgetPercentage = $allocatedBudget/$budgetTotal*100;
    $departmentDetails .= "\n\t\t".'<tr>';
    $departmentDetails .= "\n\t\t\t".'<td>'.$v['dep_id'].'</td>';
    $departmentDetails .= "\n\t\t\t".'<td>'.trim($v['dep_name']).'</td>';
    $departmentDetails .= "\n\t\t\t".'<td>'.$v['dep_defaultRegion'].'</td>';
    $departmentDetails .= "\n\t\t\t".'<td>'.$v['dep_isNational'].'</td>';
    $departmentDetails .= "\n\t\t\t".'<td align="right">R '.number_format($v['dep_budgetAllocation'],2,'.',' ').'</td>';
    $departmentDetails .= "\n\t\t\t".'<td style="background-color:'.$v['dep_chartColor'].';color:'.$v['dep_chartColor'].'">'.$v['dep_chartColor'].'</td>';
    $departmentDetails .= "\n\t\t\t".'<td>'.$v['dep_fromEmailName'].'</td>';
    $departmentDetails .= "\n\t\t\t".'<td>'.$v['dep_fromEmailAddress'].'</td>';
    $departmentDetails .= "\n\t\t\t".'<td>'.$v['dep_contact_id'].'</td>';
    $departmentDetails .= "\n\t\t\t".'<td align="right">'.number_format($budgetPercentage,2,'.',' ').' %</td>';
    $departmentDetails .= "\n\t\t".'</tr>';
    
    if (!$isFirstData) $javascriptGraphData .= ',';
    $javascriptGraphData .= '{
                	      value: '.$v['dep_budgetAllocation'].',
                	      color: \''.$v['dep_chartColor'].'\'
                        }';
    $isFirstData = false;
    
}

$rg_options         = '';
$regionList         = $GLOBALS['functions']->GetRegionList();
foreach ($regionList as $v) {
    $rg_options .= '<option value="'.$v['region_id'].'">'.$v['region_id'].' - '.$v['region_name'].'</option>';
}

$totalAllocatedBudget       = number_format($budgetTotal,2,'.',' ');

#   LOAD AND RETURN HTML
require('html/'.$curScript.'.htm');