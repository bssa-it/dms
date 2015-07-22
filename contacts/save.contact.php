<?php 

/**
 * @description
 * This file saves a new contact
 * 
 * @author      Chezre Fredericks
 * @date_created 22/06/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
include("inc/class.contact.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

$c = new contact($_POST);
$c->save();
$jsonReturn['result'][] = array('request'=>'$c->createNewContact()','result'=>$c);

# EMAIL
$isPrimary = 1;
if (!empty($_POST['email'])) {
    foreach ($_POST['email'] as $e) {
        if (empty($e)) continue;
        $parms['version'] = 3;
        $parms['is_primary'] = $isPrimary;
        $isPrimary = 0;
        $parms['email'] = $e;
        $parms['location_type_id'] = 1;
        $parms['contact_id'] = $c->civicrmApiObject['id'];
        $updateResult = civicrm_api('Email','create',$parms);
        $jsonReturn['result'][] = array('request'=>$parms,'result'=>$updateResult);
    }
}

# PHONE
$isPrimary = 1;
if (!empty($_POST['phone'])) {
    foreach ($_POST['phone'] as $p) {
        if (empty($p)) continue;
        $parms['version'] = 3;
        $parms['is_primary'] = $isPrimary;
        $isPrimary = 0;
        $parms['phone'] = $p;
        $parms['location_type_id'] = 1;
        $parms['contact_id'] = $c->civicrmApiObject['id'];
        $updateResult = civicrm_api('Phone','create',$parms);
        $jsonReturn['result'][] = array('request'=>$parms,'result'=>$updateResult);
    }
}

# ADDRESS
$isPrimary = 1;
if (!empty($_POST['street_address'])) {
    foreach ($_POST['street_address'] as $k=>$s) {
        $addresses[$k]['street_address'] = $s;
        $addresses[$k]['isPrimary'] = $isPrimary;
        $addresses[$k]['location_type_id'] = 1;
        $addresses[$k]['contact_id'] = $c->civicrmApiObject['id'];
        $isPrimary = 0;
    }
}


$cnt = 1;
foreach (range(1, 3) as $r) {
    if (!empty($_POST['supplemental_address_'.$r])) {
        foreach ($_POST['supplemental_address_'.$r] as $k=>$s) $addresses[$k]['supplemental_address_'.$r] = $s;
    }
}
if (!empty($_POST['city'])) {
    foreach ($_POST['city'] as $k=>$s) $addresses[$k]['city'] = $s;
}
if (!empty($_POST['postal_code'])) {
    foreach ($_POST['postal_code'] as $k=>$s) $addresses[$k]['postal_code'] = $s;
}

if (!empty($addresses)) {
    foreach ($addresses as $a) {
        if (empty($a)) continue;
        $parms['version'] = 3;
        $parms['is_primary'] = $isPrimary;
        $parms = array_merge($parms,$a);
        $updateResult = civicrm_api('Address','create',$parms);
        $jsonReturn['result'][] = array('request'=>$parms,'result'=>$updateResult);
    }
}

$jsonReturn['message'] = "Contact created";
print json_encode($jsonReturn);