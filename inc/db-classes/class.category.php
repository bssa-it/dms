<?php

Class category { 

var $cat_id;
var $cat_name;
var $cat_bgt;
var $cat_tax_exempt;
var $subregion;
var $cat_departments;



function Load($id) {

    $sql = "SELECT * FROM `dms_category` where `cat_id` = $id";
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
	if (isset($this->cat_id) && !empty($this->cat_id)) {
	$sql = "
            UPDATE `dms_category` SET
              `cat_name` = '$this->cat_name',
              `cat_bgt` = '$this->cat_bgt',
              `cat_tax_exempt` = '$this->cat_tax_exempt',
              `subregion` = '$this->subregion',
              `cat_departments` = '$this->cat_departments'
            WHERE
                `cat_id` = '$this->cat_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_category` (
              `cat_name`,
              `cat_bgt`,
              `cat_tax_exempt`,
              `subregion`,
              `cat_departments`
    ) VALUES (
              '$this->cat_name',
              '$this->cat_bgt',
              '$this->cat_tax_exempt',
              '$this->subregion',
              '$this->cat_departments'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->cat_id = (empty($this->cat_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->cat_id;
	
}

# end class	
}

?>
