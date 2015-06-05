<?php 

/**
 * @description
 * This file produces a pdf version of the template for a preview.
 * 
 * @author      Chezre Fredericks
 * @date_created 29/11/2013
 * @Changes
 */

#   BOOTSTRAP
include("inc/globals.php");
$xmlConfig = simplexml_load_file("inc/config.xml");
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   LOAD TEMPLATE
$template = $_POST['letterEditor'];
$letters = '<page>'."\n";
$letters .= $template;
$letters .= '</page>';
#   MARGINS
$l = ($_POST['tpl_marginLeft']=='T'||!is_numeric($_POST['tpl_marginLeft'])) ? (string)$xmlConfig->acknowledgementconfig->marginLeft:$_POST['tpl_marginLeft'];
$t = ($_POST['tpl_marginTop']=='T'||!is_numeric($_POST['tpl_marginTop'])) ? (string)$xmlConfig->acknowledgementconfig->marginTop:$_POST['tpl_marginTop'];
$r = ($_POST['tpl_marginRight']=='T'||!is_numeric($_POST['tpl_marginRight'])) ? (string)$xmlConfig->acknowledgementconfig->marginRight:$_POST['tpl_marginRight'];
$b = ($_POST['tpl_marginBottom']=='T'||!is_numeric($_POST['tpl_marginBottom'])) ? (string)$xmlConfig->acknowledgementconfig->marginBottom:$_POST['tpl_marginBottom'];
$margins = array($l,$t,$r,$b);

#   TRY TO CREATE THE PDF
try
{
    $html2pdf = new HTML2PDF('P', 'A4', 'en',false,'ISO-8859-15',$margins);
    $html2pdf->setDefaultFont('times');
    $html2pdf->writeHTML($letters);
    $html2pdf->Output($_POST['tpl_name'].".pdf");
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}