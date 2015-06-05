<?php 

/**
 * @description
 * This script saves the required geocoding.
 * 
 * @author      Chezre Fredericks
 * @date_created 23/06/2014
 * @Changes
 * 
 */
#   BOOTSTRAP
include("inc/globals.php");

$lat = array();
foreach ($_POST['lat'] as $klat=>$vlat) if (!empty($vlat)) $lat[] = $vlat;

$lon = array();
foreach ($_POST['lon'] as $klon=>$vlon) if (!empty($vlon)) $lon[] = $vlon;

foreach ($_POST['ids'] as $k=>$v) {
    $p = new postalcodes;
    $p->Load($v);
    $p->pco_lat = $lat[$k];
    $p->pco_lon = $lon[$k];
    $p->Save();
    
    $intPostalCode = (int)$p->pco_postal_code;
    $civi[$intPostalCode] = array('postalCode'=>$p->pco_postal_code,'lat'=>$p->pco_lat,'lon'=>$p->pco_lon);
}

if (!empty($civi)) {
    $civiRecords = array();
    foreach ($civi as $k=>$v) {
        $apiParams['version'] = 3;
        $apiParams['postal_code'] = $k;
        $result = civicrm_api('address','get',$apiParams);
        
        if ($result['values']>0) {
            foreach ($result['values'] as $r) $updateRecords[] = array('id'=>$r['id'],'geo_code_1'=>$v['lat'],'geo_code_2'=>$v['lon']);    
        } else {
            $strParams['version'] = 3;
            $strParams['postal_code'] = $v['postalCode'];
            $strResult = civicrm_api('address','get',$apiParams);
            if ($strResult['values']>0) {
                foreach ($strResult['values'] as $r) $updateRecords[] = array('id'=>$r['id'],'geo_code_1'=>$v['lat'],'geo_code_2'=>$v['lon']);    
            }   
        }
           
    }   
}

foreach ($updateRecords as $k=>$v) {
    $update['version'] = 3;
    $update = array_merge($update,$v);
    $updateResult = civicrm_api('address','create',$update);
}

#   GO BACK...
header('location:geocoding.php');