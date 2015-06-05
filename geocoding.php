<?php

/**
 * @description
 * This page helps with the geocoding of postal codes
 * 
 * @author      Chezre Fredericks
 * @date_created 20/06/2014
 * @Changes
 * 
 */
 #   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = 'Geocoding';
$title = $pageHeading;
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   GOOGLE API KEY
$config = simplexml_load_file("inc/config.xml");
$apiKey = (string)$config->googleApiKey;

#$sql = 'SELECT * FROM dms_postalCodes where pco_lat is null and pco_lon is null limit 100';
$sql = 'SELECT * FROM dms_postalCodes where pco_lat is null and pco_lon is null';
$result = $GLOBALS['functions']->GetDataFromSQL($sql);

$tr = '';
foreach ($result as $r) {
   $tr .= '<tr><td><input type="checkbox" name="ids[]" onchange="findMe(this.parentNode.parentNode);" value="' . $r['pco_id']; 
    $tr .= '" /></td><td>'.$r['pco_area']."</td><td>".$r['pco_suburb'].'</td>';
    $tr .= '<td><div style="float: right;height:16px;width:16px;cursor:pointer"><img src="img/search-black-32.png" width="16" height="16"  onclick="findArea(this.parentNode.parentNode.parentNode);" /></div></td>';
    $tr .= '<td>'.$r['pco_postal_code'].'</td>';
    $tr .= '<td width="40"><input type="text" value="'.$r['pco_lon'].'" name="lon[]" class="latLng" onchange="checkBox(this.parentNode.parentNode)" /></td>';
    $tr .= '<td width="40"><input type="text" value="'.$r['pco_lat'].'" name="lat[]" class="latLng" onchange="checkBox(this.parentNode.parentNode)" /></td></tr>';
}

require('html/'.$curScript.'.htm');


#https://maps.googleapis.com/maps/api/geocode/xml?address=Gauteng,lyttleton&key=AIzaSyCUGfwjehI3OPkzrn1PhslT6LQiAMTVqm8

?>