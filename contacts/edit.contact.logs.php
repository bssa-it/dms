<?php

/**
 * @description
 * This page loads the log of the contact's changes
 * 
 * @author      Chezre Fredericks
 * @date_created 11/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if (!isset($_GET['d'])||empty($_GET['d'])) {
    echo 'not found';
    exit();
}

#   SET VITAL VARIABLES
$dnrNo = $_GET['d'];

#   LOAD DONOR DETAIL
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
if (!$dnr) {
    echo 'not found';
    exit();
}
$contactId = $dnr['contact_id'];
$log = $GLOBALS['functions']->getContactLog($contactId);
$logRows = '';
if (!empty($log)) {
    foreach ($log as $l) {
        $c = $GLOBALS['functions']->getCiviContactByQuery($l['modified_id']);
        $logRows .= "\n" . '<tr>';
        $logRows .= '<td>' . $l['modified_date'] . '</td>';
        $logRows .= '<td>' . $l['data'] . '</td>';
        $logRows .= '<td>' . $c['display_name'] . '</td>';
        $logRows .= '</tr>';
    }
} else {
    $logRows .= '<tr><td colspan="3">Contact record as not been changed yet.</td></tr>';
}

#   SHOW THE HTML
require('html/'.$curScript.'.htm');