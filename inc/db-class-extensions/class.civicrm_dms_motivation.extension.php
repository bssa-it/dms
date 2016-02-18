<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_motivation.php");
class civicrm_dms_motivation_extension extends civicrm_dms_motivation {
    function __construct($id = null) {
        parent::__construct($id);
    }
    
    public static function getMotivationCodes() {
        $sql = "SELECT id,motivation_id,CASE WHEN LENGTH(TRIM(`description`)) = 0 THEN 'Unknown' ELSE `description` END `description` FROM civicrm_dms_motivation ORDER BY `description`;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        } 
    }
}