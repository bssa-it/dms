<?php

/**
 * @description
 * This script gets the body of the report.
 * 
 * @author      Chezre Fredericks
 * @date_created 07/05/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("../../../inc/globals.php");
include("../../classes/class.contribution_summary.php");

if (empty($_POST)) {
    echo 'Prompts not set';
    exit();
}

$department = $_POST['department_id'];
$legacy = $_POST['include_legacy'];
$level = ($legacy=='O') ? 2:$_POST['level_id'];
$category = ($level>0&&is_numeric($_POST['category_id'])) ? str_pad($_POST['category_id'],4,'0',STR_PAD_LEFT):null;
if ($legacy=='O') $category = '8001';
$month = (!empty($_POST['date'])) ? date("Y-m-t",strtotime($_POST['date'])):null;


if ($_POST['department_id']=='All') {
    $departmentName = 'All Departments';
} else {
    $d = new department();
    $d->Load($department);
    $departmentName = $d->dep_id . ' - ' .  $d->dep_name;
}
$reportLevels = array('Grand Summary','Category Totals','Category');
$inReportPrompts = $theadRows = $tbodyRows = $tfootRows = '';
switch ($level) {
    case 0:
        $reportHeading = 'Grand Summary (' . $departmentName . ')';
        break;
    default:
        $categoryName = $GLOBALS['functions']->GetCategoryName($category);
        $reportHeading = $category.' - '.$categoryName;
        break;
}


$breadCrumbs = '';
foreach ($reportLevels as $k=>$v) {
    if ($k>$level) break;
    switch ($k) {
        case 0:
            $value = 'SUM';
            break;
        case 1:
            $value = substr($category,0,1) . '000';
            break;
        case 2:
            $value = $category;
            break;
    }
    $display = ($k>0) ? $v.' ('.$value.')':$v;
    $breadCrumbs .= '<li val="'.$value.'">&gt; '.$display.'</li>';
}


$inReportPrompts = '<input type="hidden" id="ip_level_id" value="'.$level.'" />';

$options = array();
$defaultValue = (empty($month)) ? date("Y-m"):date("Y-m",strtotime($month));
$options['type'] = 'month';
$dateInput = $GLOBALS['functions']->getInput('','ip_date',$defaultValue,$options);
$inReportPrompts .= '<div class="lbl">Date</div><div class="ipInput">'.$dateInput.'</div>';

$options = array();
$options['showAll'] = 'Y';
$options['showValueWithText'] = 'Y';
$options['options']['option'] = array('db'=>'db','tableName'=>'dms_department','valueField'=>'dep_id','textField'=>'dep_name');
$departmentSelect = preg_replace('/\\n/','',$GLOBALS['functions']->getSelect('','ip_department_id',$department,$options));
$inReportPrompts .= '<div class="lbl">Department</div><div class="ipInput">'.$departmentSelect.'</div>';


$options = array();
$options['showValueWithText'] = 'Y';
$options['options']['option'][] = array('value'=>'SUM','text'=>'Grand Summary');
$options['options']['option'][] = array('db'=>'db','tableName'=>'dms_category','valueField'=>'cat_id','textField'=>'cat_name');
$categorySelect = preg_replace('/\\n/','',$GLOBALS['functions']->getSelect('','ip_category_id',$category,$options));
$inReportPrompts .= '<div class="lbl">Category</div><div class="ipInput">'.$categorySelect.'</div>';

$showLegacy = (empty($category)||$category=='SUM') ? '':'style="display: none"';
$options = array();
$options['options']['option'][] = array('value'=>'Y','text'=>'Yes');
$options['options']['option'][] = array('value'=>'N','text'=>'No'); 
$options['options']['option'][] = array('value'=>'O','text'=>'Only');
$legacySelect = preg_replace('/\\n/','',$GLOBALS['functions']->getSelect('','ip_include_legacy',$legacy,$options));
$inReportPrompts .= '<div id="legacyInput" '.$showLegacy.'><div class="lbl">Legacies and Trusts</div><div class="ipInput">'.$legacySelect.'</div></div>';

$cs = new contribution_summary($department,$category,$month,$legacy);
$table = $cs->createDisplayTable();



?>
<script type="text/javascript">
    $(document).ready(function(){
       <?php if (!empty($inReportPrompts)) { ?>
       $("#inReportPrompts").empty().append('<?php echo preg_replace('/\'/','\\\'',$inReportPrompts); ?>');
       $("#promptContainer").show();
       <?php } ?>
       bindCategoryChange();
       bindBreadcrumbsClick();
       bindLegacyChange();
   });
</script>

<div id="reportHeading">
    <?php echo $reportHeading; ?>
</div>

<div id="reportTableDiv">
    <?php echo $table; ?>
</div>

<div id="reportBreadCrumbs"><div style="float: left;margin-top: 5px;">You are here: </div><ul><?php echo $breadCrumbs; ?></ul></div>
