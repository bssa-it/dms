<?php

/**
 * @description
 * This script retrieves the group totals
 * 
 * @author      Chezre Fredericks
 * @date_created 15/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include '../../inc/globals.php';
$xml = simplexml_load_file("../../logs/latest.xml");
$array = $GLOBALS['functions']->xml2array($xml);

print json_encode($array);