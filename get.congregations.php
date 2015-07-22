<?php

/**
 * @description
 * This script retrieves the congregation list and returns a json object
 * 
 * @author      Chezre Fredericks
 * @date_created 18/06/2015
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

$o = new civicrm_dms_organisation_extension();
$allOrganisations = $o->getAll();
$return = array();
if (!empty($options)) {
    if (isset($options['dp'])) {
        $depDn = $options['dp'];
        $s = 0;
        $c = 1;
    }
    if (isset($options['dn'])) {
        $depDn = $options['dn'];
        $s = 1;
        $c = 2;
    }
    if (isset($options['dp'])&&isset($options['dn'])) {
        $depDn = $options['dp'].$options['dn'];
        $s = 0;
        $c = 3;
    }
    foreach ($allOrganisations as $o) {
        if ($depDn==substr($o['org_id'],$s,$c)) $return[] = $o;
    }
} 
if (empty($return)) $return = $allOrganisations;

print json_encode($return);