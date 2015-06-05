<?php

/**
 * @description
 * This script checks if you are admin.  if not, user will be redirected to dashboard
 * 
 * @author      Chezre Fredericks
 * @date_created 13/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
session_start();

#   JOOMLA AUTHENTICATION
define('_JEXEC',1);
define('JPATH_BASE',preg_replace('/dms\/logs\/grapevine\/requests/','',dirname(__FILE__)) );
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

$isroot = $user->authorise('core.admin');

if ($isroot) {
    $files = glob('*');
    $fileList = '';
    foreach ($files as $f) {
        $fileList .= '<li class="filenam" path="'.$f.'">' . $f . '</li>';
    }
    require('../../../html/index.htm');
} else {
    header('location:/');
}