<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_region.php");
Class civicrm_dms_region_extension extends civicrm_dms_region { 
    
    function __construct($orgId = null) {
        if (!is_null($orgId)) $this->LoadByOrgId ($orgId);
    }
    
    function LoadByRegionId($regId) {
        $sql = "SELECT * FROM `civicrm_dms_region` where `reg_id` = '$regId'";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->reg_id = $regId;
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                $this->$k = stripslashes($v);
            }
            return true;
        }
    }

    function getAll() {
        $sql = "SELECT * FROM `civicrm_dms_region` order by reg_name";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array();
        } else {
            return $result;
        }
    }
}