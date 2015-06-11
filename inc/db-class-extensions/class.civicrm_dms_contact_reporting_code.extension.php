<?php

Class civicrm_dms_contact_reporting_code_extension extends civicrm_dms_contact_reporting_code { 
    
    function __construct($id = null) {
        if (!empty($id)) $this->LoadByContactId ($id);
    }
    
    function LoadByContactId($contact_id) {    
        $sql = "SELECT * FROM `civicrm_dms_contact_reporting_code` where `contact_id` = $contact_id";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->contact_id = $contact_id;
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                    $this->$k = stripslashes($v);
            }
            return true;
        }
    }

}