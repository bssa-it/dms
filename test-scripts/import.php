<?php

/**
 * @description
 * This script imports new contacts into the CiviCRM database.
 * 
 * @author      Chezre Fredericks
 * @package     za.co.biblesociety.dmsextension
 * @copyright   None
 * @version     4.0.1
 * 
 * @Changes
 * 02/12/2014 - Chezre Fredericks:
 * File created
 * 
 * 4.0.1 changes:
 * File Created
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   SKELETON BOOTSTRAP
include("inc/class.db.php");

$xmlConfig = simplexml_load_file("inc/config.xml");

$dbConfig               = $xmlConfig->databaseConnection;
$db                     = new database;
$db->username           = (string)$dbConfig->username;
$db->password           = (string)$dbConfig->password;
$db->host               = (string)$dbConfig->host;
$db->database           = (string)$dbConfig->database;
$db->connect(true);

require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

#   GET NEW DONORS
$sql = "SELECT civ_contact_id `id`,dnr_title `title` FROM dms_donor WHERE civ_contact_id IS NOT NULL AND LENGTH(dnr_title) > 0 limit 5;";

# GET NEW DONORS
$donors = $db->select($sql);
if (!empty($donors)) {
    # START THE IMPORT
    foreach ($donors as $k=>$v) {
        try {
            $insertParams = array();
            foreach ($v as $parameter=>$value) $insertParams[$parameter] = $value;
            $result = civicrm_api3('Contact', 'create', $insertParams);

            echo "<pre />";
            print_r($result);
        } catch (Exception $e) {
            echo "<pre />";
            print_r($e);
        }
    }
}
