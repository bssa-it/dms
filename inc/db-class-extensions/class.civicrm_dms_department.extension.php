<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_department.php");
Class civicrm_dms_department_extension extends civicrm_dms_department { 

    function __construct($depId=null) {
        if (!is_null($depId)) $this->loadByDepartment($depId);
    }
    
    function loadByDepartment($depId) {
        $sql = "SELECT * FROM `civicrm_dms_department` where `dep_id` = '$depId'";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->dep_id = $depId;
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                $this->$k = stripslashes($v);
            }
            return true;
        }
    }
    
    function getDistinctDenominations() {
        if (empty($this->dep_id)) return array();
        $sql = "SELECT DISTINCT substr(`org_id`,2,2) `den_id`,`den_name` FROM `civicrm_dms_organisation` "
                . "inner join civicrm_dms_denomination on `den_id` = substr(`org_id`,2,2) "
                . "where substr(`org_id`,1,1) = '".$this->dep_id."' "
                . "order by substr(`org_id`,2,2)";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array();
        } else {
            return $result;
        }
    }
    
    function getAll() {
        $sql = "SELECT * FROM `civicrm_dms_department` order by dep_name";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array();
        } else {
            return $result;
        }
    }
}