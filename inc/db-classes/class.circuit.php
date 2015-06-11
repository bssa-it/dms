<?php

Class circuit { 

var $id;
var $cir_id;
var $cir_den_id;
var $cir_syn_id;
var $cir_name;



function Load($id) {

    $sql = "SELECT * FROM `dms_circuit` where `id` = $id";
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
            UPDATE `dms_circuit` SET
              `cir_id` = '$this->cir_id',
              `cir_den_id` = '$this->cir_den_id',
              `cir_syn_id` = '$this->cir_syn_id',
              `cir_name` = '$this->cir_name'
            WHERE
                `id` = '$this->id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_circuit` (
              `cir_id`,
              `cir_den_id`,
              `cir_syn_id`,
              `cir_name`
    ) VALUES (
              '$this->cir_id',
              '$this->cir_den_id',
              '$this->cir_syn_id',
              '$this->cir_name'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->id;
	
}

# end class	
}

?>
