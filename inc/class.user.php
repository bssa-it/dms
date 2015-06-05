<?php

Class user { 

var $usr_id;
var $usr_q1_wid_id;
var $usr_q2_wid_id;
var $usr_q3_wid_id;
var $usr_q4_wid_id;
var $usr_contact_id;

function Load($id) {

    $sql = "SELECT * FROM `dms_user` where `usr_id` = $id";
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
function Save($joomlaUserId=null) {
	
    $this->MySqlEscape();
    if (isset($this->usr_id) && !empty($this->usr_id)) {
	$sql = "
            UPDATE `dms_user` SET
              `usr_q1_wid_id` = '$this->usr_q1_wid_id',
              `usr_q2_wid_id` = '$this->usr_q2_wid_id',
              `usr_q3_wid_id` = '$this->usr_q3_wid_id',
              `usr_q4_wid_id` = '$this->usr_q4_wid_id',
              `usr_contact_id` = '$this->usr_contact_id'
            WHERE
                `usr_id` = '$this->usr_id';";
    } ELSE {
        if (empty($joomlaUserId)) return false;
        $sql = "
            INSERT INTO `dms_user` (`usr_id`,
              `usr_q1_wid_id`,
              `usr_q2_wid_id`,
              `usr_q3_wid_id`,
              `usr_q4_wid_id`,
              `usr_contact_id`
    ) VALUES (
              $joomlaUserId,
              '$this->usr_q1_wid_id',
              '$this->usr_q2_wid_id',
              '$this->usr_q3_wid_id',
              '$this->usr_q4_wid_id',
              '$this->usr_contact_id'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->usr_id = (empty($this->usr_id)) ? $joomlaUserId : $this->usr_id;
	
}

# end class	
}