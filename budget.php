<?php

/**
 * @description
 * This is the budget maintenance page of the DMS system
 * 
 * @author      Chezre Fredericks
 * @date_created 30/01/2014
 * @Changes
 * 
 */
 
#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   CAN DELETE PERMISSION
$deleteAuthorization = $_SESSION['dms_user']['authorisation']->isHo;

#   CHECK IF EDIT IS ALLOWED
$xmlConfig = simplexml_load_file("inc/config.xml");
$editable = ((string)$xmlConfig->budget['allowEdit']=='true');
$editAllowed = ($editable) ? 'validateFilter()':'editDisabled()';

#   CREATE MENU
$menu = new menu;
$finYear = $GLOBALS['functions']->getFinancialYear();
$pageHeading = 'DMS Budget Manager <span style="font-size: 14pt;">FY '.$finYear.'</span>';
$title = 'Budget Manager';

#   LOAD REGION, DEPARTMENT AND CATEGORY FROM SESSION 
$_SESSION['dmsBudget']['bud_region']     = (isset($_SESSION['dmsBudget']['bud_region'])) ? $_SESSION['dmsBudget']['bud_region'] : null;
$_SESSION['dmsBudget']['bud_region']     = (isset($_GET['r'])) ? $_GET['r'] : $_SESSION['dmsBudget']['bud_region'];

$_SESSION['dmsBudget']['bud_department'] = (isset($_SESSION['dmsBudget']['bud_department'])) ? $_SESSION['dmsBudget']['bud_department'] : null;
$_SESSION['dmsBudget']['bud_department'] = (isset($_GET['d'])) ? $_GET['d'] : $_SESSION['dmsBudget']['bud_department'];

$_SESSION['dmsBudget']['bud_category']   = (isset($_SESSION['dmsBudget']['bud_category'])) ? $_SESSION['dmsBudget']['bud_category'] : null;
$_SESSION['dmsBudget']['bud_category']   = (isset($_GET['c'])) ? $_GET['c'] : $_SESSION['dmsBudget']['bud_category'];

#   LOAD DATA FOR GRAPHS
$regionList         = $GLOBALS['functions']->GetRegionList();
$regionOptions      = '';
foreach ($regionList as $v) {
    $selected = ($_SESSION['dmsBudget']['bud_region']==$v['region_id']) ? ' SELECTED':''; 
    $regionOptions .= '<option value="'.$v['region_id'].'"'.$selected.'>'.$v['region_id'].' - '.$v['region_name'].'</option>';
}
$departmentList             = $GLOBALS['functions']->GetDepartmentList();
$departmentOptions          = '';
$departmentJavascriptArray  = 'var departments = [';
$isFirstDepartment          = true;
foreach ($departmentList as $v) {
    $selected = ($_SESSION['dmsBudget']['bud_department']==$v['dep_id']) ? ' SELECTED':''; 
    $departmentOptions .= ($v['dep_budget_allocation']>0) ? '<option value="'.$v['dep_id'].'"'.$selected.'>'.$v['dep_id'].' - '.$v['dep_name'].'</option>':'';
    $departmentJavascriptArray .= ($isFirstDepartment) ? '':',';
    $isFirstDepartment = false;
    $departmentJavascriptArray .= '{
            depId: "'.$v['dep_id'].'",
            desc: "'.$v['dep_id'].' - '.$v['dep_name'].'",
            isNational: "'.$v['dep_is_national'].'",
            value: '.$v['dep_budget_allocation'].'
        }';
}
$departmentJavascriptArray .= "\n]";

$categoryList       = $GLOBALS['functions']->GetCategoryList(true);
$categoryOptions    = '';
foreach ($categoryList as $v) {
    $selected = ($_SESSION['dmsBudget']['bud_category']==$v['cat_id']) ? ' SELECTED':'';
    $departmentsAllowed = (empty($v['cat_departments'])) ? '*' : preg_replace('/;/','',$v['cat_departments']);
    $categoryOptions .= '<option value="'.$v['cat_id'].'-'.$departmentsAllowed.'"'.$selected.'>'.str_pad($v['cat_id'],4,'0',STR_PAD_LEFT).' - '.$v['cat_name'].'</option>';
}

