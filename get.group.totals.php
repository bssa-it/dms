<?php

/**
 * @description
 * This script retrieves the group totals
 * 
 * @author      Chezre Fredericks
 * @date_created 15/04/2015
 * @Changes
 * 
 */
include("inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$groups = $GLOBALS['functions']->getGroups();
if ($groups['count']>0) {
    $groupDivs = '';
    foreach ($groups['values'] as $g) {
        $total = $GLOBALS['functions']->getTotalContactsInGroup($g['id']);
        $groupDivs .= '<div class="groupSummaryDiv" gid="'.$g['id'].'"><div class="groupName">'.$g['title'].'</div>';
        $groupDivs .= '<div class="groupTotal">'.$total.'</div></div>';
    }
    echo $groupDivs;
} else {
    echo '<div class="errMessageDiv">Sorry, no groups found</div>';
}