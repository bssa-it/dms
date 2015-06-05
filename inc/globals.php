<?php

/**
 * @description
 * This file initializes the global components for the DMS.
 * 
 * @author      Chezre Fredericks
 * @date_created 21/11/2013
 * @Changes
 * 
 * 3.0.2
 * 08/07/2014 - Chezre Fredericks:
 * a) added version to globals superglobal
 * 
 * 5.0.1
 * 26/03/2015 - Chezre Fredericks:
 * use absolute path to find inc directory.
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
define('DMS_PATH', '/var/www/joomla/dms/');
$GLOBALS['dms_base_path'] = DMS_PATH;

#   Get XML configurations
$xmlConfig = simplexml_load_file(DMS_PATH . "inc/config.xml");

#   Include classes
$classesConfig          = $xmlConfig->classes->children();
foreach ($classesConfig as $class) require($class);

#   Load GLOBAL functions
$GLOBALS['functions']   = new functions;

#   Connect to the DMS database
$dbConfig               = $xmlConfig->databaseConnection;
$db                     = new database;
$db->username           = (string)$dbConfig->username;
$db->password           = (string)$dbConfig->password;
$db->host               = (string)$dbConfig->host;
$db->database           = (string)$dbConfig->database;
$connectionOk = $db->connect();
if (!$connectionOk) {
    header('location: /dms/configuration/');
    exit();   
}
$GLOBALS['db']          = $db;

#   CiviCRM and Joomla database connection settings:
$dbConfig               = $xmlConfig->civiConnection;
$civiDb                     = new database;
$civiDb->username           = (string)$dbConfig->username;
$civiDb->password           = (string)$dbConfig->password;
$civiDb->host               = (string)$dbConfig->host;
$civiDb->database           = (string)$dbConfig->database;
$civiDb->connect(true);
$GLOBALS['civiDb']      = $civiDb;
$GLOBALS['civiDBConnectionDetails'] = $xmlConfig->civiConnection;
$GLOBALS['joomlaDBConnectionDetails'] = $xmlConfig->joomlaConnection;

#   user defined access levels
$allAccessLevels        = $xmlConfig->accessLevels->children();
foreach ($allAccessLevels as $accessLevel) $GLOBALS['accessLevels'][$accessLevel->getName()] = (string)$accessLevel;

#   2014-07-08 Chezre Fredericks 3.0.2.a:
#   current versions
$GLOBALS['currentCivicrmVersion'] = (string)$xmlConfig->versions->civicrm;
$GLOBALS['currentDmsVersion'] = (string)$xmlConfig->versions->dms;
#   end 3.0.2.a

#   add the civicrm api config files
require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

#   add config to globals
$GLOBALS['xmlConfig'] = $xmlConfig;

#   load user with permissions and user config (loaded into the Joomla session)
require("security.php");