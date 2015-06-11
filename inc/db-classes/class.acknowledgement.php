<?php

Class acknowledgement { 

var $ack_id;
var $ack_date;
var $ack_usr_id;
var $ack_method;
var $ack_document;
var $ack_civi_con_id;
var $ack_trns_id;

function Load($id) {

    $sql = "SELECT * FROM `dms_acknowledgement` where `ack_id` = $id";
    $result = $GLOBALS['db']->select($sql);
    if (!$result) {
    	return false;
    } else {
    	foreach ($result[0] as $k => $v) {
    		$this->$k = stripslashes($v);
    	}
    	return true;
    }
}
	
function MySqlEscape() {
    foreach ($this as $k => $v) {
    	$this->$k = mysql_real_escape_string($v,$GLOBALS['db']->connection);
    }
}
function Save() {
	
	$this->MySqlEscape();
    $trnsId = (empty($this->ack_trns_id)) ? 'NULL':"'$this->ack_trns_id'";
	if (isset($this->ack_id) && !empty($this->ack_id)) {
	$sql = "
            UPDATE `dms_acknowledgement` SET
              `ack_date` = '$this->ack_date',
              `ack_usr_id` = '$this->ack_usr_id',
              `ack_method` = '$this->ack_method',
              `ack_document` = '$this->ack_document',
              `ack_civi_con_id` = '$this->ack_civi_con_id',
              `ack_trns_id` = $trnsId
            WHERE
                `ack_id` = '$this->ack_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_acknowledgement` (
              `ack_date`,
              `ack_usr_id`,
              `ack_method`,
              `ack_document`,
              `ack_civi_con_id`,
              `ack_trns_id`
    ) VALUES (
              '$this->ack_date',
              '$this->ack_usr_id',
              '$this->ack_method',
              '$this->ack_document',
              '$this->ack_civi_con_id',
              $trnsId
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->ack_id = (empty($this->ack_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->ack_id;
	
}

# end class	
}

?>
