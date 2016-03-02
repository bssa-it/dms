<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_receipt_entry.php");
class civicrm_dms_receipt_entry_extension extends civicrm_dms_receipt_entry {
    function __construct($id = null) {
        parent::__construct($id);
    }
    function loadReceiptEntries($receipt_id) {
        $sql = "select E.*,C.display_name `user` from civicrm_dms_receipt_entry E "
                . " inner join civicrm_contact C on C.id = E.received_by "
                . "where receipt_id = $receipt_id;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
    public static function getPaymentInstruments() {
        $cfg = simplexml_load_file('/var/www/joomla/dms/contributions/inc/config.xml');
        $optGroupid = (string)$cfg->paymentInstrumentOptionGroupId;
        $sql = "select * from civicrm_option_value where is_active = 1 and option_group_id = $optGroupid;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        } 
    }
    
    public static function getAllocations($receiptId) {
        $sql = "SELECT 
	E.id,
	receipt_id,
	received_datetime,
	received_by,
	receipt_no,
	receipt_amount,
	line_no,
	contact_id,
	E.motivation_id,
	external_identifier `dnr_no`,
	display_name,
	CONCAT(M.description,' - ',M.motivation_id) `motivation`,
        contribution_id,
        E.is_deleted
FROM 
	civicrm_dms_receipt_entry E  
	left JOIN civicrm_contact C ON C.id = E.contact_id  
	LEFT JOIN civicrm_dms_motivation M ON M.motivation_id = E.motivation_id 
WHERE receipt_id = $receiptId;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
    
    function setLineNo() {
        
        if (!empty($this->line_no)) return;
        
        if (empty($this->receipt_id)) {
            $this->line_no = 1;
        } else {
            $sql = "SELECT COUNT(*) `tot` FROM `civicrm_dms_receipt_entry` WHERE receipt_id = {$this->receipt_id};";
            $result = $GLOBALS['civiDb']->select($sql);
            if (!$result) {
                $this->line_no = 1;
            } else {
                $this->line_no = $result[0]['tot']+1;
            }
        }
        
    }
    
    function Save() {
        $this->setLineNo();
        $this->setNextReceiptNo();
        parent::Save();
    }
    
    function setNextReceiptNo($office=null) {
        
        if (!empty($this->receipt_no)) return;
        
        $cfgFile = '/var/www/joomla/dms/contributions/inc/config.xml';
        $cfg = simplexml_load_file($cfgFile);
        $defaultOffice = (int)$cfg->officeId;
        $nextReceiptNo = (int)$cfg->receiptNo++;
        if (empty($office)) {
            $office = (!empty($_SESSION['dms_user']['office_id'])) ? $_SESSION['dms_user']['office_id']:$defaultOffice;
        }
        $sql = "SELECT MAX(receipt_no) receipt_no FROM `civicrm_dms_receipt_entry` WHERE substr(receipt_no,1,1) = $office;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->receipt_no = $cfg->receipt_no = $nextReceiptNo;
            file_put_contents($cfgFile, $cfg->asXML());
        } else {
            if ($result[0]['receipt_no']>0) {
                $this->receipt_no = $result[0]['receipt_no'];
                $this->receipt_no++;
            } else {
                $padLen = strlen($nextReceiptNo)-1;
                $this->receipt_no = $office.  str_pad('1', $padLen,'0',STR_PAD_LEFT);
            }
        }
    }
}