#   LOAD BUDGET DETAILS
$budgetDetails  = '';
$budgetList     = $GLOBALS['functions']->getBudgets();
$departmentTotal = array();
foreach ($budgetList as $v) {
    $display = 'table-row';
    if (!is_null($_SESSION['dmsBudget']['bud_region'])&&$_SESSION['dmsBudget']['bud_region']!=$v['bud_region']) $display = 'none';
    if (!is_null($_SESSION['dmsBudget']['bud_department'])&&$_SESSION['dmsBudget']['bud_department']!=$v['bud_department']) $display = 'none';
    if (!is_null($_SESSION['dmsBudget']['bud_category'])&&$_SESSION['dmsBudget']['bud_category']!=$v['bud_category']) $display = 'none';
    $canDelete = ($deleteAuthorization&&$editable) ? '':'class="idCol"';
    $budgetDetails .= '<tr onclick="setBudgetId(this)" style="display: '.$display.';background-color: '.$v['dep_chartColor'].'">';
    $budgetDetails .= '<td class="idCol">'.$v['bud_id'].'</td>';
    $budgetDetails .= '<td>'.$v['bud_department'].' - '.$v['dep_name'].'</td>';
    $budgetDetails .= '<td>'.$v['bud_region'].' - '.$v['region_name'].'</td>';
    $budgetDetails .= '<td>'.str_pad($v['bud_category'],4,'0',STR_PAD_LEFT).' - '.$v['cat_name'].'</td>';
    $budgetDetails .= '<td align="right">'.number_format($v['bud_amount'],2,'.',' ').'</td>';
    $budgetDetails .= '<td align="center"  onclick="deleteBudget(this);" '.$canDelete.'><img src="img/trash-16.png" alt="delete budget" /></td>';
    $budgetDetails .= '<td class="idCol">'.trim($v['dep_chartColor']).'</td>';
    $budgetDetails .= '</tr>';
    
    $departmentTotal[$v['bud_department']]['total'] = (isset($departmentTotal[$v['bud_department']]['total'])) ? $departmentTotal[$v['bud_department']]['total']+$v['bud_amount']:$v['bud_amount'];
    $departmentTotal[$v['bud_department']]['color'] = $v['dep_chartColor'];
    #echo $v['bud_department'] . ' - ' . $v['dep_chartColor'] . '<br />';
}

#   LOAD CHART DATA
$depChartData               = 'var depChartData = [';
$colorPallette              = 'var cPalette = [';
$isFirstDepartment = true;
foreach ($departmentTotal as $d=>$t) {
    $colorPallette .= ($isFirstDepartment) ? '':',';
    $depChartData .= ($isFirstDepartment) ? '':',';
    $isFirstDepartment = false;
    $depChartData .= '{
            dp: "'.$d.'",
            val: '.$t['total'].'
        }';
    $colorPallette .= "\n'".trim($t['color'])."'";
    #echo $d . ' ' . $t['color'] . '<br />';
}
$depChartData .= "];";
$colorPallette .= "];";

$budgetCount    = count($budgetList);
$fillCount      = 13 - $budgetCount;    
if ($fillCount > 0 ) for ($i=0;$i<=12;$i++) $budgetDetails .= '<tr><td colspan="6">&nbsp;</td></tr>';

$perspective = (!is_null($_SESSION['dmsBudget']['bud_department']) && $GLOBALS['functions']->isNationalDepartment($_SESSION['dmsBudget']['bud_department'])) ? 'national':'regional';

#   LOAD AND RETURN HTML
require('html/'.$curScript.'.htm');