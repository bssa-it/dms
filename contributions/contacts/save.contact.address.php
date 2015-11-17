<?php 

/**
 * @description
 * This file saves an address for a contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

# first check if this is a sower address
$sowerLocationTypeId = (string)$GLOBALS['xmlConfig']->sowerLocationTypeId['id'];
if (!empty($_POST['location_type_id'])&&$_POST['location_type_id']==$sowerLocationTypeId) {
    $civiContactAddresses = $GLOBALS['functions']->getCiviContactAddresses($_POST['contact_id']);
    foreach ($civiContactAddresses as $a) {
        if ($a['locationTypeId']==$sowerLocationTypeId) {
            $apiParams['version'] = 3;
            $apiParams['id'] = $a['id'];
            $apiParams['location_type_id'] = (string)$GLOBALS['xmlConfig']->previousSowerLocationTypeId['id'];
            $result = civicrm_api('Address','create',$apiParams);
            $jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);
        }
    }
}

$apiParams['version'] = 3;
$apiParams = array_merge($apiParams,$_POST);
$apiParams['is_primary'] = (!empty($_POST['is_primary'])) ? '1':'0';
unset($apiParams['address_is_primary']);
if (empty($_POST['id'])) unset($apiParams['id']);

if (!empty($apiParams['postal_code'])) {
    $geoLatLon = $GLOBALS['functions']->getLatLonFromPostalCode($apiParams['postal_code']);
    $apiParams['geo_code_1'] = (!empty($geoLatLon['lat'])) ? $geoLatLon['lat']:null;
    $apiParams['geo_code_2'] = (!empty($geoLatLon['lon'])) ? $geoLatLon['lon']:null;
}

$result = civicrm_api('Address','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

if (!empty($_POST['is_primary'])) {
    $parms['version'] = 3;
    $parms['id'] = $_POST['address_is_primary'];
    $parms['is_primary'] = '0';
    $updateResult = civicrm_api('Address','create',$parms);
    
    $jsonReturn['result'][] = array('request'=>$parms,'result'=>$updateResult);
     
}

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
$jsonReturn['message'] = "Address detail updated";
echo json_encode($jsonReturn);