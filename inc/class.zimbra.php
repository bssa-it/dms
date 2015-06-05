<?php

class zimbraTask {
    var $id;
    var $date;
    var $eDate;
    var $status;
    
    function updateTask($authToken,$client) {
        
        $soapVar = '
        <ModifyTaskRequest id="'.$this->id.'">
            <m>
                <inv status="'.$this->status.'">
                    <comp>
                        <s d="'.$this->date.'"/>
                        <e d="'.$this->eDate.'"/>
                    </comp>
                </inv>
            </m>
        </ModifyTaskRequest>';
        
        echo htmlentities($soapVar);
        
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
                "ModifyTaskRequest",
                $params,
                $options,
                $soapHeader
                );
             echo "<pre />";
             print_r($result);
        } catch (SoapFault $exception) {
            echo "<pre />";
            print_r($exception);
        }
    }
}

class zimbraAppointment {
    var $id;
    var $date;
    var $eDate;
    var $status;
    
    function updateAppointment($authToken,$client) {
        
        $soapVar = '<ModifyAppointmentRequest id="'.$this->id.'"><m><inv status="'.$this->status.'">
            <comp>
                <s d="'.$this->date.'"/>
                <e d="'.$this->eDate.'"/>
            </comp>
        </inv></m></ModifyAppointmentRequest>';
        
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
                "ModifyAppointmentRequest",
                $params,
                $options,
                $soapHeader
                );
             echo "<pre />";
             print_r($result);
        } catch (SoapFault $exception) {
            echo "<pre />";
            print_r($exception);
        }
    }
}

class zimbraAccount {
    
    var $email;
    var $authToken;
    var $preauthkey;
    var $client;
    
    function getZimbraAuthToken() {
        $timestamp=time()*1000;
        $preauthToken=hash_hmac("sha1",$this->email."|name|0|".$timestamp,$this->preauthkey);
        
        $this->client = new SoapClient(null,  
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
          
        $var = new SoapVar('<account by="name">'.$this->email.'</account><preauth timestamp="'.$timestamp.'" expires="0">'.$preauthToken.'</preauth>', XSD_ANYXML);
        $params = array($var,);                 
        try {  
            $soapHeader = new SoapHeader(  
               'urn:zimbraAccount',  
               'context'  
            );  
            $result = $this->client->__soapCall(  
               "AuthRequest",   
               $params,   
               null,  
               $soapHeader  
            );
            $this->authToken=$result['authToken'];  
        } catch (SoapFault $exception) {  
            echo "<pre />";
            print_r($exception);
        }
    }
    
    function getItem($zimbraItemId) {
        
        $soapHeader = new SoapHeader(
            'urn:zimbraMail',  
            'context',  
            new SoapVar(  
                '<ns1:context><format type="xml" /></ns1:context>',  
                XSD_ANYXML  
            )                                     
        );
        $soapVar = '<item id="'.$zimbraItemId.'" />';
        
        /*echo htmlentities($soapVar);
        exit();*/
        
        try {
            $params = array( 
                new SoapVar($soapVar, XSD_ANYXML)
            );  
            $result = $this->client->__soapCall(  
                "GetItemRequest",   
                $params,   
                array('uri' => 'urn:zimbraMail'),  
                $soapHeader  
            );  
            $xml = new SimpleXMLElement($this->client->__getLastResponse());  
            $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');  
            $body = $xml->xpath('//soap:Body'); 
            return $body;
            
        } catch (exception $ex) {
            echo "<pre />";
            print_r($ex);
            return '';
        }
    }
}

?>