<?php

Class contact {
    
    var $civicrmApiObject;
    var $dmsDonorObject;
    
    function __construct($contactType=null) {
        $this->civicrmApiObject['contact_type'] = (!empty($contactType)) ? $contactType : 'Individual';
    }
    
    function createNewContact() {
        $this->civicrmApiObject['version'] = 3;
        $this->civicrmApiObject['external_identifier'] = $this->getNextExternalIdentifier();
        
        $result = civicrm_api('Contact','create',$this->civicrmApiObject);
        if ($result['is_error']>0) {
            foreach ($result as $k=>$v) {
                $this->civicrmApiObject[$k] = $v;
            }
        } else {
            foreach ($result['values'][$result['id']] as $k=>$v) {
                $this->civicrmApiObject[$k] = $v;
            }
            $settings = simplexml_load_file('/var/www/joomla/dms/contacts/config.xml');
            $settings->nextExternalIdentifier['value'] = $this->civicrmApiObject['external_identifier']+1;
            file_put_contents('/var/www/joomla/dms/contacts/config.xml', $settings->asXML());
        }
        return $result;
    }
    
    function getNextExternalIdentifier() {
        $settings = simplexml_load_file('/var/www/joomla/dms/contacts/config.xml');
        $nextId = (int)$settings->nextExternalIdentifier['value'];
        $civiDb = $GLOBALS['civiDb']->database;
        $nextIdOk = false;
        
        while (!$nextIdOk) {
            $sql = "SELECT COUNT(*) `cnt` FROM $civiDb.civicrm_contact WHERE external_identifier = " . $nextId;
            $result = $GLOBALS['civiDb']->select($sql);
            if (!$result) {
                return null;
            } else {
                $nextIdOk = ((int)$result[0]['cnt']<1);
                if (!$nextIdOk) $nextId++;
            }
        }
        return $nextId;
    }
}