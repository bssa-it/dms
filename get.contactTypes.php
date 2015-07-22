<?php

/**
 * @description
 * This script retrieves the contact type list and returns a json object
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

$contactTypes = $GLOBALS['functions']->getCiviContactTypes();
$return = array();
if (!empty($contactTypes)) {
    foreach ($contactTypes as $t) {
        $parent = (!empty($t['parent_id'])) ? $contactTypes[$t['parent_id']]['label']:$t['label'];
        $return[] = array("name"=>$t['label'], "parent"=>$parent);
    }
}

print json_encode($return);