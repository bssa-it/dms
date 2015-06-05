<?php

Class civilink { 

var $dms_dnr_no;
var $dms_civi_contact_id;



function Load($id) {

    $sql = "SELECT * FROM `dms_civiLink` where `dms_dnr_no` = $id";
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
	if (isset($this->dms_dnr_no) && !empty($this->dms_dnr_no)) {
	$sql = "
            UPDATE `dms_civiLink` SET
              `dms_civi_contact_id` = '$this->dms_civi_contact_id'
            WHERE
                `dms_dnr_no` = '$this->dms_dnr_no';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_civiLink` (
              `dms_civi_contact_id`
    ) VALUES (
              '$this->dms_civi_contact_id'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->dms_dnr_no = (empty($this->dms_dnr_no)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->dms_dnr_no;
	
}

# end class	
}

?>
