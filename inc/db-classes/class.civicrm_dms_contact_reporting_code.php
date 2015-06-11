<?php

Class civicrm_dms_contact_reporting_code { 

        var $id;
	var $contact_id;
	var $organisation_id;
	var $category_id;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_contact_reporting_code` where `id` = $id";
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
                    UPDATE `civicrm_dms_contact_reporting_code` SET
                        `contact_id` = '$this->contact_id',
			`organisation_id` = '$this->organisation_id',
			`category_id` = '$this->category_id'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_contact_reporting_code` (
                        `contact_id`,
			`organisation_id`,
			`category_id`
                    ) VALUES (
                        '$this->contact_id',
			'$this->organisation_id',
			'$this->category_id'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}