<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_contact_other_data.php");
Class civicrm_dms_contact_other_data_extension extends civicrm_dms_contact_other_data {
    
    function __construct($id = null) {
        if (!is_null($id)) $this->LoadByContactId($id);
    }
    
    function LoadByContactId($contact_id) {
        $sql = "SELECT * FROM `civicrm_dms_contact_other_data` where `contact_id` = $contact_id";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql); 
        if (!$result) {
            $this->newDefault($contact_id);
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                $this->$k = stripslashes($v);
            }
            return true;
        }
    }
    
    function newDefault($contact_id) {
        $this->contact_id = $contact_id;
        if (empty($this->contact_id)) return false;
        
        $defaults = simplexml_load_file("/var/www/joomla/dms/contacts/inc/config.xml");
        foreach ($defaults->defaults->other_data->children() as $k=>$v) {
            $key = (string)$k;
            $value = (string)$v;
            $this->$key = $value;
        }
        if (!empty($_SESSION['dms_user']['civ_contact_id'])) $this->inserted_by_contact_id = $_SESSION['dms_user']['civ_contact_id'];
    }
}