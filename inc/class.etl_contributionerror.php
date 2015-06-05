<?php

Class etl_contributionerror { 

var $err_id;
var $trns_id;
var $dnr_no;
var $error_message;
var $sql;
var $error_date;
var $resolved_date;

function Load($id) {

    $sql = "SELECT * FROM `dms_etl_contributionError` where `err_id` = $id";
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
	if (isset($this->err_id) && !empty($this->err_id)) {
	$sql = "
            UPDATE `dms_etl_contributionError` SET
              `trns_id` = '$this->trns_id',
              `dnr_no` = '$this->dnr_no',
              `error_message` = '$this->error_message',
              `sql` = '$this->sql',
              `error_date` = '$this->error_date',
              `resolved_date` = '$this->resolved_date'
            WHERE
                `err_id` = '$this->err_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_etl_contributionError` (
              `trns_id`,
              `dnr_no`,
              `error_message`,
              `sql`,
              `error_date`,
              `resolved_date`
    ) VALUES (
              '$this->trns_id',
              '$this->dnr_no',
              '$this->error_message',
              '$this->sql',
              '$this->error_date',
              '$this->resolved_date'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->err_id = (empty($this->err_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->err_id;
	
}

# end class	
}

?>
