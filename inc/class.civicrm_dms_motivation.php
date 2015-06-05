<?php

Class civicrm_dms_motivation { 

var $id;
var $motivation_id;
var $description;

function Load($id) {

    $sql = "SELECT * FROM `civicrm_dms_motivation` where `id` = $id";
    $GLOBALS['functions']->showSql($sql);
    $result = $GLOBALS['civiDb']->select($sql);
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
    	$this->$k = mysql_real_escape_string($v,$GLOBALS['civiDb']->connection);
    }
}

function Save() {
	
	$this->MySqlEscape();
	if (isset($this->id) && !empty($this->id)) {
	$sql = "
            UPDATE `civicrm_dms_motivation` SET
              `motivation_id` = '$this->motivation_id',
              `description` = '$this->description'
            WHERE
                `id` = '$this->id';";
    } ELSE {
    $sql = "
            INSERT INTO `civicrm_dms_motivation` (
              `motivation_id`,
              `description`
    ) VALUES (
              '$this->motivation_id',
              '$this->description'
    );";
    }
    $GLOBALS['functions']->showSql($sql);
    $result = $GLOBALS['civiDb']->execute($sql);
    $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
}

# end class	
}
