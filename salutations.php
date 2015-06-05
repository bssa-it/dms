<?php

/**
 * @description
 * This script will save extra salutations for a specific denomination and category in a department
 * 
 * @author      Chezre Fredericks
 * @date_created 09/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');

$menu = $GLOBALS['functions']->createMenu();
$pageHeading = $title = 'Alternate Salutation Rules';

$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

$mySalutationRows = $depOpts = $denOpts = $catOpts = $typeOpts = $langOpts = '';

$allDepartments = $GLOBALS['functions']->GetDepartmentList();
foreach ($allDepartments as $d) {
    $depOpts .= '<option value="' . $d['dep_id'] . '">' . $d['dep_id'] .' - '.$d['dep_name'].'</option>';
}


$denominations = $GLOBALS['functions']->GetDenominationList();
foreach ($denominations as $dn) {
    $denOpts .= "\n" .'<option value="'.$dn['den_id'].'">'.$dn['den_id'].' - '.$dn['den_name'].'</option>';
}

$categories = $GLOBALS['functions']->GetCategoryList();
foreach ($categories as $c) {
    $catOpts .= '<option value="'.$c['cat_id'].'">'.str_pad($c['cat_id'],4,'0',STR_PAD_LEFT).' - '.$c['cat_name'].'</option>';
}

$types = $GLOBALS['xmlConfig']->acknowledgementconfig->salutations->type;
foreach ($types as $t) {
    $typeOpts .= '<option value="'.$t['value'].'">'.$t['value'].'</option>';
}

$languages = $GLOBALS['xmlConfig']->acknowledgementconfig->languages->language;
foreach ($languages as $l) {
    $langOpts .= '<option value="'.$l['value'].'">'.$l['desc'].'</option>';
}

$allSalutations = $GLOBALS['functions']->getMySalutations();
if (!empty($allSalutations)) {
    foreach ($allSalutations as $k=>$v) {
        $mySalutationRows .= "\n" . '<tr salId="'.$v['sal_id'].'" class="salut">';
        $mySalutationRows .= '<td>'.$v['sal_department_id'].'</td>';
        $mySalutationRows .= '<td>'.$v['sal_denomination_id'].'</td>';
        $mySalutationRows .= '<td>'.$v['sal_category_id'].'</td>';
        $mySalutationRows .= '<td>'.$v['sal_type'].'</td>';
        $mySalutationRows .= '<td>'.$v['sal_language'].'</td>';
        $mySalutationRows .= '<td>'.$v['sal_text'].'</td>';
        $mySalutationRows .= '</tr>';
    }
} else {
    $mySalutationRows .= "\n" . '<tr salId=""><td colspan="6">No Salutation Rules</td></tr>';
}
require('html/'.$curScript.'.htm');