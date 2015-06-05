<?php

/**
 * @description
 * This is the dashboard of the DMS system
 * 
 * @author      Chezre Fredericks
 * @date_created 29/11/2013
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = $title = 'Dashboard';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   ADD HTML - PART 1
include("html/index-part1.htm");

#   FILL WIDGETS
foreach (range(1,4) as $qtrNo) {
    echo '<div class="widgetQtr" id="q'.$qtrNo.'">';
    $widgetId = "usr_q".$qtrNo."_wid_id";
    $wid = $_SESSION['dms_user']['dashboard']->$widgetId;
    if (empty($wid)) {
        echo '&nbsp;';
    } else {
        $w = new widget();
        $w->Load($wid);
        $prompts = simplexml_load_file("widgets/" . $w->wid_directory . "/" . $w->wid_xmlFilename);
        include("widgets/" . $w->wid_directory . "/" . $w->wid_script);
        include("widgets/" . $w->wid_directory . "/" . $w->wid_display);   
    } 
    echo '</div>';
}

#   ADD HTML - PART 1
include('html/index-part2.htm');