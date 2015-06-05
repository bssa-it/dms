<?php

/**
 * @description
 * This script exports the acknowledged contributions to a CSV file.
 * 
 * @author      Chezre Fredericks
 * @date_created 21/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CREATE MENU
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = 'My Downloads';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   GET THE LAST FILE CREATED IN CURRENT SESSION
$fname = (!isset($_SESSION['dms_acknowledgements']['lastFilename'])) ? 'No file created today':$_SESSION['dms_acknowledgements']['lastFilename'];
$noofRecords = (!isset($_SESSION['dms_acknowledgements']['noOfRecords'])) ? '0':$_SESSION['dms_acknowledgements']['noOfRecords'];

#   GET ALL OTHER CSV AND CONSOLIDATED PDF'S CREATED BY THE CURRENT USER
$filenameRecords = $GLOBALS['functions']->GetMailMergeFilenames();
$filenameRows = '<tr><td colspan="5">No Mail Merge Files found for you.</td></tr>';
if (!empty($filenameRecords)) {
    $filenameRows = '';
    foreach ($filenameRecords as $k=>$f) {
        $filenameRows .= "\n\t".'<tr>';
        $filenameRows .= "\n\t\t".'<td>'.$f['ack_document'].'</td>';
        $filenameRows .= "\n\t\t".'<td>'.$f['row count'].'</td>';
        $filenameRows .= "\n\t\t".'<td>'.date("d/m/Y H:i:s",strtotime($f['ack_date'])).'</td>';
        $filenameRows .= "\n\t\t".'<td>'.$_SESSION['dms_user']['fullname'].' ('.$_SESSION['dms_user']['userid'].')</td>';
        $filenameRows .= "\n\t\t".'<td><img src="img/download.png" onclick="downloadFile(\''.$f['ack_document'].'\');" height="16" width="16" style="cursor:pointer" title="download file"/></td>';
        $filenameRows .= "\n\t".'</tr>';   
    }
}

#   SHOW HTML
require('html/'.$curScript.'.htm');