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

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (empty($_GET['batch_id'])) {
    echo "Invalid batch number";
    exit();
}

#   BOOTSTRAP
include("../inc/globals.php");

$be = new civicrm_dms_batch_entry_extension();
$entries = $be->loadBatchEntries($_GET['batch_id']);

$b = new civicrm_dms_batch_header_extension($_GET['batch_id']);

?>
<table width="100%" cellspacing="0" cellpadding="5" id="tblBatchEntries" class="tblDetails">
    <thead>
        <tr>
            <td>Entry No</td>
            <td>Date Received</td>
            <td>Received By</td>
            <td>Receipt No</td>
            <td>Receipt Type</td>
            <td>Receipt Amount</td>
        </tr>
    </thead>
    <tbody>
        <?php if (count($entries)>0&&  is_array($entries)) {
            foreach ($entries as $e) { 
            echo '<tr eid="'.$e['id'].'" class="batchRow">';
            echo '<td>' . $e['entry_no'] . '</td>';
            echo '<td>' . $e['received_datetime'] . '</td>';
            echo '<td>' . $e['user'] . '</td>';
            echo '<td>' . $e['receipt_no'] . '</td>';
            echo '<td>' . $e['receipt_type'] . '</td>';
            echo '<td>' . $e['receipt_amount'] . '</td>';
            echo '</tr>';
        }} else {
            echo '<tr><td colspan="6">No entries found</td></tr>';
        } ?>
    </tbody>
</table>