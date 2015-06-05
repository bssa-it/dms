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

$options = array('uri' => "urn:zimbraMail");
$params = array( 
new SoapVar('
<m l="4942">
    <inv method="REQUEST" fb="B" transp="O" status="CONF" allDay="0">
        <comp loc="chezre office" name="Test-appt-4">
            <s d="20140901T090000"/>
            <e d="20140901T100000"/>
            <alarm action="DISPLAY"> 
                <trigger> 
                    <rel neg="1" h="1" /> 
                </trigger>
            </alarm>
            <or a="fredericks@biblesociety.co.za" sentBy="test@biblesociety.co.za" />
        </comp>
    </inv>
    <mp ct="text/html"><content>This is content of the meeting - html</content></mp>
</m>', XSD_ANYXML)
);

try {
echo "creating header<br>";
$soapHeader = new SoapHeader(
'urn:zimbra',
'context',
new SoapVar('<ns2:context><authToken>' . $authToken . '</authToken></ns2:context>', XSD_ANYXML)
);

echo "trying<br>";
$result = $client->__soapCall(
"CreateAppointmentRequest",
$params,
$options,
$soapHeader
);
echo "executed<br>";
} catch (SoapFault $exception) {
    echo "<pre> \n";
    print_r($exception);
    echo "</pre>";
}
echo "shot!<br />" . date("Y-m-d H:i:s");

?>