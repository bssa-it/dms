<?php

 /**
 * @description
 * This script loads the cricket widget.
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * 
 * @Changes
 * 21/07/2015 - Chezre Fredericks:
 * File created
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

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
    
    $class = 'opq';
    $tktId = $tktClosed = '';
    $date = date("Y-m-").str_pad($v,2,'0',STR_PAD_LEFT);
    if (!empty($v)&&isWorkDay($date)&&$v<=date("d")) {
        $synced = checkIfDateWasSynced($date);
        $class = ((string)$synced->{'ticket_closed'}=='Y') ? 'opq':'red';
        $tktId = ' tktId="'. (string)$synced->{'ticket_id'} . '"';
        $tktClosed = ' tktClosed="'. (string)$synced->{'ticket_closed'} . '"';
    }
    $calRows .= '<td class="'.$class.'"'.$tktId.$tktClosed.'>'.$num.'</td>';
    if (in_array($k, $saturdays)) $calRows .= '</tr>';
}

$currMonth = date("F",strtotime($useDate));
$currYear = date("Y",strtotime($useDate));

function checkIfDateWasSynced($date) {
    $post['tkt_type_id'] = 48;
    $post['tkt_opendate'] = $date;
    $post['user'] = 'ithelpdesk';
    $post['checkIfSyncTicketExists'] = 'Y';
    
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, "129.47.16.10/api.ticket.php"); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    curl_close($ch); 

    $data = json_decode($output);
    return $data;
}

function isWorkDay($date) {
    
    $xml = simplexml_load_file('widgets/accpac/holidays.xml');
    $holidays = array();
    foreach ($xml->children() as $d) {
        $dt['dayName'] = $d['desc'];
        $dt['date'] = $d['value'];
        
        $holidays[] = (string)$d['value'];
    }
    $isWeekend = (date('N', strtotime($date)) >= 6);
    return (!in_array($date,$holidays)&&!$isWeekend);
    
}