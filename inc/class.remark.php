<?php

Class remark { 

var $drm_dnr_no;
var $drm_entry_date;
var $drm_upd_date;
var $drm_text;

function Load($dnrNo,$drmEntryDate) {

    $sql = "SELECT * FROM `dms_remark` where `drm_dnr_no` = $id and `drm_entry_date` = '$drmEntryDate'";
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
	if (isset($this->drm_dnr_no) && !empty($this->drm_dnr_no) && isset($this->drm_entry_date) && !empty($this->drm_entry_date)) {
	   $sql = "
            UPDATE `dms_remark` SET
              `drm_upd_date` = '$this->drm_upd_date',
              `drm_text` = '$this->drm_text'
            WHERE
                `drm_entry_date` = '$this->drm_entry_date'
                and
                `drm_dnr_no` = '$this->drm_dnr_no';";
        $result = $GLOBALS['db']->execute($sql);
    } 
    if (isset($this->drm_dnr_no) && !empty($this->drm_dnr_no)) {
        $this->drm_entry_date = date("Y-m-d H:i:s"); 
        $sql = "
            INSERT INTO `dms_remark` (
              `drm_dnr_no`,
              `drm_entry_date`,
              `drm_text`
        ) VALUES (
               $this->drm_dnr_no,
              '$this->drm_entry_date',
              '$this->drm_text'
        );";
        $result = $GLOBALS['db']->execute($sql);
    }
}

}

?>
