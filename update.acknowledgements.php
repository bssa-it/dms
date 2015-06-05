<?php

/**
 * @description
 * This exports the acknowledged contributions to a mail merge file.
 * 
 * @author      Chezre Fredericks
 * @date_created 21/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$filename = preg_replace('/ /','_',$_POST['filename']);
if (file_exists('acklists/'.$filename)) $filename = preg_replace('/.txt/',date("-YmdHis").'.txt',$filename);
$cnt = 0;

#   CREATE MAIL MERGE DATA FOR EACH CONTRIBUTION
foreach ($_POST['trxns'] as $k=>$v) {
    $t = new transaction();
    $t->Load($v); 
    
    $mr = new mergerecord;
    $mr->loadByTrnsId($v);
    
    $e = $mr->getMergeRecordHeadings();
    foreach ($mr->xmlFields->field as $m) {
        $f = (string)$m['id'];
        $e[(string)$m['label']] = $mr->$m['id'];
    }
    $exportArray[] = $e; 
    
    # INSERT AN ACKNOWLEDGEMENT RECORD INTO THE DMS dms_acknowledgement TABLE
    $a = new acknowledgement();
    $a->ack_date = date("Y-m-d H:i:s");
    $a->ack_civi_con_id = $t->civ_contribution_id;
    $a->ack_document = $filename;
    $a->ack_method = 'export';
    $a->ack_trns_id = $t->trns_id;
    $a->ack_usr_id = $_SESSION['dms_user']['userid'];
    $a->Save();
    
    # UPDATE LAST ACKNOWLEDGEMENT DATE
    $ap = new acknowledgementpreferences();
    $ap->LoadByContactId($t->civ_contact_id);
    $ap->apr_last_acknowledgement_date = $a->ack_date;
    $ap->apr_last_acknowledgement_trns_id = $t->trns_id;
    $ap->apr_unacknowledged_total = 0;
    $ap->Save();
    
    # UPDATE THE CONTRIBUTION RECORD WITH A THANKYOU DATE IN THE CIVICRM DATABASE.
    $updateParams['version'] = 3;
    $updateParams['id'] = $t->civ_contribution_id;
    $updateParams['thankyou_date'] = $a->ack_date;
    $result = civicrm_api('Contribution', 'create', $updateParams);
    
    $cnt++;
}

#   UPDATE THE SESSION WITH THE LATEST MAIL MERGE FILE CREATED
$_SESSION['dms_acknowledgements']['lastFilename'] = $filename;
$_SESSION['dms_acknowledgements']['noOfRecords'] = $cnt;

#   CREATE THE CSV FILE ON THE SERVER (CAN BE DOWNLOADED AT A LATER STAGE)
$GLOBALS['functions']->exportArrayToCSV('acklists/'.$filename,$exportArray);

#   GO TO THE EXPORTED ACKNOWLEDGEMENTS PAGE
header('location:export.acknowledgements.php');