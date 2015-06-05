<?php

Class profile { 

var $add_dnr_no;
var $add_dnr_name;
var $add_dnr_inits;
var $add_dnr_title;
var $add_addr;
var $add_post_cd;
var $tel_no;
var $fax_no;
var $cell_no;
var $e_mail;
var $spouse_name;
var $child1_name;
var $child2_name;
var $child3_name;
var $bday_spouse;
var $bday_child1;
var $bday_child2;
var $bday_child3;



function Load($id) {

    $sql = "SELECT * FROM `dms_profile` where `add_dnr_no` = $id";
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
	if (isset($this->add_dnr_no) && !empty($this->add_dnr_no)) {
	$sql = "
            UPDATE `dms_profile` SET
              `add_dnr_name` = '$this->add_dnr_name',
              `add_dnr_inits` = '$this->add_dnr_inits',
              `add_dnr_title` = '$this->add_dnr_title',
              `add_addr` = '$this->add_addr',
              `add_post_cd` = '$this->add_post_cd',
              `tel_no` = '$this->tel_no',
              `fax_no` = '$this->fax_no',
              `cell_no` = '$this->cell_no',
              `e_mail` = '$this->e_mail',
              `spouse_name` = '$this->spouse_name',
              `child1_name` = '$this->child1_name',
              `child2_name` = '$this->child2_name',
              `child3_name` = '$this->child3_name',
              `bday_spouse` = '$this->bday_spouse',
              `bday_child1` = '$this->bday_child1',
              `bday_child2` = '$this->bday_child2',
              `bday_child3` = '$this->bday_child3'
            WHERE
                `add_dnr_no` = '$this->add_dnr_no';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_profile` (
              `add_dnr_name`,
              `add_dnr_inits`,
              `add_dnr_title`,
              `add_addr`,
              `add_post_cd`,
              `tel_no`,
              `fax_no`,
              `cell_no`,
              `e_mail`,
              `spouse_name`,
              `child1_name`,
              `child2_name`,
              `child3_name`,
              `bday_spouse`,
              `bday_child1`,
              `bday_child2`,
              `bday_child3`
    ) VALUES (
              '$this->add_dnr_name',
              '$this->add_dnr_inits',
              '$this->add_dnr_title',
              '$this->add_addr',
              '$this->add_post_cd',
              '$this->tel_no',
              '$this->fax_no',
              '$this->cell_no',
              '$this->e_mail',
              '$this->spouse_name',
              '$this->child1_name',
              '$this->child2_name',
              '$this->child3_name',
              '$this->bday_spouse',
              '$this->bday_child1',
              '$this->bday_child2',
              '$this->bday_child3'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->add_dnr_no = (empty($this->add_dnr_no)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->add_dnr_no;
	
}

# end class	
}

?>
