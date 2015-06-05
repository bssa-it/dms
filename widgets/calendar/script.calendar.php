<?php

 /**
 * @description
 * This script loads the calendar widget.
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * 
 * @Changes
 * 28/03/2015 - Chezre Fredericks:
 * File created
 * 
 */
$useDate = date("Y-m-01");
$firstOfMonth = date("w",strtotime($useDate));
$lastOfMonth = date("t",strtotime($useDate));
$monthStarted = false;
$cnt = 1;
foreach (range(0, 41) as $i) {
    if (!$monthStarted) $monthStarted = ($i==$firstOfMonth);
    if ($cnt>$lastOfMonth) break; 
    $month[$i] = ($monthStarted) ? $cnt:null;
    if ($monthStarted) $cnt++;
}
$calRows = '';
$sundays = array(0,7,14,21,28,35);
$saturdays = array(6,13,20,27,34,41);
foreach ($month as $k=>$v) {
    $num = empty($v) ? '&nbsp;':$v;
    if (in_array($k,$sundays)) $calRows .= '<tr>';
    $calRows .= '<td idx="'.$k.'">'.$num.'</td>';
    if (in_array($k, $saturdays)) $calRows .= '</tr>';
}
$currMonth = date("F",strtotime($useDate));
$currYear = date("Y",strtotime($useDate));