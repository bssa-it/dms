<?php

Class synod { 

var $id;
var $syn_id;
var $syn_den_id;
var $syn_name;



function Load($id) {

    $sql = "SELECT * FROM `dms_synod` where `id` = $id";
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
            UPDATE `dms_synod` SET
              `syn_id` = '$this->syn_id',
              `syn_den_id` = '$this->syn_den_id',
              `syn_name` = '$this->syn_name'
            WHERE
                `id` = '$this->id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_synod` (
              `syn_id`,
              `syn_den_id`,
              `syn_name`
    ) VALUES (
              '$this->syn_id',
              '$this->syn_den_id',
              '$this->syn_name'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->id;
	
}

# end class	
}

?>
