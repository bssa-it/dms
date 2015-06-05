<?php

Class orgunit { 

var $org_id;
var $org_type;
var $org_name;
var $org_addr;
var $org_pcode;
var $org_rep_name;
var $org_year_end;
var $org_members;
var $org_subregion;

function Load($id) {

    $sql = "SELECT * FROM `dms_orgunit` where `org_id` = $id";
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
	if (isset($this->org_id) && !empty($this->org_id)) {
	$sql = "
            UPDATE `dms_orgunit` SET
              `org_type` = '$this->org_type',
              `org_name` = '$this->org_name',
              `org_addr` = '$this->org_addr',
              `org_pcode` = '$this->org_pcode',
              `org_rep_name` = '$this->org_rep_name',
              `org_year_end` = '$this->org_year_end',
              `org_members` = '$this->org_members',
              `org_subregion` = '$this->org_subregion'
            WHERE
                `org_id` = '$this->org_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_orgunit` (
              `org_type`,
              `org_name`,
              `org_addr`,
              `org_pcode`,
              `org_rep_name`,
              `org_year_end`,
              `org_members`,
              `org_subregion`
    ) VALUES (
              '$this->org_type',
              '$this->org_name',
              '$this->org_addr',
              '$this->org_pcode',
              '$this->org_rep_name',
              '$this->org_year_end',
              '$this->org_members',
              '$this->org_subregion'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->org_id = (empty($this->org_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->org_id;
	
}

# end class	
}

?>