<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class civicrm_dms_receipt_type_extension extends civicrm_dms_receipt_type {
   function __construct($id = null) {
       parent::__construct($id);
   }
   public static function getAll() {
       $sql = "select * from civicrm_dms_receipt_type order by `description`;";
       $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
   }
   public static function getName($id) {
       $sql = "select `description` `name` from civicrm_dms_receipt_type where id = $id;";
       $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result[0]['name'];
        }
   }
}