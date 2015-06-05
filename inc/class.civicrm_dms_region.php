<?php

Class civicrm_dms_region { 

var $id;
var $reg_id;
var $reg_name;
var $reg_consol_id;

function Load($id) {

    $sql = "SELECT * FROM `civicrm_dms_region` where `id` = $id";
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
            UPDATE `civicrm_dms_region` SET
              `reg_id` = '$this->reg_id',
              `reg_name` = '$this->reg_name',
              `reg_consol_id` = '$this->reg_consol_id'
            WHERE
                `id` = '$this->id';";
    } ELSE {
    $sql = "
            INSERT INTO `civicrm_dms_region` (
              `reg_id`,
              `reg_name`,
              `reg_consol_id`
    ) VALUES (
              '$this->reg_id',
              '$this->reg_name',
              '$this->reg_consol_id'
    );";
    }
    $GLOBALS['functions']->showSql($sql);
    $result = $GLOBALS['civiDb']->execute($sql);
    $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
}

# end class	
}