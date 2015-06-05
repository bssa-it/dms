<?php

/**
 * @description
 * This script saves the bam certificate to the server.
 * 
 * @author      Chezre Fredericks
 * @date_created 16/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
if (!$_SESSION['dms_user']['authorisation']->isAdmin) {
    header('location: index.php');
    exit();
}

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$content = file_get_contents('html/save.bamCertificate.htm');
$patterns = array('/###lang###/','/###name###/','/###ref###/');
$replacements = array($_POST['lang'],$_POST['name'],$_POST['ref']);
$content = preg_replace($patterns, $replacements, $content);
$margins = array(10,10,10,10);
$filename = 'bam/'.$_POST['mid'].'-bam-certificate.pdf';

$m['version'] = 3;
$m['id'] = $_POST['mid'];
$m['custom_16'] = 1;
$result = civicrm_api('Membership','Create',$m);
echo ($result['is_error']===1) ? '0':$result['id'];

try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'en',false,'ISO-8859-15',$margins);
    $html2pdf->setDefaultFont('arial');
    $html2pdf->writeHTML($content);
    $html2pdf->Output($filename,'F');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}