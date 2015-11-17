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

$proxy = '129.47.16.13:3128';
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_URL, "cricscore-api.appspot.com/csa"); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch); 

$result = json_decode($output);
$tRows = '';
if (!empty($result)) {
    foreach ($result as $r) {
        $tRows .= '<tr><td mid='.$r->id.'>'.$r->t1.' vs '.$r->t2.'</td></tr>';
    }
} else {
    $tRows = '<tr><td>No Cricket matches found</td></tr>';
}