<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_organisation.php");
Class civicrm_dms_organisation_extension extends civicrm_dms_organisation { 
    
    function __construct($orgId = null) {
        if (!is_null($orgId)) $this->LoadByOrgId ($orgId);
    }
    
    function LoadByOrgId($orgId) {

        $sql = "SELECT * FROM `civicrm_dms_organisation` where `org_id` = '$orgId'";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->org_id = $orgId;
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                    $this->$k = stripslashes($v);
            }
            return true;
        }
    }
    
    function getAll() {
        $sql = "SELECT * FROM `civicrm_dms_organisation` order by org_name";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array();
        } else {
            return $result;
        }
    }
}