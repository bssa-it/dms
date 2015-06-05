<?php

/**
 * @description
 * This script inserts an appointment/meeting into zimbra.
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

include("inc/globals.php");
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
/*echo "<pre />";
print_r($_POST);*/
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

$calendarType = $_POST['calendarType'];
$calendarId = (isset($_POST['calendarId'])) ? 'l="'.$_POST['calendarId'].'"':'';
$location = (isset($_POST['location'])) ? $_POST['location']:'';
$subject = (isset($_POST['subject'])) ? $_POST['subject']:'';
$startTime = date("Ymd",strtotime($_POST['appointmentDateTime'])) . 'T' . date("His",strtotime($_POST['appointmentDateTime']));
$endTime = date("Ymd",strtotime($_POST['appointmentDateTime'])) . 'T' . date("His",strtotime($_POST['appointmentDateTime'] . ' + 30 minutes'));
$meetingDetails = (isset($_POST['details'])) ? $_POST['details']:'';
$department = (isset($_POST['department'])) ? $_POST['department']:''; 
$organizer = '';

if ($calendarType=='S'&&!empty($department)) {
    $d = new department;
    $d->Load($department);
    $organizer = '<or a="'.$d->dep_fromEmailAddress.'" sentBy="'.$email.'" />';
} 

$soapVar = '
<m '.$calendarId.'>
    <inv method="REQUEST" fb="B" transp="O" status="CONF" allDay="0">
        <comp loc="'.$location.'" name="'.$subject.'">
            <s d="'.$startTime.'"/>
            <e d="'.$endTime.'"/>
            <alarm action="DISPLAY"> 
                <trigger> 
                    <rel neg="1" m="15" /> 
                </trigger>
            </alarm>
            '.$organizer.'
        </comp>
    </inv>
    <mp ct="text/html"><content>'.$meetingDetails.'</content></mp>
</m>';

/*echo htmlentities($soapVar);
exit();*/

$options = array('uri' => "urn:zimbraMail");
$params = array( 
new SoapVar($soapVar, XSD_ANYXML)
);

try {
    $soapHeader = new SoapHeader(
        'urn:zimbra',
        'context',
        new SoapVar('<ns2:context><authToken>' . $authToken . '</authToken></ns2:context>', XSD_ANYXML)
        );

    $result = $client->__soapCall(
        "CreateAppointmentRequest",
        $params,
        $options,
        $soapHeader
        );
    try {
        if (strpos($subject,"dms-")>0) {
            $activityId = substr($subject,strpos($subject,"dms-")+4);
            $apiParams['version'] = 3;
            $apiParams['id'] = $activityId;    
            $apiParams['custom_21'] = date("Y-m-d H:i:s");
            
            $xml = new SimpleXMLElement($client->__getLastResponse());  
            $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');  
            $body = $xml->xpath('//soap:Body'); 
            $appResponse = $body[0]->CreateAppointmentResponse;
            
            echo "<pre />";
            print_r($appResponse);
            
            $itemId = (string)$appResponse['calItemId'];
            $apiParams['custom_23'] = $itemId;
            
            if (!empty($apiParams['custom_23'])) {
                $apiParams['custom_24'] = $email;
                $saveCiviUpdate = civicrm_api('Activity','Create',$apiParams);
                echo "<pre />";
                print_r($saveCiviUpdate);
                
                echo "<pre />";
                print_r($apiParams);
            }
        }   
    } catch (exception $e) {
        echo 'Error: '. $e->getMessage();
    }
} catch (SoapFault $exception) {
    echo "<pre />";
    print_r($exception);
}

?>