<?php

require_once 'Mandrill.php';

class dmsMandrill extends Mandrill {
    
    function __construct() {
        $mandrillApiKey = (string)$GLOBALS['xmlConfig']->emailconfig->mandrill->smtpPassword;
        parent::__construct($mandrillApiKey);
    }
    
}

class dmsEmail {
    
    var $message;
    function __construct($filename=null) {
        
        if (!file_exists($filename)) return;
        $fileXML = simplexml_load_file($filename);
        if ((string)$fileXML->letter->method!='Email') return;
        
        # Subject
        $this->message['subject'] = (string)$fileXML->letter->subject;
        # HTML Body
        $html = file_get_contents((string)$fileXML->letter->filename);
        if ((string)$fileXML->letter->addSignature=='Y') {
            $contact_id = (string)$fileXML->letter->contact_id;
            $region = (string)$fileXML->letter->region;
            $this->message['html'] = $this->addSignatureToBody($region,$html,$contact_id);
        } else {
            $region = (string)$fileXML->letter->region;
            $this->message['html'] = $this->addLetterHeadToBody($region, $html);
        }
        
        # To Address
        $this->addToAddress((string)$fileXML->letter->emailname,(string)$fileXML->letter->email);
        # From Address
        if ((string)$fileXML->letter->impersonate=='Y') {
            $department = (string)$fileXML->letter->department;
            $this->setDepartmentFromDetails($department);
        } else {
            if (!empty($_SESSION['dms_user']['username'])) {
                $this->setUserFromDetails();
            } else {
                $this->setDefaultFromDetails();
            }
        }
        # Reply To Address
        $this->setReplyTo();
    }
    
    function addToAddress($toName,$toAddress) {
        $address['name'] = $toName;
        $address['email'] = $toAddress;
        $address['type'] = 'to';
        $this->message['to'][] = $address;
    }
    
    function setDefaultFromDetails() {
        $this->message['from_email'] = (string)$GLOBALS['xmlConfig']->emailconfig->address;
        $this->message['from_name'] = (string)$GLOBALS['xmlConfig']->emailconfig->name;
    }
    
    function setUserFromDetails() {
        $this->message['from_email'] = $_SESSION['dms_user']['username'].(string)$GLOBALS['xmlConfig']->emailconfig->domain;
        $this->message['from_name'] = $_SESSION['dms_user']['fullname'];
    }
    
    function setDepartmentFromDetails($dep) {
        $d = new department();
        $d->Load($dep);
        
        $this->message['from_email'] = $d->dep_fromEmailAddress;
        $this->message['from_name'] = $d->dep_fromEmailName;
    }
    
    function setReplyTo() {
        if (empty($_SESSION['dms_user']['username'])) {
            $this->message['headers']['Reply-To'] = (string)$GLOBALS['xmlConfig']->emailconfig->emailconfig;
        } else {
            $this->message['headers']['Reply-To'] = $_SESSION['dms_user']['username'].(string)$GLOBALS['xmlConfig']->emailconfig->domain;
        }
    }
    
    function addSignatureToBody($region,$html,$contact_id) {
        $contact = $GLOBALS['functions']->getCiviContact($contact_id);
        $r = new region;
        $r->Load($region);
        $language = ($contact['preferred_language']=='af_ZA') ? 'afr':'eng';
        $address = 'region_address_'.$language;
        $patterns = array('/###tel###/','/###fax###/','/###address###/');
        $replacements = array($r->region_telephone,$r->region_fax,$r->$address);
        $signatureFile = $language . '_signature.htm';
        $signature = preg_replace($patterns,$replacements,file_get_contents('email/signatures/'.$signatureFile));
        return $html.$signature;
    }
    
    function addLetterHeadToBody($region,$html) {
        $xmlFile = simplexml_load_file('email/letterhead.config.xml');
        $sideImage = (string)$xmlFile->sideImage;
        $headerImage = (string)$xmlFile->headerImage;
        foreach ($xmlFile->footerImage->Children() as $f) if ($f['region']==$region) $footerImage = $f['filename'];
        $html = '<div style="float: left;margin-right: 10px;"><img src="https://int.biblesociety.co.za/images/'.$sideImage.'" width="80" /></div>
                <div style="float: left;"><img src="https://int.biblesociety.co.za/images/'.$headerImage.'" width="700" /></div>
                <div style="float: left;width: 700px;">'.$html.'</div>
                <div style="clear: both;text-align: center;width: 900px;"><img src="https://int.biblesociety.co.za/images/'.$footerImage.'" width="750" /></div>';
        return $html;
    }
}