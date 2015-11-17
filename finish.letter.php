<?php

/**
 * @description
 * This script creates a pdf or sends an email and then records an official acknowledgement.
 * 
 * @author      Chezre Fredericks
 * @date_created 27/03/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$xmlConfig = simplexml_load_file("inc/config.xml");

#   START COUNTERS
$resultRows = '';
$pdfCnt = 0;
$emailCnt = 0;
$totalCnt = 0;
$consolidatedFilename = '';

#   LOAD UNEVALUATED LETTER XML CONFIG FILES
$u = $_SESSION['dms_user']['userid'];
if ($_POST['updateAll']=='N') {
    if (file_exists($_POST['xmlFilename'])) $fileList[] = $_POST['xmlFilename'];
} elseif (!empty($_GET['d'])) {
    $fileList = glob("unevaluated/$u.*.xml");
} elseif (empty($_POST['updateAll'])&&empty($_GET['d'])) {
    header("location:index.php");
    exit();
}
  
#   PRINT/EMAIL ALL UNEVALUATED LETTERS
if (!empty($fileList)) {
    if (file_exists("acklists/$u.result.htm")) unlink("acklists/$u.result.htm");
    $allLetterFilenames = array();
    foreach ($fileList as $f) {
        $fileXML = simplexml_load_file($f);
        $html = file_get_contents((string)$fileXML->letter->filename);
        $method = (string)$fileXML->letter->method;
        $emailToName = (string)$fileXML->letter->emailname;
        $emailAddress = (string)$fileXML->letter->email;
        
        switch ($method) {
            case 'Postal Mail': 
                $margins = array();
                $margins[] = (string)$fileXML->letter->tpl_marginLeft;
                $margins[] = (string)$fileXML->letter->tpl_marginTop;
                $margins[] = (string)$fileXML->letter->tpl_marginRight;
                $margins[] = (string)$fileXML->letter->tpl_marginBottom;
                $ackDocument = preg_replace(array('/unevaluated\//','/.html/'),array('acklists/','.pdf'),(string)$fileXML->letter->filename);
                createPdf($html,$ackDocument,$margins);
                $allLetterFilenames[] = $ackDocument;
                $methodValue = $method;
                $pdfCnt++;
                break;
            case 'Email':
                $email = new dmsEmail($f);
                if (!empty($email->message)) {
                    $mandrill = new dmsMandrill();    
                    $sendResult = $mandrill->messages->send($email->message);
                    $ackDocument = preg_replace(array('/unevaluated\//'),array('acklists/'),(string)$fileXML->letter->filename);
                    file_put_contents($ackDocument,$email->message['html']);
                    $methodValue = $sendResult[0]['status'];
                } else {
                    $methodValue = 'problem with ' . $f;
                }
                $emailCnt++;
                break;
        }
        
        #   UPDATE THE TRANSACTION RECORD
        $t = new transaction();
        $filenamePieces = explode('.',(string)$fileXML->letter->filename);
        $t->Load($filenamePieces[1]);
        
        $a = new acknowledgement();
        $a->ack_civi_con_id = $t->civ_contribution_id;
        $a->ack_date = date('Y-m-d H:i:s');
        $a->ack_document = preg_replace('/acklists\//','',$ackDocument);
        $a->ack_method = $methodValue;
        $a->ack_trns_id = $t->trns_id;
        $a->ack_usr_id = $u;
        $a->Save();
        
        # UPDATE LAST ACKNOWLEDGEMENT DATE
        $ap = new acknowledgementpreferences();
        $ap->LoadByContactId($t->civ_contact_id);
        $ap->apr_last_acknowledgement_date = $a->ack_date;
        $ap->apr_last_acknowledgement_trns_id = $t->trns_id;
        $ap->apr_unacknowledged_total = 0;
        $ap->Save();
        
        $dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($t->trns_dnr_no);
        
        $resultRows .= '<tr>';
        $resultRows .= '<td>'.$t->trns_receipt_no.'</td>';
        $resultRows .= '<td align="right" style="padding-right: 10px;">R '.number_format((float)$t->trns_amount_received,2,'.',',').'</td>';
        $resultRows .= '<td>'.$t->trns_dnr_no.'</td>';
        $resultRows .= (!empty($emailAddress)) ? '<td>'.$emailToName.'</td>':'<td>'.$dnr['display_name'].'</td>';
        $resultRows .= (!empty($emailAddress)) ? '<td>'.$emailAddress.'</td>':'<td>&nbsp;</td>';
        $resultRows .= '<td>'.date('Y-m-d H:i:s').'</td>';
        $resultRows .= (!empty($mail->ErrorInfo)) ? '<td>'.$mail->ErrorInfo.'</td>':'<td>'.$methodValue.'</td>';
        $resultRows .= '<td><a href="'.$ackDocument.'">'.$ackDocument.'</a></td>';
        $resultRows .= '</tr>';
        
        $totalCnt++;
        
        if (file_exists($f)) unlink($f);
        if (file_exists((string)$fileXML->letter->filename)) unlink((string)$fileXML->letter->filename);
        
    }
    
    #   MERGE THE PDFS CREATED
    if (!empty($allLetterFilenames)) {
        include('inc/PDFMerger.php');
        $consolidatedPDF = new PDFMerger;
        foreach ($allLetterFilenames as $cf) $consolidatedPDF->addPDF($cf);
        $dateCompleted = date('Y-m-d H:i:s');
        $consolidatedFilename = 'acklists/'.$u.'.con.'.date('YmdHis',strtotime($dateCompleted)).'.pdf';
        $consolidatedPDF->merge('file',$consolidatedFilename);
        
        $a = new acknowledgement();
        $a->ack_date = $dateCompleted;
        $a->ack_document = preg_replace('/acklists\//','',$consolidatedFilename);
        $a->ack_method = 'consolidated';
        $a->ack_usr_id = $u;
        $a->Save();
    }
    
} else {
    $resultRows = '<tr><td colspan="8">There were no unevaluated letters to print.</td></tr>';
}

#   SAVE THE RESULT TO SEE AT A LATER TIME
ob_start();
$menu = new menu;
$pageHeading = $title = "Merge Results";
require('html/'.$curScript.'.htm');
file_put_contents('acklists/'.$u.'.result.htm', ob_get_contents());
exit();

#   FUNCTION TO CREATE PDF SPECIFIC TO THIS PAGE
function createPdf($content,$filename,$margins) {
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'en',false,'ISO-8859-15',$margins);
        $html2pdf->setDefaultFont('times');
        $html2pdf->writeHTML($content);
        $html2pdf->Output($filename,'F');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
}