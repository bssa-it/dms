<?php

Class menu { 

var $men_id;
var $men_name;
var $men_level;
var $men_parent;
var $men_requiredAuthorisation;
var $men_destination;
var $men_order;
var $men_htmlId;
var $men_active;

function Load($id) {

    $sql = "SELECT * FROM `dms_menu` where `men_id` = $id";
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
	if (isset($this->men_id) && !empty($this->men_id)) {
	$sql = "
            UPDATE `dms_menu` SET
              `men_name` = '$this->men_name',
              `men_level` = '$this->men_level',
              `men_parent` = '$this->men_parent',
              `men_requiredAuthorisation` = '$this->men_requiredAuthorisation',
              `men_destination` = '$this->men_destination',
              `men_order` = '$this->men_order',
              `men_htmlId` = '$this->men_htmlId',
              `men_active` = '$this->men_active'
            WHERE
                `men_id` = '$this->men_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_menu` (
              `men_name`,
              `men_level`,
              `men_parent`,
              `men_requiredAuthorisation`,
              `men_destination`,
              `men_order`,
              `men_htmlId`,
              `men_active`
    ) VALUES (
              '$this->men_name',
              '$this->men_level',
              '$this->men_parent',
              '$this->men_requiredAuthorisation',
              '$this->men_destination',
              '$this->men_order',
              '$this->men_htmlId',
              '$this->men_active'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->men_id = (empty($this->men_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->men_id;
	
}

# end class	
}

?>
