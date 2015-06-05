<?php

Class civicrm_dms_department { 

var $id;
var $dep_id;
var $dep_name;
var $dep_home_region;
var $dep_is_national;
var $dep_budget_allocation;
var $dep_chart_color;
var $dep_contact_id;

function Load($id) {

    $sql = "SELECT * FROM `civicrm_dms_department` where `id` = $id";
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
            UPDATE `civicrm_dms_department` SET
              `dep_id` = '$this->dep_id',
              `dep_name` = '$this->dep_name',
              `dep_home_region` = '$this->dep_home_region',
              `dep_is_national` = '$this->dep_is_national',
              `dep_budget_allocation` = '$this->dep_budget_allocation',
              `dep_chart_color` = '$this->dep_chart_color',
              `dep_contact_id` = '$this->dep_contact_id'
            WHERE
                `id` = '$this->id';";
    } ELSE {
    $sql = "
            INSERT INTO `civicrm_dms_department` (
              `dep_id`,
              `dep_name`,
              `dep_home_region`,
              `dep_is_national`,
              `dep_budget_allocation`,
              `dep_chart_color`,
              `dep_contact_id`
    ) VALUES (
              '$this->dep_id',
              '$this->dep_name',
              '$this->dep_home_region',
              '$this->dep_is_national',
              '$this->dep_budget_allocation',
              '$this->dep_chart_color',
              '$this->dep_contact_id'
    );";
    }
    $GLOBALS['functions']->showSql($sql);
    $result = $GLOBALS['civiDb']->execute($sql);
    $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
}

# end class	
}