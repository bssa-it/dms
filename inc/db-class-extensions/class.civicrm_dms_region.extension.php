<?php

Class civicrm_dms_region_extension extends civicrm_dms_region { 
    
    function __construct($orgId = null) {
        if (!empty($orgId)) $this->LoadByOrgId ($orgId);
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

}