<?php

Class civicrm_dms_batch_entry { 

        var $id;
	var $batch_id;
	var $received_datetime;
	var $received_by;
	var $receipt_no;
	var $receipt_amount;
	var $receipt_type;
	var $entry_no;

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_batch_entry` where `id` = $id";
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
                    UPDATE `civicrm_dms_batch_entry` SET
                        `batch_id` = '$this->batch_id',
			`received_datetime` = '$this->received_datetime',
			`received_by` = '$this->received_by',
			`receipt_no` = '$this->receipt_no',
			`receipt_amount` = '$this->receipt_amount',
			`receipt_type` = '$this->receipt_type',
                        `entry_no` = '$this->entry_no'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_batch_entry` (
                        `batch_id`,
			`received_datetime`,
			`received_by`,
			`receipt_no`,
			`receipt_amount`,
			`receipt_type`,
                        `entry_no`
                    ) VALUES (
                        '$this->batch_id',
			'$this->received_datetime',
			'$this->received_by',
			'$this->receipt_no',
			'$this->receipt_amount',
			'$this->receipt_type',
                        '$this->entry_no'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}