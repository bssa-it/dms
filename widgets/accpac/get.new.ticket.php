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

include '../../inc/globals.php';

if (empty($_GET['dt'])) {
    $result['message'] = 'No Date specified';
    $result['ticket_id'] = '';
    $result['ticket_closed'] = 'N';
    print json_encode($result);
    exit();
}

$isToday = (date('Y-m-d')==date('Y-m-d',strtotime($_GET['dt'])));

$fdate = ($isToday) ? date("Y-m-d H:i:s") : date('Y-m-d 00:00:01',strtotime($_GET['dt']));
$sdate = date("Y-m-d",strtotime($fdate));
$post['tkt_type_id'] = 48;
$post['tkt_opendate'] = $fdate;
$post['user'] = $_SESSION['dms_user']['username'];
$post['tkt_detail'] = 'Bookmaster Accpac Sync ticket created for ' . $sdate;
$post['tkt_title'] = 'Bookmaster Accpac Sync for ' . $sdate;
$post['tkt_status'] = 1;

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, "129.47.16.10/api.ticket.php"); 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch); 

echo $output;