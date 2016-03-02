<?php

Class civicrm_dms_receipt_type { 

        var $id;
	var $description;
	var $active;
	var $payment_instrument_id;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_receipt_type` where `id` = $id";
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
                    UPDATE `civicrm_dms_receipt_type` SET
                        `description` = '$this->description',
			`active` = '$this->active',
			`payment_instrument_id` = '$this->payment_instrument_id'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_receipt_type` (
                        `description`,
			`active`,
			`payment_instrument_id`
                    ) VALUES (
                        '$this->description',
			'$this->active',
			'$this->payment_instrument_id'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}