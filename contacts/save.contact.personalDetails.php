<?php 

/**
 * @description
 * This file saves the donor's personal details to CiviCRM.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 * 4.1.1 - 11/03/2015 - Chezre Fredericks
 * Remove contact from sower group and delete sower address if the contact was in the sower group.
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$jsonReturn['post'] = $_POST;

#   First Remove from sower group and delete sower addresses
if ($_POST['contact_is_deleted']==1) {
    $groups = $GLOBALS['functions']->getContactGroups($_POST['id']);
    foreach ($groups['values'] as $g) {
        if ((int)$g['group_id']===(int)$GLOBALS['xmlConfig']->civiGroups->sower) {
            $GLOBALS['functions']->deleteFromCiviGroup($g['id']);
        }
    }
    $GLOBALS['functions']->deleteSowerAddresses($_POST['id']);
}

$apiParams['version'] = 3;
$apiParams = array_merge($apiParams,$_POST);
$primaryContactTypes = array('Individual','Household','Organization');
if (in_array(trim($apiParams['contact_sub_type']),$primaryContactTypes)) $apiParams['contact_sub_type'] = '';
unset($apiParams['id_number']);
$apiParams['custom_20'] = $_SESSION['dms_user']['civ_contact_id'];
$apiParams['modified_date'] = date("Y-m-d H:i:s");

$result = civicrm_api('Contact','create',$apiParams);
$jsonReturn['result'][] = array('request'=>$apiParams,'result'=>$result);

$c = new civicrm_dms_contact_other_data_extension($apiParams['id']);
$c->id_number = $_POST['id_number'];
$result = $c->Save();
$jsonReturn['result'][] = array('request'=>$c,'result'=>$result);

$jsonReturn['message'] = "Contact personal details updated";
echo json_encode($jsonReturn);