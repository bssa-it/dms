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

if (empty($_GET['ticket_id'])) {
    $result['message'] = 'No Ticket specified';
    $result['ticket_id'] = '';
    $result['ticket_closed'] = 'N';
    print json_encode($result);
    exit();
}

$post['tkt_id'] = $_GET['ticket_id'];
$post['tkt_solution'] = 'Bookmaster Accpac Sync completed';
$post['user'] = $_SESSION['dms_user']['username'];
$post['tkt_status'] = 2;

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, "129.47.16.10/api.ticket.php"); 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch); 

echo $output;