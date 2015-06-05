<?php

/**
 * @description
 * This file loads the DMS configuration from the config.xml file for review
 * and edit.
 * 
 * The user must be Joomla Admin to access and edit the config.xml
 * 
 * @author      Chezre Fredericks
 * @date_created 22/11/2013
 * @Changes
 * 21/01/2015 - Chezre Fredericks:
 * Added the show SQL config
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
 
$curScript = basename(__FILE__, '.php');

#   JOOMLA ADMINISTRATOR CHECK 
define( '_JEXEC', 1 );
define('JPATH_BASE','/var/www/joomla' );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$mainframe = JFactory::getApplication('site');
$user = JFactory::getUser();
$isroot	= $user->get('isRoot');

if (!$isroot) {
    #   THIS USER IS NOT AN ADMINISTRATOR, BYE BYE!!!
    header("location: /");
	exit();
}

#   LOAD THE CONFIGURATIONS FROM THE config.xml FILE
$config = simplexml_load_file("../inc/config.xml");
if (!$config) {
    #   THE CONFIG FILE DOES NOT EXIST... BYE BYE!!!!
    header("location: /");
	exit();
}

error_reporting(E_ALL ^ E_WARNING);
#   SET PAGE HEADING
$pageHeading = 'DMS Configuration';


#   FIRST CHECK IF DBs CONNECT
require("../inc/class.db.php");
$dbTest = new database();

######
#
#   THE CONNECTION CHECK ONLY CHECKS THAT THE SPECIFIED USER HAS MYSQL ACCESS TO THE SERVER.  
#   IT DOESNT CHECK IF THE DATABASE EXISTS!!!!
#
######

#   DMS database 
$dbConfig               = $config->databaseConnection->children();
$dms_host               = (string)$dbConfig->host;
$dms_password           = (string)$dbConfig->password;
$dms_username           = (string)$dbConfig->username;
$dms_database           = (string)$dbConfig->database;

$dbTest->host           = $dms_host;
$dbTest->username       = $dms_username;
$dbTest->password       = $dms_password;
$dbTest->database       = $dms_database;

$dms_connectionOk       = $dbTest->connect();
$dms_connectionResult   = ($dms_connectionOk) ? '<img src="../img/robot.png" title="DMS Connection OK!" height="16" width="32" /> DMS Connection OK!':mysql_error();
$dbTest->disconnect();

 
#   Joomla CMS database
$jlConfig               = $config->joomlaConnection->children();
$jml_host               = (string)$jlConfig->host;
$jml_password           = (string)$jlConfig->password;
$jml_username           = (string)$jlConfig->username;
$jml_database           = (string)$jlConfig->database;

$dbTest->host           = $jml_host;
$dbTest->username       = $jml_username;
$dbTest->password       = $jml_password;
$dbTest->database       = $jml_database;

$jml_connectionOk       = $dbTest->connect();
$jml_connectionResult   = ($jml_connectionOk) ? '<img src="../img/robot.png" title="Joomla Connection OK!" height="16" width="32" /> Joomla Connection OK!':mysql_error();
$dbTest->disconnect(); 

#   CiviCRM database
$cvConfig               = $config->civiConnection->children();
$civ_host               = (string)$cvConfig->host;
$civ_password           = (string)$cvConfig->password;
$civ_username           = (string)$cvConfig->username;
$civ_database           = (string)$cvConfig->database;

$dbTest->host           = $civ_host;
$dbTest->username       = $civ_username;
$dbTest->password       = $civ_password;
$dbTest->database       = $civ_database;

$civ_connectionOk       = $dbTest->connect();
$civ_connectionResult   = ($civ_connectionOk) ? '<img src="../img/robot.png" title="CiviCRM Connection OK!" height="16" width="32" /> CiviCRM Connection OK!':mysql_error();
$dbTest->disconnect(); 

#   GET DMS PERMISSIONS
$allUsers = '';
if ($dms_connectionOk&&$jml_connectionOk) {
    $authConfig             = $config->authorisation->children();
    $selectConfig           = (string)$authConfig->select;
    $insertConfig           = (string)$authConfig->insert;
    $updateConfig           = (string)$authConfig->update;
    $deleteConfig           = (string)$authConfig->delete;
    $adminConfig            = (string)$authConfig->admin;
    
    require("../inc/class.functions.php");
    $fn                     = new functions;
    $jmlGroups              = $fn->getJoomlaUserGroups($jlConfig);
    $opts_select            = '';
    $opts_insert            = '';
    $opts_update            = '';
    $opts_delete            = '';
    $opts_admin             = '';
    foreach ($jmlGroups as $ug) {
        $select_selected     = ($ug['id']==$selectConfig) ? ' SELECTED':'';
        $insert_selected     = ($ug['id']==$insertConfig) ? ' SELECTED':'';
        $update_selected     = ($ug['id']==$updateConfig) ? ' SELECTED':'';
        $delete_selected     = ($ug['id']==$deleteConfig) ? ' SELECTED':'';
        $admin_selected      = ($ug['id']==$adminConfig) ? ' SELECTED':'';
        $opts_select        .= '<option value="'.$ug['id'].'"'.$select_selected.'>'.$ug['title'].'</option>'."\n";
        $opts_insert        .= '<option value="'.$ug['id'].'"'.$insert_selected.'>'.$ug['title'].'</option>'."\n";
        $opts_update        .= '<option value="'.$ug['id'].'"'.$update_selected.'>'.$ug['title'].'</option>'."\n";
        $opts_delete        .= '<option value="'.$ug['id'].'"'.$delete_selected.'>'.$ug['title'].'</option>'."\n";
        $opts_admin         .= '<option value="'.$ug['id'].'"'.$admin_selected.'>'.$ug['title'].'</option>'."\n";  
    }
    
    $jmUsers = $fn->getJoomlaUsers($jlConfig,$selectConfig);
    if (!empty($jmUsers)) foreach ($jmUsers as $u) $allUsers .= '<option value="'.$u['id'].'">'.$u['name'].'</option>';
}

#   GET THE CLASSES THAT ARE LOADED WITH THE BOOSTRAPPER
$classConfig             = $config->classes->children();
$tblClassesRows          = '<tr>';
$cnt                     = 1;
foreach ($classConfig as $class) {
    $newRow = ($cnt==1);
    $tblClassesRows .= ($newRow) ? '</tr><tr>':'';
    $tblClassesRows .= '<td>'.(string)$class.'</td>';
    $newRow = false;
    if ($cnt==5) {
        $cnt = 1;   
    } else {
        $cnt++;
    }
}
$tblClassesRows         .= '</tr>';

#   SHOW THE MYSQL TABLES THAT GET IMPORTED FROM THE SCO SERVERS   (SEE ETL PROCEDURE FOR MORE INFO)
$allImportFiles = $config->importFiles->children();
$importFiles = '';
foreach ($allImportFiles as $imfile) {
    $importFiles .= $imfile . ' <br />';
}

#   SHOW THE ACCESS LEVELS THAT CAN BE USED THROUGH OUT THE SYSTEM (MAINLY FOR MAKING USER DEFINED PAGES ACCESSIBLE SYSTEM WIDE)
$allAccessLevels = $config->accessLevels->children();
$accessLevels = '';
foreach ($allAccessLevels as $aLevel) {
    $accessLevels .= $aLevel . ' <br />';
}

#   SHOW THE BUDGET ALLOW-EDIT CONFIGURATION
$budgetYSelected = ($config->budget['allowEdit']=='true') ? ' SELECTED':'';
$budgetNSelected = ($config->budget['allowEdit']=='false') ? ' SELECTED':'';

#   SHOW THE EMAIL CONFIGURATION FOR THE MAIL SERVER
$emailHost = (string)$config->emailconfig->emailhost;
$emailAddress = (string)$config->emailconfig->address;
$emailFromName = (string)$config->emailconfig->name;
$emailPassword = (string)$config->emailconfig->password;
$emailDomain = (string)$config->emailconfig->domain;
$zimbraPreAuthKey = (string)$config->emailconfig->zimbraPreAuthKey; 
$mailchimpApiKey = (string)$config->emailconfig->mailchimpApiKey;
$mandrillHost = (string)$config->emailconfig->mandrill->host;
$mandrillPort = (string)$config->emailconfig->mandrill->port;
$mandrillUsername = (string)$config->emailconfig->mandrill->smtpUsername;
$mandrillApiKey = (string)$config->emailconfig->mandrill->smtpPassword;

#   SHOW THE CIVICRM OPTION GROUP CONFIGURATIONS
$civiPhoneTypesOptGroup = (string)$config->civiOptionGroups->phonetypes;
$languageOptGroup = (string)$config->civiOptionGroups->languageCiviOptGroupId;
$titlesOptGroup = (string)$config->civiOptionGroups->titles;
$genderOptGroup = (string)$config->civiOptionGroups->gender;
$communicationOptGroup = (string)$config->defaultPreferences->communicationMethodCiviOptGroupId;

#   SHOW THE ACKNOWLEDGEMENT COMPONENT CONFIGURATION
$acknowledgementSubject = (string)$config->acknowledgementconfig->defaultemailsubject;
$marginLeft = (string)$config->acknowledgementconfig->marginLeft;
$marginTop = (string)$config->acknowledgementconfig->marginTop;
$marginRight = (string)$config->acknowledgementconfig->marginRight;
$marginBottom = (string)$config->acknowledgementconfig->marginBottom;
$afrSalutation = (string)$config->acknowledgementconfig->languages->language[0]['salutation'];
$engSalutation = (string)$config->acknowledgementconfig->languages->language[1]['salutation'];

#   SHOW THE BAM COMPONENT CONFIGURATION
$bamMemTypeId = (string)$config->bam->civiMembershipTypeId;

#   CIVICRM API CONFIG FILE DIRECTORY
$apiCrmConfig = (string)$config->civicrmApi->crmConfigFile;
$apiCrmCoreConfig = (string)$config->civicrmApi->crmCoreConfigFile;

#   SHOW SQL options
$showSqlOpts = '';
$ynOpts = array('Y','N');
foreach ($ynOpts as $v) {
    $selected = ($config->showSql['value']==$v) ? ' SELECTED':'';
    $showSqlOpts .= '<option value="'.$v.'" '.$selected.'>'.$v.'</option>';
}

# Sower settings
$sowerAddressTypeId = (string)$config->sowerLocationTypeId['id'];

# Versions
$dmsVersion = (string)$config->versions->dms;
$civicrmVersion = (string)$config->versions->civicrm;
    
#   ADD THE HTML
require('../html/configuration.htm');