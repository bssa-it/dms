<?php

Class civicrm_dms_office { 

        var $id;
	var $business_manager_contact_id;
	var $name;
	var $address_eng;
	var $address_afr;
	var $telephone;
	var $fax;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
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
                        `business_manager_contact_id` = '$this->business_manager_contact_id',
			`name` = '$this->name',
			`address_eng` = '$this->address_eng',
			`address_afr` = '$this->address_afr',
			`telephone` = '$this->telephone',
			`fax` = '$this->fax'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_office` (
                        `business_manager_contact_id`,
			`name`,
			`address_eng`,
			`address_afr`,
			`telephone`,
			`fax`
                    ) VALUES (
                        '$this->business_manager_contact_id',
			'$this->name',
			'$this->address_eng',
			'$this->address_afr',
			'$this->telephone',
			'$this->fax'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}