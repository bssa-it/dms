<?php

/**
 * @description
 * This script retrieves the denomination list and returns a json object
 * 
 * @author      Chezre Fredericks
 * @date_created 19/06/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");

$options = array();
if (!empty($_POST)) $options = $_POST;
if (!empty($_GET)) $options = $_GET;


$return = array();
if (!empty($options)) {
    if (isset($options['dp'])) {
        $dp = new civicrm_dms_department_extension($options['dp']);
        $return = $dp->getDistinctDenominations();
    }
    if (isset($options['dn'])) {
        $return = new civicrm_dms_denomination_extension($options['dn']);
    }
}
if (empty($return)) {
    $d = new civicrm_dms_denomination_extension();
    $return = $d->getAll();;
}

print json_encode($return);