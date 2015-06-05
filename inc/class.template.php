<?php

Class template { 

var $tpl_id;
var $tpl_name;
var $tpl_marginLeft;
var $tpl_marginTop;
var $tpl_marginRight;
var $tpl_marginBottom;
var $tpl_createdByUserId;
var $tpl_dateCreated;
var $tpl_accessLevel;
var $tpl_body;

function Load($id) {

    $sql = "SELECT * FROM `dms_template` where `tpl_id` = $id";
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
	if (isset($this->tpl_id) && !empty($this->tpl_id)) {
	$sql = "
            UPDATE `dms_template` SET
              `tpl_name` = '$this->tpl_name',
              `tpl_marginLeft` = '$this->tpl_marginLeft',
              `tpl_marginTop` = '$this->tpl_marginTop',
              `tpl_marginRight` = '$this->tpl_marginRight',
              `tpl_marginBottom` = '$this->tpl_marginBottom',
              `tpl_createdByUserId` = '$this->tpl_createdByUserId',
              `tpl_dateCreated` = '$this->tpl_dateCreated',
              `tpl_accessLevel` = '$this->tpl_accessLevel',
              `tpl_body` = '$this->tpl_body'
            WHERE
                `tpl_id` = '$this->tpl_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_template` (
              `tpl_name`,
              `tpl_marginLeft`,
              `tpl_marginTop`,
              `tpl_marginRight`,
              `tpl_marginBottom`,
              `tpl_createdByUserId`,
              `tpl_dateCreated`,
              `tpl_accessLevel`,
              `tpl_body`
    ) VALUES (
              '$this->tpl_name',
              '$this->tpl_marginLeft',
              '$this->tpl_marginTop',
              '$this->tpl_marginRight',
              '$this->tpl_marginBottom',
              '$this->tpl_createdByUserId',
              '$this->tpl_dateCreated',
              '$this->tpl_accessLevel',
              '$this->tpl_body'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->tpl_id = (empty($this->tpl_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->tpl_id;
	
}

# end class	
}

?>
