<?php

/**
 * @description
 * This script finds the suburb based on the search criteria
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

include("inc/globals.php");

$donorNo = $contactId = $_POST['search'];
$donorName = (preg_match('/\*/i',$_POST['search'])) ? preg_replace('/\*/','%',$_POST['search']):'%'.$_POST['search'].'%';
$sql = "SELECT 
            id `contact_id`, 
            external_identifier `dnr_no`, 
            sort_name `display_name`
        FROM 
            `civicrm_contact` c 
        WHERE
        (id = '$contactId' OR external_identifier = '$donorNo' OR sort_name LIKE '$donorName')
        AND contact_sub_type = 'Staff'
        ORDER BY sort_name;";
$GLOBALS['functions']->showSql($sql);
$results = $GLOBALS['functions']->GetCiviDataFromSQL($sql);

$resultDivs = 'No staff members found';
if (!empty($results)) {
    $resultDivs = '';
    foreach ($results as $k=>$v) {
        $resultDivs .= '<div class="contactSearchResult" dnrno="'.$v['dnr_no'].'">'.$v['dnr_no'].'::'.$v['display_name'].'</div>';
    }
}

echo '<script>$(".contactSearchResult").bind("click",fillMe);</script>';
echo $resultDivs;