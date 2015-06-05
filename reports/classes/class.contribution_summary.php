<?php

Class contribution_summary { 

    var $departmentId;
    var $categoryId;
    var $finDates;
    var $legacyFlag;
    
    var $mtdValueRow;
    var $ytdValueRow;
    var $pytdValueRow;
    var $budgetRow;
    var $percentageChangeYtdPreviousRow;
    var $percentageYtdOfBudgetRow;
    var $percentageRegionOfTotalRow;
    
    var $totals = array('tot_mtd'=>0, 'tot_ytd'=>0,'tot_pytd'=>0,'tot_budget'=>0,'reg_ytd'=>array());

    function __construct($department_id=null,$category_id=null,$financial_dates=null,$legacyFlag='Y') {
        $this->departmentId = $department_id;
        $this->finDates = (empty($financial_dates)) ? new financialDates() : new financialDates($financial_dates);
        $this->legacyFlag = $legacyFlag;
        $this->categoryId = ($this->legacyFlag=='O') ? '8001':$category_id;
        
        $this->mtdValueRow = '<tr><td nowrap>CURRENT MONTH</td>';
        $this->ytdValueRow = '<tr><td nowrap>YEAR TO DATE</td>';
        $this->pytdValueRow = '<tr><td nowrap>PREVIOUS YEAR</td>';
        $this->budgetRow = '<tr><td nowrap>BUDGET</td>';
        $this->percentageChangeYtdPreviousRow = '<tr><td nowrap>% YTD/PREV</td>';
        $this->percentageYtdOfBudgetRow = '<tr><td nowrap>% YTD/BUDG</td>';
        $this->percentageRegionOfTotalRow = '<tr><td nowrap>% AREA/TOT</td>';
        
    }
    
    function getData($regionId) {
        
        $department = (empty($this->departmentId)||$this->departmentId=='All') ? '' : " AND SUBSTR(T.organisation_id,1,1) = '".$this->departmentId."'";
        $category = (empty($this->categoryId)) ? "T.category_id <> '9001' AND SUBSTR(T.category_id,2) <> '000'":"T.category_id = '".str_pad($this->categoryId,4,'0',STR_PAD_LEFT)."'";
        if (preg_match('/[0-9]000/',$this->categoryId)) $category = "SUBSTR(LPAD(T.category_id,4,'0'),1,1) = '" . substr($this->categoryId,0,1) . "'";
        if ($this->legacyFlag=='N') $category .= ' AND T.category_id <> 8001';
        $sql = "
        SELECT 
            category_id,
            SUM(CASE WHEN `receive_date` BETWEEN '".$this->finDates->startDateCurrentMonth."' AND '".$this->finDates->endDateCurrentMonth."' THEN total_amount ELSE 0 END) `mtd`,
            SUM(CASE WHEN `receive_date` BETWEEN '".$this->finDates->startDateFinancialYear."' AND '".$this->finDates->endDateCurrentMonth."' THEN total_amount ELSE 0 END) `ytd`,
            SUM(CASE WHEN `receive_date` BETWEEN '".$this->finDates->startDatePreviousFinancialYear."' AND '".$this->finDates->endDatePreviousMonth."' THEN total_amount ELSE 0 END) `pytd`
        FROM
            `civicrm`.`civicrm_contribution` C
            LEFT JOIN `civicrm`.`civicrm_dms_transaction` T ON T.contribution_id = C.id
        WHERE $category AND T.region_id = ".$regionId." ".$department."
            AND `receive_date` BETWEEN '".$this->finDates->startDatePreviousFinancialYear."' AND '".$this->finDates->endDateCurrentMonth."'
        GROUP BY 
            category_id;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array();
        } else {
            return $result;
        }
    }
    
    function getChartData() {
        $departmentWhere = (empty($this->departmentId)||$this->departmentId=='All') ? '':"SUBSTR(organisation_id,1,1) = '".$this->departmentId."' AND";
        $category = (empty($this->categoryId)) ? " AND T.category_id <> '9001' AND SUBSTR(T.category_id,2) <> '000'":" AND T.category_id = '".str_pad($this->categoryId,4,'0',STR_PAD_LEFT)."'";
        if (preg_match('/[0-9]000/',$this->categoryId)) $category = " AND SUBSTR(LPAD(T.category_id,4,'0'),1,1) = '" . substr($this->categoryId,0,1) . "'";
        if ($this->legacyFlag=='N') $category .= ' AND T.category_id <> 8001';
        
        $sql = "SELECT 
            MONTH(receive_date) `monthId`,
            MONTHNAME(receive_date) `month`,
            SUM(case when `receive_date` between '".$this->finDates->startDateFinancialYear."' and '".$this->finDates->endDateCurrentMonth."' then total_amount else 0 end) `total_ytd`,
            SUM(case when `receive_date` between '".$this->finDates->startDatePreviousFinancialYear."' and '".$this->finDates->endDatePreviousMonth."' then total_amount else 0 end) `total_pytd`
        FROM civicrm_contribution C 
        INNER JOIN civicrm_dms_transaction T ON T.contribution_id = C.id
        WHERE $departmentWhere (`receive_date` between '".$this->finDates->startDatePreviousFinancialYear."' and '".$this->finDates->endDatePreviousMonth."' 
            OR `receive_date` between '".$this->finDates->startDateFinancialYear."' and '".$this->finDates->endDateCurrentMonth."')
            $category
        GROUP BY 1
        ORDER BY receive_date;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array();
        } else {
            return $result;
        }
    }
    
    function createDisplayTable() {
        $thead = '<td valign="bottom">CONTRIBUTOR</td>'; 
        $tbodyValues = $tbodyAggregates = '';
        $regions = $GLOBALS['functions']->GetRegionList();
        
        foreach ($regions as $region) {
            $thead .= "\n\t" . '<td><div>' . strtoupper($region['region_name']) . '</div></td>';
            $data = $this->getData($region['region_id']);
            $_SESSION['dms_report']['data'][$region['region_id']] = $data;
            
            $this->addColumnValues($data,$region['region_id']);
            $this->addBudget($region['region_id']);
        }
        $thead .= "\n\t" . '<td><div>TOTALS</div></td>';
        $this->addTotals();
                
        $tbodyValues .= "\n\t\t" . $this->mtdValueRow;
        $tbodyValues .= "\n\t\t" . $this->ytdValueRow;
        $tbodyValues .= "\n\t\t" . $this->pytdValueRow;
        $tbodyValues .= "\n\t\t" . $this->budgetRow;
        
        $tbodyAggregates .= "\n\t\t" . $this->percentageChangeYtdPreviousRow;
        $tbodyAggregates .= "\n\t\t" . $this->percentageYtdOfBudgetRow;
        $tbodyAggregates .= "\n\t\t" . $this->percentageRegionOfTotalRow;
        
        $table = '<table id="tblReport" width="100%" cellspacing="0" cellpadding="3">';
        $table .= "\n\t" . '<thead><tr>'.$thead.'</tr></thead>';
        $table .= "\n\t" . '<tbody class="rwSpacer"><tr><td colspan="0">&nbsp;</td></tr></tbody>';
        $table .= "\n\t" . '<tbody>'.$tbodyValues.'</tbody>';
        $table .= "\n\t" . '<tbody class="rwSpacer"><tr><td colspan="0">&nbsp;</td></tr></tbody>';
        $table .= "\n\t" . '<tbody>'.$tbodyAggregates.'</tbody>';
        $table .= "\n\t" . '<tfoot><tr><td colspan="0">&nbsp;</td></tr></tfoot>';
        $table .= "\n" . '</table>';
        return $table;
    }
    function addBudget($regionId) {
        $department = (empty($this->departmentId)||$this->departmentId=='All') ? '' : " AND bud_department = '".$this->departmentId."'";
        $category = (empty($this->categoryId)) ? '':" AND bud_category = ".str_pad($this->categoryId,4,'0',STR_PAD_LEFT);
        if (preg_match('/[0-9]000/',$this->categoryId)) $category = " AND SUBSTR(LPAD(bud_category,4,'0'),1,1) = '" . substr($this->categoryId,0,1) . "'";
        if ($this->legacyFlag=='N') $category .= ' AND bud_category <> 8001';
        
        $sql = "
        SELECT 
            sum(bud_amount) `budget`
        FROM
            `dms_budget`
        WHERE 
            bud_region = ".$regionId." ".$department." ".$category.";";
        
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            $budget = 0;
        } else {
            $budget = $result[0]['budget'];
        }
        $percentage = ($budget!=0) ? number_format($this->totals['reg_ytd'][$regionId]/$budget*100,2,'.',',') : '0.00';
        $this->budgetRow .= '<td class="currencyValue" nowrap>' . number_format($budget,2,'.',',') . '</td>';
        $this->percentageYtdOfBudgetRow .= '<td class="percentageValue" nowrap>' . $percentage . '</td>';
        $this->totals['tot_budget'] += $budget;
    }
    function getBudgetTotal() {
        $department = (empty($this->departmentId)||$this->departmentId=='All') ? '' : "bud_department = '".$this->departmentId."'";
        $category = (empty($this->categoryId)) ? '':"bud_category = ".str_pad($this->categoryId,4,'0',STR_PAD_LEFT);
        if (preg_match('/[0-9]000/',$this->categoryId)) $category = "SUBSTR(LPAD(bud_category,4,'0'),1,1) = '" . substr($this->categoryId,0,1) . "'";
        if ($this->legacyFlag=='N') $category .= 'bud_category <> 8001';
        $where = (empty($department)&&empty($category)) ? '' : 'WHERE';
        $sql = "
        SELECT 
            sum(bud_amount) `budget`
        FROM
            `dms_budget`
        $where 
            ".$department;
        if (!empty($department)&&!empty($category)) $sql .= " AND ";
        $sql .= $category;
        #$GLOBALS['functions']->pre_array($sql,true);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            return 0;
        } else {
            return $result[0]['budget'];
        }
    }
    function addColumnValues($data,$regionId) {
        $mtd = $ytd = $pytd = $budget = 0;
        foreach ($data as $k=>$v) {
            $mtd += $v['mtd'];
            $ytd += $v['ytd'];
            $pytd += $v['pytd'];
            $this->totals['tot_mtd'] += $v['mtd'];
            $this->totals['tot_ytd'] += $v['ytd'];
            $this->totals['tot_pytd'] += $v['pytd'];
        }
        $this->totals['reg_ytd'][$regionId] = $ytd;
        $this->mtdValueRow .= '<td class="currencyValue" nowrap>' .number_format($mtd,2,'.',',') . '</td>';
        $this->ytdValueRow .= '<td class="currencyValue" nowrap>' . number_format($ytd,2,'.',',') . '</td>';
        $this->pytdValueRow .= '<td class="currencyValue" nowrap>' . number_format($pytd,2,'.',',') . '</td>';
        
        $percentage = ($pytd!=0) ? number_format($ytd/$pytd*100,2,'.',',') : '0.00';
        $this->percentageChangeYtdPreviousRow .= '<td class="percentageValue" nowrap>' . $percentage . '</td>';
    }
    
    function addTotals() {
        foreach ($this->totals['reg_ytd'] as $r=>$v) {
            $percentage = ($this->totals['tot_ytd']!=0) ? number_format($v/$this->totals['tot_ytd']*100,2,'.',',') : '0.00';
            $this->percentageRegionOfTotalRow .= '<td class="percentageValue" nowrap>' . $percentage . '</td>';
        }
        $this->mtdValueRow .= '<td class="currencyValue" nowrap>' . number_format($this->totals['tot_mtd'],2,'.',',') . '</td></tr>';
        $this->ytdValueRow .= '<td class="currencyValue" nowrap>' . number_format($this->totals['tot_ytd'],2,'.',',') . '</td></tr>';
        $this->pytdValueRow .= '<td class="currencyValue" nowrap>' . number_format($this->totals['tot_pytd'],2,'.',',') . '</td></tr>';
        $this->budgetRow .= '<td class="currencyValue" nowrap>' . number_format($this->totals['tot_budget'],2,'.',',') . '</td></tr>';
        
        
        $percentage = ($this->totals['tot_pytd']!=0) ? number_format($this->totals['tot_ytd']/$this->totals['tot_pytd']*100,2,'.',',') : '0.00';
        $this->percentageChangeYtdPreviousRow .= '<td class="percentageValue" nowrap>' . $percentage . '</td></tr>';
        
        
        $percentage = ($this->totals['tot_budget']!=0) ? number_format($this->totals['tot_ytd']/$this->totals['tot_budget']*100,2,'.',',') : '0.00';
        $this->percentageYtdOfBudgetRow .= '<td class="percentageValue" nowrap>' . $percentage . '</td></tr>';
        $this->percentageRegionOfTotalRow .= '<td class="percentageValue" nowrap>100.00</td></tr>';
    }
}