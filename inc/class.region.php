<?php

Class region { 

    var $region_id;
    var $region_name;
    var $region_consol_id;
    var $region_joomla_group_id;
    var $region_address_eng;
    var $region_address_afr;
    var $region_telephone;
    var $region_fax;
    
    function Load($id) {
    
        $sql = "SELECT * FROM `dms_region` where `region_id` = $id";
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
    	if (isset($this->region_id) && !empty($this->region_id)) {
    	$sql = "
                UPDATE `dms_region` SET
                  `region_name` = '$this->region_name',
                  `region_consol_id` = '$this->region_consol_id',
                  `region_joomla_group_id` = '$this->region_joomla_group_id',
                  `region_address_eng` = '$this->region_address_eng',
                  `region_address_afr` = '$this->region_address_afr',
                  `region_telephone` = '$this->region_telephone',
                  `region_fax` = '$this->region_fax'
                WHERE
                    `region_id` = '$this->region_id';";
        } ELSE {
        $sql = "
                INSERT INTO `dms_region` (
                  `region_name`,
                  `region_consol_id`,
                  `region_joomla_group_id`,
                  `region_address_eng`,
                  `region_telephone`,
                  `region_fax`
        ) VALUES (
                  '$this->region_name',
                  '$this->region_consol_id',
                  '$this->region_joomla_group_id',
                  '$this->region_address_eng',
                  '$this->region_address_afr',
                  '$this->region_telephone',
                  '$this->region_fax'
        );";
        }
        $result = $GLOBALS['db']->execute($sql);
        $this->region_id = (empty($this->region_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->region_id;
    	
    }
    
    # end class	
}