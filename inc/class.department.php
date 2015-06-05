<?php

Class department { 

var $dep_id;
var $dep_name;
var $dep_defaultRegion;
var $dep_isNational;
var $dep_budgetAllocation;
var $dep_chartColor;
var $dep_fromEmailName;
var $dep_fromEmailAddress;
var $dep_contact_id;

function Load($id) {

    $sql = "SELECT * FROM `dms_department` where `dep_id` = '$id'";
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
	if (isset($this->dep_id) && !empty($this->dep_id)) {
	$sql = "
            UPDATE `dms_department` SET
              `dep_name` = '$this->dep_name',
              `dep_defaultRegion` = '$this->dep_defaultRegion',
              `dep_isNational` = '$this->dep_isNational',
              `dep_budgetAllocation` = '$this->dep_budgetAllocation',
              `dep_chartColor` = '$this->dep_chartColor',
              `dep_fromEmailName` = '$this->dep_fromEmailName',
              `dep_fromEmailAddress` = '$this->dep_fromEmailAddress',
              `dep_contact_id` = '$this->dep_contact_id'
            WHERE
                `dep_id` = '$this->dep_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_department` (
              `dep_name`,
              `dep_defaultRegion`,
              `dep_isNational`,
              `dep_budgetAllocation`,
              `dep_chartColor`,
              `dep_fromEmailName`,
              `dep_fromEmailAddress`,
              `dep_contact_id`
    ) VALUES (
              '$this->dep_name',
              '$this->dep_defaultRegion',
              '$this->dep_isNational',
              '$this->dep_budgetAllocation',
              '$this->dep_chartColor',
              '$this->dep_fromEmailName',
              '$this->dep_fromEmailAddress',
              '$this->dep_contact_id'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->dep_id = (empty($this->dep_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->dep_id;
	
}

# end class	
}

?>