<?php

/**
 * @description
 * This checks if the user is logged in and establishes the user permission.  
 * User permissions are stored in the $_SESSION variable for use throughout 
 * server.
 * 
 * The Donor management system uses the Joomla platform, so naturally we 
 * use Joomla authentication in this file.
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * @copyright   None
 * @version     3.0.2
 * 
 * @Changes
 * 22/11/2013 - Chezre Fredericks:
 * Re-Author security check
 * 
 * @ChangesVersion 3.0.2
 * 11/06/2014 - Chezre Fredericks:
 * Add impersonation security setup.
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

session_start();

#   JOOMLA AUTHENTICATION
define('_JEXEC',1);
define('JPATH_BASE',preg_replace('/dms\/inc/','',dirname(__FILE__)) );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$mainframe = JFactory::getApplication('site');

if (!empty($_SESSION['dms_user']['impersonateUserId'])) {
    $user = new joomlauser();
    $user->Load($_SESSION['dms_user']['impersonateUserId']);
} else {
    $user = JFactory::getUser();
}

if (empty($user->id)) {
    # SO SORRY!!!! PLEASE LOG IN!!!
    session_destroy();
    header("location: /dms/login.php");
    exit();
}

#   START BUILDING THE SESSION ARRAY
$_SESSION['dms_user']['userid'] = $user->id;
$_SESSION['dms_user']['username'] = $user->username;
$_SESSION['dms_user']['fullname'] = $user->name;
if (empty($_SESSION['dms_user']['impersonateUserId'])) $_SESSION['dms_user']['impersonateUserId'] = null;

#   LOAD USER'S PERMISSIONS
$_SESSION['dms_user']['authorisation'] = new authorisation($user);

#   CHECK IF USER HAS SELECT PERMISSION, OTHERWISE KICK HIM OUT    
if (!$_SESSION['dms_user']['authorisation']->accessOk) {
    
    # SO SORRY!!!! PLEASE LOG IN!!!
    session_destroy();
    header("location: /dms/login.php");
    exit();
    # MOO HOO HAHAHAHA.... :(
}

#   NOW THE USER HAS OFFICIALLY LOGGED ONTO THE DMS, THE LOGIN TIME CAN BE ESTABLISHED
if (empty($_SESSION['dms_user']['ipaddress'])) $_SESSION['dms_user']['ipaddress'] =  $GLOBALS['functions']->getRealIpAddr();
if (empty($_SESSION['dms_user']['logintime'])) $_SESSION['dms_user']['logintime'] =  date("Y-m-d H:i:s");
if (!empty($_SESSION['dms_user']['lasttouch'])) {
    $securitConfig = simplexml_load_file('/var/www/joomla/dms/inc/security.config.xml');
    $lt = new DateTime($_SESSION['dms_user']['lasttouch']);
    
    #  dormant minutes
    $diff = $lt->diff(new DateTime());
    $dormantMinutes = ((int)$diff->h*60)+$diff->i;
    $_SESSION['dms_user']['dormantminutes'] = $dormantMinutes."m:".$diff->s."s";
    $kickUserOut = false;
    $kickUserOut = ($dormantMinutes > (int)$securitConfig->insideVPNSessionLength);
    
    if (!$kickUserOut) $kickUserOut = (!preg_match('/'.(string)$securitConfig->regexVPNstring.'/',$_SESSION['dms_user']['ipaddress'])&&$dormantMinutes > (int)$securitConfig->outOfVPNSessionLength);
    if ($kickUserOut) {
        session_destroy();
        header("location: /dms/login.php");
        exit();
    }
}
$_SESSION['dms_user']['lasttouch'] = date("Y-m-d H:i:s");

#   LOAD USER DASHBOARD WIDGETS
if (!class_exists('user')) require_once 'db-classes/class.user.php';
$u = new user();
$u->Load($user->id);
$_SESSION['dms_user']['dashboard'] = $u;
$_SESSION['dms_user']['civ_contact_id'] = $u->usr_contact_id;

#   LOAD USER CONFIG
$userConfig = simplexml_load_file($GLOBALS['dms_base_path'] . "users/" . $user->id .".user.xml");
$_SESSION['dms_user']['office_id'] = (string)$userConfig->officeId;
$_SESSION['dms_user']['config']['userType'] = (string)$userConfig->userType;
$_SESSION['dms_user']['config']['donorSearch']['defaultDatabase'] = (string)$userConfig->donorSearch->defaultDatabase;
$_SESSION['dms_user']['config']['donorSearch']['defaultBamOnly'] = (string)$userConfig->donorSearch->defaultBamOnly;
$_SESSION['dms_user']['config']['donorSearch']['defaultDonorDeleted'] = (string)$userConfig->donorSearch->defaultDonorDeleted;
$_SESSION['dms_user']['config']['departments'] = array();
foreach ($userConfig->acknowledgement->departments->department as $dpt) {
    $_SESSION['dms_user']['config']['departments'][(string)$dpt['code']] = (string)$dpt->secretary;
    if (empty($_SESSION['dms_user']['config']['impersonate'])) 
        $_SESSION['dms_user']['config']['impersonate'] = ((string)$dpt['impersonate']=='Y') ? (string)$dpt['code']:'';
}

#   Civi User log in
$session = new CRM_Core_Session;
$session->set('userID',$_SESSION['dms_user']['civ_contact_id']);