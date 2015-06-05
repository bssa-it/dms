<?php 

/**
 * @description
 * This file produces a pdf/HTML version of the unevaluated letters.
 * 
 * @author      Chezre Fredericks
 * @date_created 29/11/2014
 * @Changes
 */

#   BOOTSTRAP
include("inc/globals.php");
$curScript = basename(__FILE__, '.php');
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
$title = "Preview";

#   GET UNEVALUATED LETTERS FOR PREVIEW ALL
if (!empty($_POST['previewAll'])&&$_POST['previewAll']=='Y') {
    $u = $_SESSION['dms_user']['userid'];
    $fileList = glob("unevaluated/$u.*.xml");
    if (!empty($fileList)) {
        $letters = '';
        foreach ($fileList as $f) {
            $fileXML = simplexml_load_file($f);
            $method = (string)$fileXML->letter->method;
            if ($method!='Postal Mail') continue;
            $html = file_get_contents((string)$fileXML->letter->filename);
            $margins = array();
            $margins[] = (string)$fileXML->letter->tpl_marginLeft;
            $margins[] = (string)$fileXML->letter->tpl_marginTop;
            $margins[] = (string)$fileXML->letter->tpl_marginRight;
            $margins[] = (string)$fileXML->letter->tpl_marginBottom;   
            $ackDocument = preg_replace(array('/unevaluated\//','/.html/'),array('acklists/','.pdf'),(string)$fileXML->letter->filename);
            $allLetterFilenames[] = $ackDocument;
            createPdf($html,$ackDocument,$margins);  
        }
        if (!empty($allLetterFilenames)) {
            include('inc/PDFMerger.php');
            $consolidatedPDF = new PDFMerger;
            foreach ($allLetterFilenames as $cf) $consolidatedPDF->addPDF($cf);
            $consolidatedFilename = $u.'-preview-all.pdf';
            $consolidatedPDF->merge('browser',$consolidatedFilename);
        }
    } else {
        # IF THERE ARE NO UNEVALUATED FILES GO BACK TO THE DASHBOARD
        header('location: index.php');
    }
    exit();
}


#   LOAD LETTER FOR SINGLE LETTER PREVIEW
$template = $_POST['letterEditor'];
$displayMethod = (!empty($_POST['method'])) ? $_POST['method'] : 'Postal Mail'; 

if ($displayMethod=='Postal Mail') {
    #   IF METHOD IS PRINT LOAD THE CONFIG FILE IN CASE THE DEFAULT MARGINS ARE REQUIRED
    $xmlConfig = simplexml_load_file("inc/config.xml");
    $letters = '<page>'."\n";
    $letters .= $template;
    $letters .= '</page>';
    $l = ($_POST['tpl_marginLeft']=='T'||!is_numeric($_POST['tpl_marginLeft'])) ? (string)$xmlConfig->acknowledgementconfig->marginLeft:$_POST['tpl_marginLeft'];
    $t = ($_POST['tpl_marginTop']=='T'||!is_numeric($_POST['tpl_marginTop'])) ? (string)$xmlConfig->acknowledgementconfig->marginTop:$_POST['tpl_marginTop'];
    $r = ($_POST['tpl_marginRight']=='T'||!is_numeric($_POST['tpl_marginRight'])) ? (string)$xmlConfig->acknowledgementconfig->marginRight:$_POST['tpl_marginRight'];
    $b = ($_POST['tpl_marginBottom']=='T'||!is_numeric($_POST['tpl_marginBottom'])) ? (string)$xmlConfig->acknowledgementconfig->marginBottom:$_POST['tpl_marginBottom'];
    $margins = array($l,$t,$r,$b);
    $previewFilename = (!empty($_POST['filename'])) ? $_POST['filename'] : 'preview';
    
    #   TRY AND LOAD THE PDF VERSION OF THE LETTER
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'en',false,'ISO-8859-15',$margins);
        $html2pdf->setDefaultFont('times');
        $html2pdf->writeHTML($letters);
        $html2pdf->Output($previewFilename.".pdf");
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }   
}

if ($displayMethod=='Email') {
    #   IF THE METHOD IS EMAIL THEN JUST LOAD THE HTML FILE SAVED FOR THE UNEVALUATED LETTER
    $pageHeading = "Email Preview";
    $fromName = $_SESSION['dms_user']['fullname'] . '&nbsp;&lt;'.$_SESSION['dms_user']['username'].'@biblesociety.co.za&gt;';
    if ($_POST['impersonate']=='Y') {
        $d = new department();
        $d->Load($_POST['department']);
        $fromName = $d->dep_fromEmailName . '&nbsp;&lt;'.$d->dep_fromEmailAddress.'&gt;';
    }
    $e = new dmsEmail();
    $template = $e->addLetterHeadToBody($_POST['region'], $template);
    require('html/'.$curScript.'.htm');
    exit();
}

#   CREATE PDF FUNCTION FOR PREVIEW ALL
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