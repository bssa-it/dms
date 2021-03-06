<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_receipt_header.php");
Class civicrm_dms_receipt_header_extension extends civicrm_dms_receipt_header { 
    function __construct($id = null) {
        parent::__construct($id);
    }
    
    function getAllOpenReceipts() {
        $sql = "SELECT B.*,C.display_name,O.`name` `office`,T.description `receipt_type` FROM civicrm_dms_receipt_header B "
                ." inner join civicrm_contact C on C.id = B.created_by "
                ." inner join civicrm_dms_receipt_type T on B.receipt_type_id = T.id "
                . " INNER JOIN `civicrm_dms_office` O ON O.id = B.`office_id` "
                . "WHERE receipt_status != 'closed';";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
}