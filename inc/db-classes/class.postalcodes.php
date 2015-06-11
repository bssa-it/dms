<?php

Class postalcodes { 

var $pco_id;
var $pco_suburb;
var $pco_postal_code;
var $pco_area;
var $pco_type;
var $pco_lat;
var $pco_lon;

function Load($id) {

    $sql = "SELECT * FROM `dms_postalCodes` where `pco_id` = $id";
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
    	$this->$k = mysql_real_escape_string($v);
    }
}
function Save() {
	
	$this->MySqlEscape();
	if (isset($this->pco_id) && !empty($this->pco_id)) {
	$sql = "
            UPDATE `dms_postalCodes` SET
              `pco_suburb` = '$this->pco_suburb',
              `pco_postal_code` = '$this->pco_postal_code',
              `pco_area` = '$this->pco_area',
              `pco_type` = '$this->pco_type',
              `pco_lat` = '$this->pco_lat',
              `pco_lon` = '$this->pco_lon'
            WHERE
                `pco_id` = '$this->pco_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_postalCodes` (
              `pco_suburb`,
              `pco_postal_code`,
              `pco_area`,
              `pco_type`,
              `pco_lat`,
              `pco_lon`
    ) VALUES (
              '$this->pco_suburb',
              '$this->pco_postal_code',
              '$this->pco_area',
              '$this->pco_type',
              '$this->pco_lat',
              '$this->pco_lon'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->pco_id = (empty($this->pco_id)) ? mysql_insert_id() : $this->pco_id;
	
}

# end class	
}

?>
