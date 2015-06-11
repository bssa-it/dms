<?php

Class budget { 

var $bud_id;
var $bud_region;
var $bud_department;
var $bud_category;
var $bud_amount;
var $bud_insert_user;
var $bud_dateinserted;
var $bud_datelastupdated;
var $bud_update_user;
var $bud_deleted;

function Load($id) {

    $sql = "SELECT * FROM `dms_budget` where `bud_id` = $id";
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
	if (isset($this->bud_id) && !empty($this->bud_id)) {
	$sql = "
            UPDATE `dms_budget` SET
              `bud_region` = '$this->bud_region',
              `bud_department` = '$this->bud_department',
              `bud_category` = '$this->bud_category',
              `bud_amount` = '$this->bud_amount',
              `bud_datelastupdated` = '$this->bud_datelastupdated',
              `bud_update_user` = '$this->bud_update_user'
            WHERE
                `bud_id` = '$this->bud_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_budget` (
              `bud_region`,
              `bud_department`,
              `bud_category`,
              `bud_amount`,
              `bud_insert_user`,
              `bud_dateinserted`
    ) VALUES (
              '$this->bud_region',
              '$this->bud_department',
              '$this->bud_category',
              '$this->bud_amount',
              '$this->bud_insert_user',
              '$this->bud_dateinserted'
    );";
    }
    #echo $sql;
    $result = $GLOBALS['db']->execute($sql);
    $this->bud_id = (empty($this->bud_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->bud_id;
}

function delete()
{
    $this->MySqlEscape();
	if (isset($this->bud_id) && !empty($this->bud_id)) {
	   $sql = "DELETE FROM `dms_budget` WHERE `bud_id` = '$this->bud_id';";
    } else {
        return;
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->bud_id = null;
    return $result;
}
# end class	
}

?>
