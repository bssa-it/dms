<?php

Class congregation { 

var $id;
var $con_id;
var $con_den_id;
var $con_sin_id;
var $con_cir_id;
var $con_name;



function Load($id) {

    $sql = "SELECT * FROM `dms_congregation` where `id` = $id";
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
	if (isset($this->id) && !empty($this->id)) {
	$sql = "
            UPDATE `dms_congregation` SET
              `con_id` = '$this->con_id',
              `con_den_id` = '$this->con_den_id',
              `con_sin_id` = '$this->con_sin_id',
              `con_cir_id` = '$this->con_cir_id',
              `con_name` = '$this->con_name'
            WHERE
                `id` = '$this->id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_congregation` (
              `con_id`,
              `con_den_id`,
              `con_sin_id`,
              `con_cir_id`,
              `con_name`
    ) VALUES (
              '$this->con_id',
              '$this->con_den_id',
              '$this->con_sin_id',
              '$this->con_cir_id',
              '$this->con_name'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->id;
	
}

# end class	
}

?>
