<?php

Class civicrm_dms_contact_other_data_extension extends civicrm_dms_contact_other_data {
    
    function __construct($id = null) {
        if (!empty($id)) $this->LoadByContactId($id);
    }
    
    function LoadByContactId($contact_id) {
        $sql = "SELECT * FROM `civicrm_dms_contact_other_data` where `contact_id` = $contact_id";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql); 
        if (!$result) {
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                $this->$k = stripslashes($v);
            }
            return true;
        }
    }
}