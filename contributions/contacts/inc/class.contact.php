<?php

include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_acknowledgement_preferences.extension.php");
include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_contact_reporting_code.extension.php");
include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_contact_other_data.extension.php");

Class contact {
    
    var $civicrmApiObject;
    var $dmsDonorObject;
    
    function __construct($contactVariables=null) {
        $this->civicrmApiObject['version'] = 3;
        if (empty($contactVariables)) $this->civicrmApiObject['contact_type'] = 'Individual';
        if (is_int($contactVariables)) {
            $this->load($contactVariables);
            return;
        } else {
            $this->newDefault();
        }
        
        if (is_string($contactVariables)) $this->setContactTypes($contactVariables);
        if (is_array($contactVariables)) foreach ($contactVariables as $k=>$v) $this->civicrmApiObject[$k] = $v;
        if (isset($this->civicrmApiObject['contact_sub_type'])&&strtoupper($this->civicrmApiObject['contact_type'])==strtoupper($this->civicrmApiObject['contact_sub_type'])) unset($this->civicrmApiObject['contact_sub_type']);
    }
    
    function load($contactId) {
        $this->civicrmApiObject['version'] = 3;
        $this->civicrmApiObject['id'] = $contactId;
        $this->civicrmApiObject = civicrm_api('contact','getsingle',$this->civicrmApiObject);
        $this->loadDmsData();
    }
    
    function loadByExternalIdentifier($exid) {
        $this->civicrmApiObject['external_identifier'] = $exid;
        $this->civicrmApiObject = civicrm_api('contact','getsingle',$this->civicrmApiObject);
    }
    
    function loadDmsData() {
        if (empty($this->civicrmApiObject['id'])) return;
        $a = new civicrm_dms_acknowledgement_preferences_extension($this->civicrmApiObject['id']);
        foreach ($a as $k=>$v) if ($k!='id') $this->civicrmApiObject[$k] = $v;
        $r = new civicrm_dms_contact_reporting_code_extension($this->civicrmApiObject['id']);
        foreach ($r as $k=>$v) if ($k!='id') $this->civicrmApiObject[$k] = $v;
        $o = new civicrm_dms_contact_other_data_extension($this->civicrmApiObject['id']);
        foreach ($o as $k=>$v) if ($k!='id') $this->civicrmApiObject[$k] = $v;
    }
    
    function setContactTypes($contactType) {        
        $sql = "SELECT 
	CASE WHEN B.label IS NULL THEN B.label ELSE A.label END `contact_sub_type`,
	CASE WHEN B.label IS NULL THEN A.label ELSE B.label END `contact_type` 
FROM `civicrm_contact_type` A
LEFT JOIN civicrm_contact_type B ON A.parent_id = B.id"
                . " WHERE A.label = '$contactType'";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->civicrmApiObject['contact_type'] = $contactType;
        } else {
            $this->civicrmApiObject['contact_type'] = $result[0]['contact_type'];
            if (!empty($result[0]['contact_sub_type'])) $this->civicrmApiObject['contact_sub_type'] = $result[0]['contact_sub_type'];
        }
    }
    private function createNewContact() {
        $result = civicrm_api('Contact','create',$this->civicrmApiObject);
        if ($result['is_error']>0) {
            foreach ($result as $k=>$v) $this->civicrmApiObject[$k] = $v;
        } else {
            foreach ($result['values'][$result['id']] as $k=>$v) $this->civicrmApiObject[$k] = $v;
            $this->saveNextConfigContactNo();

            $a = new civicrm_dms_acknowledgement_preferences_extension($this->civicrmApiObject['id']);
            $a->Save();

            $r = new civicrm_dms_contact_reporting_code_extension($this->civicrmApiObject['id']);
            foreach ($this->civicrmApiObject as $k=>$v) if (isset($r->$k)) $r->$k = $v;
            $r->Save();

            $o = new civicrm_dms_contact_other_data_extension($this->civicrmApiObject['id']);
            foreach ($this->civicrmApiObject as $k=>$v) if (isset($r->$k)) $r->$k = $v;
            $o->Save();
            
            $this->loadDmsData();
        }
        return $result;
    }
    
    function save() {
        if (empty($this->civicrmApiObject['id'])) {
            $this->createNewContact();
            return;
        }
        $this->civicrmApiObject['version'] = 3;
        $result = civicrm_api('Contact','create',$this->civicrmApiObject);
        
        if ($result['is_error']>0) {
            foreach ($result as $k=>$v) $this->civicrmApiObject[$k] = $v;
            return;
        }
        
        $a = new civicrm_dms_acknowledgement_preferences_extension($this->civicrmApiObject['id']);
        foreach ($a as $k=>$v) if ($k!='id'&&isset($this->civicrmApiObject[$k])) $a->$k = $this->civicrmApiObject[$k];
        $a->Save();
        
        $r = new civicrm_dms_contact_reporting_code_extension($this->civicrmApiObject['id']);
        foreach ($r as $k=>$v) if ($k!='id'&&isset($this->civicrmApiObject[$k])) $r->$k = $this->civicrmApiObject[$k];
        $r->Save();
        
        $o = new civicrm_dms_contact_other_data_extension($this->civicrmApiObject['id']);
        foreach ($o as $k=>$v) if ($k!='id'&&isset($this->civicrmApiObject[$k])) $o->$k = $this->civicrmApiObject[$k];
        $o->Save();
        
        $this->logContactChange();
    }
    
    function getNextExternalIdentifier() {
        $settings = simplexml_load_file('/var/www/joomla/dms/contacts/inc/config.xml');
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
    
    function saveNextConfigContactNo() {
        $settings = simplexml_load_file('/var/www/joomla/dms/contacts/inc/config.xml');
        $settings->nextExternalIdentifier['value'] = $this->civicrmApiObject['external_identifier']+1;
        file_put_contents('/var/www/joomla/dms/contacts/inc/config.xml', $settings->asXML());
    }
    
    function logContactChange() {
        if (empty($this->civicrmApiObject['id'])) return false;
        $o = new civicrm_dms_contact_other_data_extension($this->civicrmApiObject['id']);
        $o->modified_by_contact_id = $_SESSION['dms_user']['civ_contact_id'];
        $o->Save();
        
        $this->civicrmApiObject['modified_date'] = date("Y-m-d H:i:s");
        $update = civicrm_api('Contact','Create',$this->civicrmApiObject);
        return $update;
    }
    
    function getContactLog() {
        if (empty($this->civicrmApiObject['id'])) return false;
        $contactId = $this->civicrmApiObject['id'];
        $sql = "SELECT * FROM civicrm_log WHERE entity_id = $contactId AND entity_table = 'civicrm_contact'"
                . " ORDER BY modified_date DESC";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
    
    function getContactGroups() {
        if (empty($this->civicrmApiObject['id'])) return array();
        $gParams['version'] = 3;
        $gParams['contact_id'] = $this->civicrmApiObject['id'];
        $groups = civicrm_api('GroupContact','get',$gParams);
        return $groups;
    }
    
    function getEmailAddresses() {
        if (empty($this->civicrmApiObject['id'])) return array();
        $parms['version'] = 3;
        $parms['contact_id'] = $this->civicrmApiObject['id'];
        $result = civicrm_api('Email','get',$parms);
        return $result;
    }
    
    function getPostalAddresses() {
        if (empty($this->civicrmApiObject['id'])) return array();
        $parms['version'] = 3;
        $parms['contact_id'] = $this->civicrmApiObject['id'];
        $result = civicrm_api('Address','get',$parms);
        return $result;
    }
    
    function getContactNos() {
        if (empty($this->civicrmApiObject['id'])) return array();
        $parms['version'] = 3;
        $parms['contact_id'] = $this->civicrmApiObject['id'];
        $result = civicrm_api('Phone','get',$parms);
        return $result;
    }
    
    function getNotes() {
        if (empty($this->civicrmApiObject['id'])) return array();
        $parms['version'] = 3;
        $parms['entity_id'] = $this->civicrmApiObject['id'];
        $parms['entity_table'] = 'civicrm_contact';
        $result = civicrm_api('Notes','get',$parms);
        return $result;
    }
    
    function newDefault() {
        $defaults = simplexml_load_file("/var/www/joomla/dms/contacts/inc/config.xml");
        foreach ($defaults->defaults->contact->children() as $k=>$v) {
            $key = (string)$k;
            $value = (string)$v;
            $this->civicrmApiObject[$key] = $value;
        }
        $this->civicrmApiObject['external_identifier'] = $this->getNextExternalIdentifier();
    }

}