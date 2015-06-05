<?php

Class salutations { 

var $sal_id;
var $sal_department_id;
var $sal_denomination_id;
var $sal_category_id;
var $sal_type;
var $sal_language;
var $sal_text;
var $sal_active;
var $sal_created_user_id;
var $sal_created_date;

function Load($id) {

    $sql = "SELECT * FROM `dms_salutations` where `sal_id` = $id";
    $GLOBALS['functions']->showSql($sql);
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
	if (isset($this->sal_id) && !empty($this->sal_id)) {
	$sql = "
            UPDATE `dms_salutations` SET
              `sal_department_id` = '$this->sal_department_id',
              `sal_denomination_id` = '$this->sal_denomination_id',
              `sal_category_id` = '$this->sal_category_id',
              `sal_type` = '$this->sal_type',
              `sal_language` = '$this->sal_language',
              `sal_text` = '$this->sal_text',
              `sal_active` = '$this->sal_active',
              `sal_created_user_id` = '$this->sal_created_user_id',
              `sal_created_date` = '$this->sal_created_date'
            WHERE
                `sal_id` = '$this->sal_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_salutations` (
              `sal_department_id`,
              `sal_denomination_id`,
              `sal_category_id`,
              `sal_type`,
              `sal_language`,
              `sal_text`,
              `sal_active`,
              `sal_created_user_id`,
              `sal_created_date`
    ) VALUES (
              '$this->sal_department_id',
              '$this->sal_denomination_id',
              '$this->sal_category_id',
              '$this->sal_type',
              '$this->sal_language',
              '$this->sal_text',
              '$this->sal_active',
              '$this->sal_created_user_id',
              '$this->sal_created_date'
    );";
    }
    $GLOBALS['functions']->showSql($sql);
    $result = $GLOBALS['db']->execute($sql);
    $this->sal_id = (empty($this->sal_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->sal_id;
	
}

function Delete($id) {
    $sql = "DELETE FROM `dms_salutations` where `sal_id` = $id";
    $GLOBALS['functions']->showSql($sql);
    $result = $GLOBALS['db']->execute($sql);
    return (!$result);
}

# end class	
}