<?php

include("inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (empty($_POST['message'])||empty($_POST['cellphone_nos'])) $GLOBALS['functions']->goToIndexPage();

$xmlSettings = simplexml_load_file("inc/grapevine/grapevine.config.xml");
$xmlMessageTemplate = file_get_contents("inc/grapevine/message.template.xml");
$msisdnTemplate = file_get_contents("inc/grapevine/msisdn.template.xml");

$message = $_POST['message'];
$msisdns = $_POST['cellphone_nos'];
$msisdn = '';
foreach ($msisdns as $k=>$v) {
    $r = '/###msisdn###/';
    $msisdn .= preg_replace($r, $v, $msisdnTemplate);
}

$tokens = array('/###affiliateCode###/','/###authenticationCode###/','/###now###/','/###message###/','/###msisdns###/');
$replacements = array($xmlSettings->Affiliate_Code,$xmlSettings->Authentication_Code,date("Y-m-d").'T'.date("H:i:s"),$message,$msisdn);
$xml_post = preg_replace($tokens, $replacements, $xmlMessageTemplate);
 
$URL = (string)$xmlSettings->URL;

$proxy = '129.47.16.13:3128';
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_URL, $URL); 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

# SAVE SMS REQUEST
$user = $_SESSION['dms_user']['userid'];
$timestamp = date("YmdHis") . str_pad(rand(0,999),3,'0');
if (!is_dir('logs/grapevine/'.$user)) mkdir('logs/grapevine/'.$user);
$filename = 'logs/grapevine/'.$user .'/'. $timestamp .'.xml';
$xml = simplexml_load_string($xml_post);
file_put_contents($filename, $xml->asXML());

# SAVE SMS REQUEST RESULT
$xml = simplexml_load_string($output);
$filename = 'logs/grapevine/'.$user .'/result.'. $timestamp .'.xml';
file_put_contents($filename, $xml->asXML());

# RETURN REQUEST RESULT
$json = json_encode($xml);
echo $json;