<?php

Class civicrm_dms_acknowledgement { 

        var $id;
	var $contribution_id;
	var $acknowledgement_datetime;
	var $usr_id;
	var $method;
	var $document;
	

        function __construct($id=null) {
            if (!empty($id)) {
                $sql = "SELECT * FROM `civicrm_dms_acknowledgement` where `id` = $id";
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
            $sql = "SELECT * FROM `civicrm_dms_acknowledgement` where `id` = $id";
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
                    UPDATE `civicrm_dms_acknowledgement` SET
                        `contribution_id` = '$this->contribution_id',
			`acknowledgement_datetime` = '$this->acknowledgement_datetime',
			`usr_id` = '$this->usr_id',
			`method` = '$this->method',
			`document` = '$this->document'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_acknowledgement` (
                        `contribution_id`,
			`acknowledgement_datetime`,
			`usr_id`,
			`method`,
			`document`
                    ) VALUES (
                        '$this->contribution_id',
			'$this->acknowledgement_datetime',
			'$this->usr_id',
			'$this->method',
			'$this->document'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}