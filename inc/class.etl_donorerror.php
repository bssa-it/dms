<?php

Class etl_donorerror { 

var $err_id;
var $dnr_no;
var $error_message;
var $sql;
var $error_date;
var $error_resolved;
var $resolved_date;

function Load($id) {

    $sql = "SELECT * FROM `dms_etl_donorError` where `err_id` = $id";
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
            UPDATE `dms_etl_donorError` SET
              `dnr_no` = '$this->dnr_no',
              `error_message` = '$this->error_message',
              `sql` = '$this->sql',
              `error_date` = '$this->error_date',
              `error_resolved` = '$this->error_resolved',
              `resolved_date` = '$this->resolved_date'
            WHERE
                `err_id` = '$this->err_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_etl_donorError` (
              `dnr_no`,
              `error_message`,
              `sql`,
              `error_date`,
              `error_resolved`,
              `resolved_date`
    ) VALUES (
              '$this->dnr_no',
              '$this->error_message',
              '$this->sql',
              '$this->error_date',
              '$this->error_resolved',
              '$this->resolved_date'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->err_id = (empty($this->err_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->err_id;
	
}

# end class	
}

?>
