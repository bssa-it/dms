<?php

Class civicrm_dms_statement_entry { 

        var $id;
	var $deposit_date;
	var $deposit_reference;
	var $deposit_amount;
	var $balance;
	var $batch_id;
	var $reconciled;
	var $reconciled_datetime;
	var $reconciled_by;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_statement_entry` where `id` = $id";
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
                    UPDATE `civicrm_dms_statement_entry` SET
                        `deposit_date` = '$this->deposit_date',
			`deposit_reference` = '$this->deposit_reference',
			`deposit_amount` = '$this->deposit_amount',
			`balance` = '$this->balance',
			`batch_id` = '$this->batch_id',
			`reconciled` = '$this->reconciled',
			`reconciled_datetime` = '$this->reconciled_datetime',
			`reconciled_by` = '$this->reconciled_by'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_statement_entry` (
                        `deposit_date`,
			`deposit_reference`,
			`deposit_amount`,
			`balance`,
			`batch_id`,
			`reconciled`,
			`reconciled_datetime`,
			`reconciled_by`
                    ) VALUES (
                        '$this->deposit_date',
			'$this->deposit_reference',
			'$this->deposit_amount',
			'$this->balance',
			'$this->batch_id',
			'$this->reconciled',
			'$this->reconciled_datetime',
			'$this->reconciled_by'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}