<?php

/**
 * @description
 * This script logs the user into joomla
 * 
 * @author      Chezre Fredericks
 * @date_created 27/03/2015
 * @Changes
 * 
 */

/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/

define('_JEXEC',1);
define('JPATH_BASE',preg_replace('/dms/','',dirname(__FILE__)) );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );

/* Create the Application */
$mainframe = JFactory::getApplication('site');
jimport('joomla.plugin.helper');

$credentials = array();
$credentials['username'] = $_POST['username'];
$credentials['password'] = $_POST['password'];

//perform the login action
$error = $mainframe->login($credentials);
$user = JFactory::getUser();
//now you are logged in

if (empty($user->id)) {
    echo "Username/Password incorrect";
} else {
    echo $user->id;
}