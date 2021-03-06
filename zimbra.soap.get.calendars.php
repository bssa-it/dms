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
$xmlConfig = simplexml_load_file("inc/config.xml");

$PREAUTH_KEY=(string)$xmlConfig->emailconfig->zimbraPreAuthKey;
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

/*echo "<pre />";
print_r($_SESSION['dms_user']);*/

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
     
     echo '<script type="text/javascript" language="javascript"> 
            $(".aCalendar").click(function(event) {
                event.preventDefault();
                window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes,location=no");
            });
            </script>';
     echo '<ul id="ulCalendar">';
     $checked = ' CHECKED';
     foreach ($calendars as $k=>$v) {
        echo '<li class="liCalendar"><input type="radio" value="P-'.$v['@attributes']['id'].'" name="zimbraCalendarId" '.$checked.' /><a class="aCalendar" href="zimbra.get.calendar.php?c=' . $v['@attributes']['name'] . '"> '.$v['@attributes']['name'].'</a></li>';
        $checked = '';
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
                echo '<li class="liCalendar"><input type="radio" value="S-'.$v['@attributes']['id'].'-'.$dep.'" name="zimbraCalendarId" /><a class="aCalendar" href="zimbra.get.calendar.php?c=' . $v['@attributes']['name'] . '"> '.$v['@attributes']['name'].'</a></li>';
                break;
            }   
        }
     }
     echo "</ul>";       
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