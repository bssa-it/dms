<?php

/**
 * @description
 * This script gets the chart data for the report.
 * 
 * @author      Chezre Fredericks
 * @date_created 24/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("../../../inc/globals.php");
include("../../classes/class.contribution_summary.php");

$department = $_POST['department_id'];
$legacy = $_POST['include_legacy'];
$level = ($legacy=='O') ? 2:$_POST['level_id'];
$category = ($level>0&&is_numeric($_POST['category_id'])) ? str_pad($_POST['category_id'],4,'0',STR_PAD_LEFT):null;
if ($legacy=='O') $category = '8001';
$month = (!empty($_POST['date'])) ? date("Y-m-t",strtotime($_POST['date'])):null;

$seriesDescriptions = $dataSource = '';
$cs = new contribution_summary($department,$category,$month,$legacy);
$data = $cs->getChartData();
$budgetTotal = $cs->getBudgetTotal();
        
if ($_POST['department_id']=='All') {
    $departmentName = 'All Departments';
} else {
    $d = new department();
    $d->Load($department);
    $departmentName = $d->dep_id . ' - ' .  $d->dep_name;
}


if (!empty($data)) {
    $currMonth = '';
    $months = array(11=>1,12=>2,1=>3,2=>4,3=>5,4=>6,5=>7,6=>8,7=>9,8=>10,9=>11,10=>12);
    $monthlyBudget = $budgetTotal/12;
    $total = $ptotal = 0;
    foreach ($data as $k=>$v) {
        $total += $v['total_ytd'];
        $ptotal += $v['total_pytd'];
        $budget = $monthlyBudget*$months[$v['monthId']];
        if (!empty($dataSource)) $dataSource .= ',';
        $dataSource .= "\n" . '{ month: "'.$v['month'].'"';
        $dataSource .= ', actual:' . $total . ',budget: ' . $budget . ', pactual: '.$ptotal.' }' ;
    }
    

    $toolTipFormat = 'largeNumber';
    
?>
<script type="text/javascript">
   $(document).ready(function(){
    var dSource = [<?php echo $dataSource; ?>];
    var chartOptions = {
    dataSource: dSource,
    commonSeriesSettings: {
        type: "spline",
        argumentField: "month",
        font: { color: "#254B7C" }
        
    },
    commonAxisSettings: {
        color: '#254B7C',
        grid: {
            visible: true,
            color: '#254B7C'
        },
        label: {
            font: { color: '#254B7C' }
        }
    },
    series: [
        { valueField: "budget", name: "Budget", color: "#254B7C" },
        { valueField: "actual", name: "Actual", color: "#CD3333" },
        { valueField: "pactual", name: "Previous", color: "#3e6730" }
    ],
    tooltip:{
        enabled: true,
        format: '<?php echo $toolTipFormat; ?>',
        
    },
    legend: {
        visible: true
    },
    commonPaneSettings: {
        border:{
            visible: false
        }
    },
    title: {
        text: 'YTD vs PYTD vs Budget (<?php echo $departmentName; ?>)',
        verticalAlignment: 'top'
    },
    valueAxis: [{
        label: {
            format: '<?php echo $toolTipFormat; ?>'
        }
    }]
};

var chart = $("#budActChart").dxChart(chartOptions);
bindCategoryChange();
bindLegacyChange();
});
</script>
<div class="chartsDiv">
    <div id="budActChart"></div>
</div>

<?php 

} else { 
    echo "No data for " . $departmentName; 
}