<?php

/**
 * @description
 * This script loads the user's config settings.
 * 
 * @author      Chezre Fredericks
 * @date_created 12/05/2014
 * @Changes
 * 02/04/2015 - Chezre Fredericks - 5.0.1:
 * Added Javascript array for user's widgets
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$menu = new menu;
$pageHeading = $title = 'My Configuration';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   USER CONFIG
$myConfigFilename = "users/".$_SESSION['dms_user']['userid'].".user.xml";
$myConfig = simplexml_load_file($myConfigFilename); 
$userType = (string)$myConfig->userType;

#   USER DETAIL
$username = $_SESSION['dms_user']['username'];
$userFullname = $_SESSION['dms_user']['fullname'];
$userId = $_SESSION['dms_user']['userid'];
$civiId = $_SESSION['dms_user']['civ_contact_id'];
$civiContact = $GLOBALS['functions']->getCiviContact($civiId);
$dnrId = $civiContact['external_identifier'];
$ipaddress = $_SESSION['dms_user']['ipaddress'];
$loginTime = $_SESSION['dms_user']['logintime'];


#   LOAD WIDGET ID'S THAT BELONG TO THIS USER 
$q1WidgetId = (!empty($_SESSION['dms_user']['dashboard']->usr_q1_wid_id)) ? $_SESSION['dms_user']['dashboard']->usr_q1_wid_id:-1;
$q2WidgetId = (!empty($_SESSION['dms_user']['dashboard']->usr_q2_wid_id)) ? $_SESSION['dms_user']['dashboard']->usr_q2_wid_id:-1;
$q3WidgetId = (!empty($_SESSION['dms_user']['dashboard']->usr_q3_wid_id)) ? $_SESSION['dms_user']['dashboard']->usr_q3_wid_id:-1;
$q4WidgetId = (!empty($_SESSION['dms_user']['dashboard']->usr_q4_wid_id)) ? $_SESSION['dms_user']['dashboard']->usr_q4_wid_id:-1;


#   02/04/2015 - JAVASCRIPT ARRAY FOR USER'S WIDGETS
$usersWidgets = "\n" . 'var userWidgets = [];';
foreach (range(1,4) as $qtr) {
    $wid = "usr_q".$qtr."_wid_id";
    if (!empty($_SESSION['dms_user']['dashboard']->$wid)) {
        $w = new widget();
        $w->Load($_SESSION['dms_user']['dashboard']->$wid);
        $usersWidgets .= "\n" . 'userWidgets.push("'.$w->wid_directory.'");';
    }
}
#   JAVASCRIPT ARRAY FOR ALL WIDGETS & INIT FOR WIDGET OPTION LIST
$widOpts = '<option value="">-- select --</option>';
$widJavaObj = "\n".'var widgetList = []';
$allWidgets = $GLOBALS['functions']->getAllWidgetTemplates();
foreach ($allWidgets as $k=>$v) {
    $widOpts .= '<option value="'.$v['wid_directory'].'">'.$v['wid_directory'].'</option>';
    $widJavaObj .= "\n". 'widgetList.push("'.$v['wid_directory'].'");';
}

#   LOAD SPECIFIED WIDGET
if (!empty($_GET['i'])) $wId = $_GET['i']; 
if (empty($wId)) $wId = $q1WidgetId;

#   INSERT HTML
require('html/'.$curScript.'.htm');