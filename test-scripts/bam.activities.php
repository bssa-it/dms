<?php 

/**
 * @description
 * This file saves a note into the CiviCRM database for a contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/07/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
#   SKELETON BOOTSTRAP
include("inc/class.db.php");
$xmlConfig = simplexml_load_file("inc/config.xml");

$civiConfig             = $xmlConfig->civiConnection;
$civiDb                 = new database;
$civiDb->username       = (string)$civiConfig->username;
$civiDb->password       = (string)$civiConfig->password;
$civiDb->host           = (string)$civiConfig->host;
$civiDb->database       = (string)$civiConfig->database;
$civiDb->connect(true);
$GLOBALS['db'] = $civiDb;
        
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once (string)$xmlConfig->civicrmApi->crmConfigFile;
require_once (string)$xmlConfig->civicrmApi->crmCoreConfigFile;
$config = CRM_Core_Config::singleton();

$session = new CRM_Core_Session;
$session->set('userID',2);

$sql = "SELECT id,contact_id,join_date FROM civicrm_membership WHERE membership_type_id = 1";

$memberships = $GLOBALS['db']->select($sql);
if (!empty($memberships)) {
    foreach ($memberships as $k=>$v) {
        $apiParams['version'] = 3;
        $apiParams['source_contact_id'] = 2;
        $apiParams['source_record_id'] = $v['id'];
        $apiParams['activity_type_id'] = 7;
        $apiParams['subject'] = 'Bible-a-Month Club - Status: New';
        $apiParams['activity_date_time'] = $v['join_date'];
        $apiParams['status_id'] = 2;
        $apiParams['priority_id'] = 2;
        $result = civicrm_api('Activity','create',$apiParams);
                
        $contact['version'] = 3;
        $contact['activity_id'] = $result['id'];
        $contact['contact_id'] = $v['contact_id'];
        $contact['record_type_id'] = 3;
        $cResult = civicrm_api('ActivityContact','create',$contact);
        
    }
}

echo "done";