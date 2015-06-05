<?php

/**
 * @description
 * This script removes a contact from a group
 * 
 * @author      Chezre Fredericks
 * @date_created 11/02/2015
 * @Changes
 * 4.1.1 - 11/03/2015 - Chezre Fredericks:
 * Delete sower postal address if its the sower group
 * 
 * 
 */
 
#   SKELETON BOOTSTRAP
include("inc/globals.php");

$group['version'] = 3;
$group['id'] = $_POST['id'];
$result = civicrm_api('GroupContact', 'get', $group);
$jsonReturn['result'][] = array('request'=>$group,'result'=>$result);

#   mark sower addresses as previous when deleting contact from sower group
$GLOBALS['functions']->markSowerAsPreviousAddresses($_POST['contact_id']);

#   Remove contact from group
$result = $GLOBALS['functions']->deleteFromCiviGroup($_POST['id']);
$jsonReturn['result'][] = array('request'=>"\$GLOBALS['functions']->deleteFromCiviGroup(\$_POST['id'])",'result'=>$result);

#   Log contact last modified
$GLOBALS['functions']->logContactChange($_POST['contact_id']);
echo json_encode($jsonReturn);