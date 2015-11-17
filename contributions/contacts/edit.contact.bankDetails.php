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
$accounts = $GLOBALS['functions']->getOrderAccounts($dnrNo);
$accountRows = '<tr><td colspan="3">No bank details available</td></tr>';
if (!empty($accounts)) {
    $accountRows = '';
    foreach ($accounts as $a) {
        $accountRows .= "\n" . '<tr>';
        $accountRows .= '<td>'.$a['ordr_acct_no'].'</td>';
        $accountRows .= '<td>'.$a['ordr_bnk_id'].'</td>';
        $accountRows .= '<td>'.$a['ordr_ref'].'</td>';
        $accountRows .= '</tr>';
    }
}

#   SHOW THE HTML
require('html/'.$curScript.'.htm');