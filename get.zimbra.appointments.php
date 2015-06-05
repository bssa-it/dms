<?php

/**
 * @description
 * This script retrieves the calendars from zimbra for the user.
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

include("inc/globals.php");

$PREAUTH_KEY=(string)$GLOBALS['xmlConfig']->emailconfig->zimbraPreAuthKey;
$email = $_SESSION['dms_user']['username']."@biblesociety.co.za";
$timestamp=time()*1000;
$preauthToken=hash_hmac("sha1",$email."|name|0|".$timestamp,$PREAUTH_KEY);

$client = new SoapClient(null,  
 array(  
   'location' => "https://mymail.biblesociety.co.za/service/soap",  
   'uri' => "urn:zimbraAccount",  
   'trace' => 1,  
   'exceptions' => 1,  
   'soap_version' => SOAP_1_1,  
   'style' => SOAP_RPC,  
   'use' => SOAP_LITERAL  
 )  
);    
  
$var = new SoapVar('<account by="name">'.$email.'</account><preauth timestamp="'.$timestamp.'" expires="0">'.$preauthToken.'</preauth>', XSD_ANYXML);
$params = array($var,);                 
try {  
    $soapHeader = new SoapHeader(  
       'urn:zimbraAccount',  
       'context'  
    );  
    $result = $client->__soapCall(  
       "AuthRequest",   
       $params,   
       null,  
       $soapHeader  
    );
    $authToken=$result['authToken'];  
} catch (SoapFault $exception) {  echo "auth fault";  }
if (empty($authToken)) exit();

/*
    $soapHeader = new SoapHeader(  
                    'urn:zimbraAccount',  
                    'context',  
                         new SoapVar(  
                              '<ns1:context><format type="xml" /></ns1:context>',  
                              XSD_ANYXML  
                         )                                     
                    );  
     $result = $client->__soapCall(  
                    "GetInfoRequest",   
                    array(''),   
                    array('uri' => 'urn:zimbraAccount'),  
                    $soapHeader  
     );  
     $xml = new SimpleXMLElement($client->__getLastResponse());  
     $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');  
     $body = $xml->xpath('//soap:Body');  
     
     $xml->registerXPathNamespace('zimbra', 'urn:zimbraAccount');  
     $response = $xml->xpath('//soap:Body/zimbra:GetInfoResponse');
     echo "<pre />";
     print_r($response);

exit();*/


$appts = array();
try {  
     $soapHeader = new SoapHeader(  
                    'urn:zimbraMail',  
                    'context',  
                         new SoapVar(  
                              '<ns1:context><format type="xml" /></ns1:context>',  
                              XSD_ANYXML  
                         )                                     
                    );  
     $result = $client->__soapCall(  
                    "GetFolderRequest",   
                    array(''),   
                    array('uri' => 'urn:zimbraMail'),  
                    $soapHeader  
     );  
     $xml = new SimpleXMLElement($client->__getLastResponse());  
     $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');  
     $body = $xml->xpath('//soap:Body');  
     
     $xml->registerXPathNamespace('zimbra', 'urn:zimbraMail');  
     $GetFolderResponse = $xml->xpath('//soap:Body/zimbra:GetFolderResponse');  
     
     $folder = $GetFolderResponse[0]->folder;
     
     $folder->registerXPathNamespace('zimbra', 'urn:zimbraMail');  
     $calendars = $folder->xpath('zimbra:folder[@view="appointment"]');  
     $calendars = xml2array ( $calendars, $out = array () );
     
     foreach ($calendars as $k=>$v) {
        getAppointments($v['@attributes']['name']);
     }
     
     $depts = array();
     foreach ($_SESSION['dms_user']['config']['departments'] as $dep=>$depName) {
        $d = new department;
        $d->Load($dep);
        if (!in_array($d->dep_fromEmailAddress,$depts)) $depts[$dep] = $d->dep_fromEmailAddress;
     }
     $links = $folder->xpath('zimbra:link[@view="appointment"]');  
     $links = xml2array ( $links, $out = array () );  
     foreach ($links as $k=>$v) {
        foreach ($depts as $dep=>$depEmail) {
            if ($depEmail==$v['@attributes']['owner']) { 
                getAppointments($v['@attributes']['name']);
                break;
            }
        }
     }
} catch (SoapFault $exception) {  
     echo "exception caught while trying to fetch Folder info<br><br>";  
     echo htmlentities($client->__getLastResponse()) . "<br><br>";    
}

function xml2array ( $xmlObject, $out = array () )  
{  
   foreach ( (array) $xmlObject as $index => $node )  
        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;  
   return $out;  
}

function getAppointments($name) {
    
    $PREAUTH_KEY=(string)$GLOBALS['xmlConfig']->emailconfig->zimbraPreAuthKey;
    $email = $_SESSION['dms_user']['username']."@biblesociety.co.za";
    $timestamp=time()*1000;
    $preauthToken=hash_hmac("sha1",$email."|name|0|".$timestamp,$PREAUTH_KEY);
    $zm_url = 'https://mymail.biblesociety.co.za/';
    $zm_url .= 'service/preauth?account='.$email;
    $zm_url .= '&by=name&timestamp='.$timestamp."&expires=0&preauth=".$preauthToken;
    $zm_url .= '&redirectURL=home/~/'.$name.'.json';
    $zimbra_user = $_SESSION['dms_user']['username'];

    $ch = curl_init();
    $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
    $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 300";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Pragma: "; 
    $proxy = '129.47.16.13:3128';
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_URL, $zm_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies");  
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie'.$zimbra_user.'.txt');
    $response = curl_exec($ch);
    echo $response;
    curl_close($ch);

}