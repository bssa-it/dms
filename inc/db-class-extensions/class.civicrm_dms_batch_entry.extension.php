<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_batch_entry.php");
class civicrm_dms_batch_entry_extension extends civicrm_dms_batch_entry {
    function __construct($id = null) {
        parent::__construct($id);
    }
    function loadBatchEntries($batch_id) {
        $sql = "select E.*,C.display_name `user` from civicrm_dms_batch_entry E "
                . " inner join civicrm_contact C on C.id = E.received_by "
                . "where batch_id = $batch_id;";
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
}