<?php

/**
 * @description
 * This page loads the donor relationships edit form.
 * 
 * @author      Chezre Fredericks
 * @date_created 15/07/2014
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
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
if (!$dnr) $GLOBALS['functions']->goToIndexPage();
$contactId = $dnr['contact_id'];

$relationships = $GLOBALS['functions']->getCiviContactRelationships($contactId);
$relationshipTypes = $GLOBALS['functions']->getAllCiviRelationshipTypes();
$relationshipTypeOpts = '';
foreach ($relationshipTypes as $type) { 
    $relationshipTypeOpts .= '<option value="left-right-'.$type['id'].'">'.$type['label_a_b'].'</option>';
    if ($type['label_a_b']!=$type['label_b_a']) $relationshipTypeOpts .= '<option value="right-left-'.$type['id'].'">'.$type['label_b_a'].'</option>';
}

$contactRelationshipRows = '';
$hiddenInputs = '';
$jqueryBindings = '';
if (!empty($relationships)) {
    foreach ($relationships as $r) {
         $relType = ($contactId==$r['contact_id_a']) ? 'label_a_b':'label_b_a';
         $relationship = 'Unknown';
         foreach ($relationshipTypes as $type) if ($type['id']==$r['relationship_type_id']) $relationship = $type[$relType];
         
         $otherContactId = ($contactId==$r['contact_id_a']) ? $r['contact_id_b']:$r['contact_id_a'];
         $otherContact = $GLOBALS['functions']->getCiviContact($otherContactId);
         
         $hiddenInputs .= "\n" . '<input type="hidden" id="relationship_type_id-'.$r['id'].'" value="'.$r['relationship_type_id'].'" />';
         $hiddenInputs .= "\n" . '<input type="hidden" id="donor_no_right-'.$r['id'].'" value="'.$otherContact['external_identifier'].'" />';
         $hiddenInputs .= "\n" . '<input type="hidden" id="contact_id_right-'.$r['id'].'" value="'.$otherContactId.'" />';
         $jqueryBindings .= "\n" . '$("#imgDelete-'.$r['id'].'").bind("click",deleteRelationship);';
         $jqueryBindings .= "\n" . '$("#imgEdit-'.$r['id'].'").bind("click",editRelationship);';
         $contactRelationshipRows .= "\n" . '<tr>';
         $contactRelationshipRows .= '<td>' . $dnr['display_name'] . '</td>';
         $contactRelationshipRows .= '<td id="relationship_desc-'.$r['id'].'">' . $relationship . '</td>';
         $contactRelationshipRows .= '<td id="nameB-'.$r['id'].'">' . $otherContact['display_name'] . ' (' . $otherContact['external_identifier'] . ')' . '</td>';
         $contactRelationshipRows .= '<td><img src="/dms/img/delete.png" id="imgDelete-'.$r['id'].'" title="delete" width="16" height="16" /> <img src="/dms/img/edit-16x16.png" title="edit" width="16" height="16" id="imgEdit-'.$r['id'].'" />'; 
         $contactRelationshipRows .= '</tr>';
    }
}

#   SHOW THE HTML
require('html/'.$curScript.'.htm');