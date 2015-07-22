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
$d = new civicrm_dms_department_extension();
$allDepartments = $d->getAll();
if (!empty($options)) {
    if (isset($options['dp'])) {
        $return = new civicrm_dms_department_extension($options['dp']);
    }
    if (isset($options['office'])) {
        foreach ($allDepartments as $dep) {
            if ($dep['dep_office_id']==$options['office']) $return[] = $dep;
        }
    }
} 
if (empty($return)) $return = $allDepartments;
print json_encode($return);