<?php

/**
 * @description
 * add new widget to user.   This is done by the user.
 * 
 * @author      Chezre Fredericks
 * @date_created 02/04/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

$jmlUserId = $_SESSION['dms_user']['userid'];
$u = new user;
$u->Load($jmlUserId);
$w = new widget();
$widgetPosition = 'usr_q'.$_POST['p'].'_wid_id';
$widgetExists = $w->Load($u->$widgetPosition);
if (!$widgetExists||$w->wid_directory!=$_POST['w']) {
    $tmplFile = $GLOBALS['dms_base_path'].'widgets/'.$_POST['w'].'/tmpl.'.$_POST['w'].'.xml';
    $widgetFile = preg_replace('/tmpl/',$jmlUserId,$tmplFile);
    copy($tmplFile,$widgetFile);
    $w->copyTemplate($_POST['w'], $jmlUserId);    # <-  This saves the widget as well.
}
$u->$widgetPosition = $w->wid_id;
$u->Save();

$returnArray['newWidgetId'] = $w->wid_id;
foreach (range(1,4) as $qtr) {
    $wpos = 'usr_q'.$qtr.'_wid_id';
    if (!empty($u->$wpos)) {
        $w->Load($u->$wpos);
        $returnArray['q'.$qtr] = $w->wid_directory;
    } else {
        $returnArray['q'.$qtr] = '0';
    }
}
        
echo json_encode ($returnArray);