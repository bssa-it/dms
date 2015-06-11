<?php

Class civicrm_dms_organisation_extension extends civicrm_dms_organisation { 

    function LoadByOrgId($orgId) {

        $sql = "SELECT * FROM `civicrm_dms_organisation` where `org_id` = '$orgId'";
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