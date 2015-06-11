<?php

Class civicrm_dms_office { 

        var $id;
	var $name;
	var $address_eng;
	var $address_afr;
	var $contact_id;
	var $business_manager_contact_id;
	

        function __construct($id=null) {
            if (!empty($id)) {
                $sql = "SELECT * FROM `civicrm_dms_office` where `id` = $id";
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
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_office` where `id` = $id";
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
            foreach ($this as $k => $v) $this->$k = mysql_real_escape_string($v,$GLOBALS['civiDb']->connection);
        }

        function Save() {
            $this->MySqlEscape();
            if (isset($this->id) && !empty($this->id)) {
                $sql = "
                    UPDATE `civicrm_dms_office` SET
                        `name` = '$this->name',
			`address_eng` = '$this->address_eng',
			`address_afr` = '$this->address_afr',
			`contact_id` = '$this->contact_id',
			`business_manager_contact_id` = '$this->business_manager_contact_id'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_office` (
                        `name`,
			`address_eng`,
			`address_afr`,
			`contact_id`,
			`business_manager_contact_id`
                    ) VALUES (
                        '$this->name',
			'$this->address_eng',
			'$this->address_afr',
			'$this->contact_id',
			'$this->business_manager_contact_id'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}