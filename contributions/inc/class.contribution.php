<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_receipt_header.php");
include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_receipt_entry.php");
include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_receipt_header.extension.php");
include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_receipt_entry.extension.php");
include_once("/var/www/joomla/dms/contacts/inc/class.contact.php");
include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_acknowledgement.extension.php");

class contribution extends civicrm_dms_receipt_entry_extension {
    
    var $receipt_total;
    
    function __construct($id = null) {
        if (!empty($id)) parent::__construct($id);
    }
    
    function isConnectedToCivi(){
        return (empty($_SESSION['CiviCRM']['userID'])) ? false:true;
    }
    
    function Save() {
        parent::Save();
        $isConnected = $this->isConnectedToCivi();
        if (!$isConnected) return;
        if (empty($this->contribution_id)&&!empty($this->id)) {
            $parms['version'] = 3;
            if (!empty($this->receipt_no)) {
                $parms['trxn_id'] = $this->receipt_no;
                
                $search = civicrm_api('Contribution','get',$parms);
                
                if (empty($search['count'])) {
                    
                    $parms['contact_id'] = $this->contact_id;
                    $parms['financial_type_id'] = $this->getFinancialTypeId();
                    $parms['payment_instrument_id'] = $this->getPaymentInstrumentId();
                    $parms['receive_date'] = $this->received_datetime;
                    $parms['total_amount'] = $this->receipt_amount;
                    $parms['trxn_id'] = $this->receipt_no;
                    $parms['contribution_status_id'] = $this->getContributionStatusId();

                    $result = civicrm_api('Contribution', 'create', $parms);
                    if (empty($result['is_error'])) {   
                       $this->contribution_id = $result['id'];
                       parent::Save();
                       
                       $this->setReportDetail();
                       $this->updateReceiptTotal();
                    }
                } else {
                    $this->contribution_id = $search['id'];
                    parent::Save();
                }
                
            }
            
            
        }
    }
    function setReportDetail() {
        $c = new contact((int)$this->contact_id);
        $o = new civicrm_dms_organisation_extension($c->civicrmApiObject['organisation_id']);
        $t = new civicrm_dms_transaction_extension($this->contribution_id);
        $t->motivation_id = $this->motivation_id;
        $t->category_id = $c->civicrmApiObject['category_id'];
        $t->organisation_id = $c->civicrmApiObject['organisation_id'];
        $t->region_id = $o->org_region;
        
        $ack = new civicrm_dms_acknowledgement_extension($this->contribution_id);
        $t->must_acknowledge = ($ack->mustContributionBeAcknowledged()) ? 'Y':'N';

        $t->receipt_id = $this->receipt_id;
        $t->receipt_entry_id = $this->id;
        $t->contact_id = $this->contact_id;
        
        $h = new civicrm_dms_receipt_header_extension($this->receipt_id);
        if (!empty($h->completed_date)&&empty($t->completed_date)) {
            $t->completed_date = $h->completed_date;
            $t->completed_by_user_id = $_SESSION['dms_user']['civ_contact_id'];
        }
        
        $t->Save();
        
    }
    
    #  THIS FUNCTION IS WHERE WE DECIDE IF CONTRIBUTION IS COMPLETE
    function getContributionStatusId() {
        # FIRST GET CFG DEETS
        $cfgFile = '/var/www/joomla/dms/contributions/inc/config.xml';
        $cfg = simplexml_load_file($cfgFile);
        $defaultStatus = (int)$cfg->defaults->defaultContributionStatus;
        $completedStatus = (int)$cfg->defaults->completedContributionStatus;
        if (empty($this->receipt_id)) return $defaultStatus;
        
        # CHECK IF TRANSACTION IS COMPLETE
        $h = new civicrm_dms_receipt_header_extension($this->receipt_id);
        if (!empty($h->completed_date)) return $completedStatus;
        
        # CHECK IF TRANSACTION MUST BE REFUNDED
        # ...
        
        # RETURN DEFAULT STATUS
        return $defaultStatus;
    }
    
    function getPaymentInstrumentId() {
        $cfgFile = '/var/www/joomla/dms/contributions/inc/config.xml';
        $cfg = simplexml_load_file($cfgFile);
        $defaultPaymentInstrumentId = (int)$cfg->defaults->payment_instrument_id;
        if (empty($this->receipt_id)) return $defaultPaymentInstrumentId;
        $h = new civicrm_dms_receipt_header_extension($this->receipt_id);
        $t = new civicrm_dms_receipt_type_extension($h->receipt_type_id);
        return $t->payment_instrument_id;
    }
    
    function getFinancialTypeId() {
        $cfgFile = '/var/www/joomla/dms/contributions/inc/config.xml';
        $cfg = simplexml_load_file($cfgFile);
        $defaultFinancialTypeId = (int)$cfg->defaults->defaultFinancialTypeId;
        return $defaultFinancialTypeId;
    }
    function updateReceiptTotal() {
        if (empty($this->receipt_id)) return;
        $sql = "SELECT SUM(receipt_amount) `total` FROM civicrm_dms_receipt_entry WHERE receipt_id = {$this->receipt_id}";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->receipt_total = 0;
        } else {
            $this->receipt_total = $result[0]['total'];
        }
        
        $h = new civicrm_dms_receipt_header_extension($this->receipt_id);
        $h->receipt_total = $this->receipt_total;
        $h->Save();
    }
}


        
