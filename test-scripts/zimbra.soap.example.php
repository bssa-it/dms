<?php

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
   $params = array(  
                $var,  
                );                 
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
                            "CreateAppointment",   
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
             $links = $folder->xpath('zimbra:link[@view="appointment"]'); 
             
             echo "<pre> \n";
             print_r($links);
             echo "</pre>";
        } catch (SoapFault $exception) {    }  
   } catch (SoapFault $exception) {  echo "auth fault";  } 
 ?>  