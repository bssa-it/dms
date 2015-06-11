<?php

Class civicrm_dms_department_extension extends civicrm_dms_department { 

    function __construct($depId=null) {
        if (!empty($depId)) $this->loadByDepartment($depId);
    }
    
    function loadByDepartment($depId) {
        $sql = "SELECT * FROM `civicrm_dms_department` where `dep_id` = '$depId'";
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