<?php

Class denomination { 

var $den_id;
var $den_name;
var $den_consol_category;



function Load($id) {

    $sql = "SELECT * FROM `dms_denomination` where `den_id` = $id";
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
	if (isset($this->den_id) && !empty($this->den_id)) {
	$sql = "
            UPDATE `dms_denomination` SET
              `den_name` = '$this->den_name',
              `den_consol_category` = '$this->den_consol_category'
            WHERE
                `den_id` = '$this->den_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_denomination` (
              `den_name`,
              `den_consol_category`
    ) VALUES (
              '$this->den_name',
              '$this->den_consol_category'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->den_id = (empty($this->den_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->den_id;
	
}

# end class	
}

?>
