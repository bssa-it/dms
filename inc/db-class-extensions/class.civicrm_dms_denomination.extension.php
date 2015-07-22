<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_denomination.php");
Class civicrm_dms_denomination_extension extends civicrm_dms_denomination { 
    
    function __construct($denId = null) {
        if (!is_null($denId)) $this->LoadByDenId ($denId);
    }
    
    function LoadByDenId($denId) {

        $sql = "SELECT * FROM `civicrm_dms_denomination` where `den_id` = '$denId'";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->den_id = $denId;
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                    $this->$k = stripslashes($v);
            }
            return true;
        }
    }
    
    function getAll() {
        $sql = "SELECT * FROM `civicrm_dms_denomination` order by den_name";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array();
        } else {
            return $result;
        }
    }
}