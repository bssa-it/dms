<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_receipt_entry.php");
include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_receipt_entry.extension.php");
class contribution extends civicrm_dms_receipt_entry_extension {
    function __construct($id = null) {
        if (!empty($id)) parent::__construct($id);
        $isConnected = $this->isConnectedToCivi();
        if (!$isConnected) return;
        
        if (!empty($this->contribution_id)) {
            # load civi record
            $parms['version'] = 3;
            $parms['id'] = $this->contribution_id;
            $result = civicrm_api('Contact','Get',$parms);
            if (!empty($result['is_error'])) {
                foreach ($result['values'][0] as $k=>$v) {
                    if (empty($this->$k)) $this->$k = $v;
                }
            }
        }
    }
    
    function isConnectedToCivi(){
        if (empty($_SESSION['CiviCRM']['UserID'])) return false;
    }
    
    function Save() {
        #  START HERE
        
        parent::Save();
        if (empty($this->receipt_no)&&!empty($this->id)) {
            $filename = '/var/www/joomla/dms/contributions/inc/config.xml';
            $cfgXml = simplexml_load_file($filename);
            $this->receipt_no = (int)$cfgXml->receiptNo+1;
            parent::Save();
            $cfgXml->receiptNo = $this->receipt_no;
            file_put_contents($filename, $cfgXml->asXML());
        }
        if (empty($this->contribution_id)&&!empty($this->id)) {
            $parms['version'] = 3;
            foreach ($this as $k=>$v) $parms[$k] = $v;
            $result = civicrm_api('Contribution', 'create', $parms);
            if (!empty($result['is_error'])) {
                $this->contribution_id = $result['id'];
            }
        }
        
    }
    
}


        