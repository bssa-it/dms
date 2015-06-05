<?php

 /**
 * @description
 * This script loads the budget vs actual widget.
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * 
 * @Changes
 * 28/03/2015 - Chezre Fredericks:
 * File created
 * 
 */

include("../../inc/globals.php");
$finDates = new financialDates();
$primaryDepartment = $_SESSION['dms_user']['config']['impersonate'];
$fyStartDate = $GLOBALS['functions']->getFinancialYearStartDate();
$sql = "SELECT 
    MONTH(receive_date) `monthId`,
    MONTHNAME(receive_date) `month`,
    SUM(case when `receive_date` between '".$finDates->startDateFinancialYear."' and '".$finDates->endDateCurrentMonth."' then total_amount else 0 end) `total_ytd`,
    SUM(case when `receive_date` between '".$finDates->startDatePreviousFinancialYear."' and '".$finDates->endDatePreviousMonth."' then total_amount else 0 end) `total_pytd`
FROM civicrm_contribution C 
INNER JOIN civicrm_dms_transaction T ON T.contribution_id = C.id
WHERE SUBSTR(organisation_id,1,1) = '$primaryDepartment' AND ((`receive_date` between '".$finDates->startDateFinancialYear."' and '".$finDates->endDateCurrentMonth."')
    OR (`receive_date` between '".$finDates->startDatePreviousFinancialYear."' and '".$finDates->endDatePreviousMonth."'))
GROUP BY 1
ORDER BY receive_date;";

$seriesDescriptions = '';
$data = $GLOBALS['civiDb']->select($sql);
$returnSource = array();

if (!empty($data)) {
    $d = new department();
    $d->Load($primaryDepartment);
    $currMonth = '';
    $months = array(11=>1,12=>2,1=>3,2=>4,3=>5,4=>6,5=>7,6=>8,7=>9,8=>10,9=>11,10=>12);
    $monthlyBudget = $d->dep_budgetAllocation/12;
    $total = $pTotal = 0;
    foreach ($data as $k=>$v) {
        $total += $v['total_ytd'];
        $pTotal += $v['total_pytd'];
        $budget = $monthlyBudget*$months[$v['monthId']];
        
        $d = array();
        $d['month'] = $v['month'];
        $d['actual'] = $total;
        $d['pactual'] = $pTotal;
        $d['budget'] = $budget;
        $returnSource[] = $d;
    }
   
}
echo json_encode($returnSource);