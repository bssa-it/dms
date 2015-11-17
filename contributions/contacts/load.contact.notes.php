<?php

/**
 * @description
 * This is the notes for the contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if ((empty($_SESSION['dmsDonorSearchCriteria']['srch_database'])&&empty($_GET['s']))
        ||!isset($_GET['d'])||empty($_GET['d'])) $GLOBALS['functions']->goToIndexPage();

#   SET VITAL VARIABLES
$database = (!empty($_GET['s'])) ? $_GET['s'] : $_SESSION['dmsDonorSearchCriteria']['srch_database'];
$dnrNo = $_GET['d'];

$notesRows = '';

#   LOAD CONTRIBUTION ROWS
switch ($database) {
    case "archive":
        $dnr = new donor();
        if (!$dnr->Load($dnrNo)) $GLOBALS['functions']->goToIndexPage();
        $isDeleted = ($dnr->dnr_deleted=='Y');
        
        $notesRows = '<thead><tr style="font-weight:bold;background-color: #7B7922;">
                    <td>Date</td>
                    <td>Note</td>
                </tr></thead>';
        $notesRows .= '<tbody>';
        $trxns = $GLOBALS['functions']->GetRemarks($dnrNo);
        if ($trxns) {
            foreach ($trxns as $k=>$v) {
                $notesRows .= '<tr>';
                $notesRows .= '<td>' . date("d-m-Y",strtotime($v['drm_entry_date'])) . '</td>';
                $notesRows .= '<td>' . $v['drm_text'] . '</td>';
                $notesRows .= '</tr>'; 
            }
        } else {
            $notesRows .= '<tr><td colspan="2">There are no Remarks.</td></tr>';
        }
        $notesRows .= '</tbody>';
        $contactId = $dnr->civ_contact_id;
        break;
    case "civicrm":
        $dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
        if (!$dnr) $GLOBALS['functions']->goToIndexPage();
        $contactId = $dnr['contact_id'];
        $isDeleted = ($dnr['contact_is_deleted']==1);

        $contParams['version'] = 3;
        $contParams['entity_id'] = $contactId;
        $contParams['entity_table'] = 'civicrm_contact';
        $contParams['options']['sort'] = ' modified_date DESC';
        $apiRecord = civicrm_api('Note', 'Get', $contParams);
        $notesRows = '<thead><tr style="font-weight:bold;background-color: #7B7922;">
                    <td>Date</td>
                    <td>Subject</td>
                    <td>Note</td>
                    <td>Inserted By</td>
                    <td>&nbsp;</td>
                </tr></thead>';
        $notesRows .= '<tbody>';
        if ($apiRecord['count']>0) {
            foreach ($apiRecord['values'] as $k=>$v) {
                
                $user = $GLOBALS['functions']->getFullNameFromCiviContactId($v['contact_id']);
                
                $notesRows .= '<tr>';
                $notesRows .= '<td>' . date("d-m-Y",strtotime($v['modified_date'])) . '</td>';
                $notesRows .= '<td>' . $v['subject'] . '</td>';
                $notesRows .= '<td>' . $v['note'] . '</td>';
                $notesRows .= '<td>' . $user . '</td>';
                $notesRows .= '<td valign="middle"><img src="/dms/img/delete.png" width="12" height="12" class="imgDelete" nid="'.$v['id'].'" /></td>';
                $notesRows .= '</tr>'; 
            }
        } else {
            $notesRows .= '<tr><td colspan="5">There are no Notes.</td></tr>';
        }
        $notesRows .= '</tbody>';
        break;
}

#   ADD THE HTML
require('html/'.$curScript.'.htm');