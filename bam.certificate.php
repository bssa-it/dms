<?php

/**
 * @description
 * This script retrieves unprinted BAM certificates from the CiviCRM database and loads them 
 * to be printed.
 * 
 * @author      Chezre Fredericks
 * @date_created 16/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
if (!$_SESSION['dms_user']['authorisation']->isHo) {
    header('location: index.php');
    exit();
} 
$curScript = basename(__FILE__, '.php');

$menu = new menu;
$pageHeading = $title = 'BAM Certicate printing';

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$names = $bamRefs = $langOpts = '';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
$language = (!empty($_GET['lang'])) ? $_GET['lang'] : 'afr';
$limit = (!empty($_GET['limit'])) ? $_GET['limit'] : 0;
$next4 = $GLOBALS['functions']->getNextBAMCertificates($language,$limit);
if (empty($next4)) {
    $language = 'eng';
    $next4 = $GLOBALS['functions']->getNextBAMCertificates($language,$limit);
}
$languages = array('afr'=>'Afrikaans','eng'=>'English','mix'=>'Mix');
foreach ($languages as $k=>$v) {
    $selected = ($k==$language) ? ' SELECTED':'';
    $langOpts .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}

if (!empty($next4)) {
    $cnt = 1;
    foreach ($next4 as $c) {
        $clang = ($c['lang']=='af_ZA') ? 'afr':'eng';
        if ($clang=='eng'&&$cnt<3&&$language=='mix') {
            $cnt++;
            continue;
        }
        $name = strtoupper(trim($c['first_name'] . ' ' . $c['last_name']));
        $names .= "\n" . '<div class="nameDiv" id="name-'.$cnt.'" dnr="'.$c['dnr_no'].'" title="edit" cid="'.$c['contact_id'].'" mid="'.$c['mid'].'" lan="'.$clang.'">'.$name.'</div>';
        $bamRefs .= "\n" . '<div class="bamRefDiv" id="ref-'.$cnt.'">'.$c['ref'].'</div>';
        $cnt++;
    }
}
require('html/'.$curScript.'.htm');