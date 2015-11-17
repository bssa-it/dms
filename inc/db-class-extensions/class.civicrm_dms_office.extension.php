<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class civicrm_dms_office_extension extends civicrm_dms_office {
    function __construct($id = null) {
        parent::__construct($id);
    }
    public static function getAll() {
        $sql = "select * from civicrm_dms_office order by `name`;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
}