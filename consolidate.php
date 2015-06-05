<?php

include("inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (empty($_GET['dt'])||empty($_GET['dt'])) {
    echo "no parameters specified";
    exit();
}

$runfordate = $_GET['dt'];
$runbydn = ($_GET['dn']=='Y');

$export = exportConsolFiles($runbydn,$runfordate);
$regionList = $GLOBALS['functions']->getRegionList();
$html = '<h2>Data Extract Result</h2><div>'.date("Y-m-d H:i:s").'</div><table cellpadding="0" cellspacing="0" border="0" width="100%">';
if (!empty($export)) {
    foreach ($regionList as $v) {
        $local_file = "/tmp/data/consol".$v['region_id'].".txt";
        $img = ($export[$v['region_id']] && strlen($export[$v['region_id']]) < 5) ? '<img src="img/right.png" alt="Files have been exported" width="24" height="24" align="center">':'<img src="img/wrong.png" alt="'.$export[$v['region_name']].' not exported" width="24" height="24" align="center">';
        $color = ($export[$v['region_id']] && strlen($export[$v['region_id']]) < 5) ? 'D4FF7F' : 'FFA9B2';
        $color2 = ($export[$v['region_id']] && strlen($export[$v['region_id']]) < 5) ? 'green' : 'red';
        $html .= '<tr style="background-color: #'.$color.';color: '.$color2.'"><td height="25" style="padding: 3px">'.$img.'&nbsp;'.$v['region_name'].' '.$local_file.'</td></tr>' . "\n";
    }
    $html .= '</table><p><h2>FTP Result</h2>';
    $html .= file_get_contents('http://129.47.16.21/ftp.php');
} else {
    $html .= '<tr><td>no data extracted</td></tr></table>';
}
echo $html;

function exportConsolFiles($runbydn=false,$month){
    $useMonth = date("Y-m-01",strtotime($month));
    $dates = new financialDates($useMonth);
    $regionList = $GLOBALS['functions']->getRegionList();
    $result = array();
    foreach ($regionList as $v) {
        $outfile = "INTO OUTFILE '/tmp/data/consol".$v['region_id'].".txt'
  FIELDS TERMINATED BY '|'
  LINES TERMINATED BY '|\\n'";
        
        $sql = "SELECT 
	'".$v['region_consol_id']."', ";
        $sql .= ($runbydn) ? 'L':'T';
        $sql .= ".category_id,
	SUM(CASE WHEN `receive_date` BETWEEN '".$dates->startDateCurrentMonth."' AND '".$dates->endDateCurrentMonth."' THEN total_amount ELSE 0 END) `mtd`,
	SUM(CASE WHEN `receive_date` BETWEEN '".$dates->startDateFinancialYear."' AND '".$dates->endDateCurrentMonth."' THEN total_amount ELSE 0 END) `ytd`,
	SUM(CASE WHEN `receive_date` BETWEEN '".$dates->startDatePreviousFinancialYear."' AND '".$dates->endDatePreviousMonth."' THEN total_amount ELSE 0 END) `pytd`," . "\n";
        $sql .= ($runbydn) ? ' 0':' CASE WHEN bud_amount IS NULL THEN 0 ELSE bud_amount END `bud_amount`';
        if ($runbydn) $sql .= "\n" . $outfile;
        $sql .= "
            FROM
`civicrm`.`civicrm_contribution` C
LEFT JOIN `civicrm`.`civicrm_dms_transaction` T ON T.contribution_id = C.id";
        if ($runbydn) {
            $sql .= "\n LEFT JOIN `dms`.`dms_categoryLink` L ON L.denomination = SUBSTR(T.`organisation_id`,2,2)";
        } else {
            $sql .= "\n LEFT JOIN `dms`.`vw_consol_budget` ON T.category_id = bud_category AND T.region_id = bud_region";
        }
        $sql .= "\nWHERE T.category_id <> '9001' AND SUBSTR(T.category_id,2) <> '000' AND T.region_id = ".$v['region_id']."
GROUP BY 2
HAVING `mtd` != 0 OR `ytd` != 0 OR `pytd` != 0";
        if (!$runbydn) $sql .= " OR `bud_amount` != 0 \nUNION
SELECT 
    '".$v['region_consol_id']."',
	bud_category, 
	0,0,0,
	`bud_amount`
    ".$outfile." 
        FROM dms.`vw_consol_budget`
LEFT JOIN (SELECT DISTINCT region_id, category_id FROM `civicrm`.`civicrm_dms_transaction` 
ORDER BY 1,2) AS `t` ON category_id = bud_category AND region_id = bud_region
WHERE `bud_region` = '".$v['region_id']."' AND region_id IS NULL;";
        
        echo '<div style="display:none">'.$sql.'</div>';
        $result[$v['region_id']] = $GLOBALS['db']->execute($sql);
    }
    return $result;  
}