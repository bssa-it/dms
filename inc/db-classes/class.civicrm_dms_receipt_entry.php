<?php

Class civicrm_dms_receipt_entry { 

        var $id;
	var $receipt_id;
	var $received_datetime;
	var $received_by;
	var $receipt_no;
	var $receipt_amount;
	var $line_no;
	var $contact_id;
	var $motivation_id;
	var $contribution_id;
	var $is_deleted;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_receipt_entry` where `id` = $id";
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
                    UPDATE `civicrm_dms_receipt_entry` SET
                        `receipt_id` = '$this->receipt_id',
			`received_datetime` = '$this->received_datetime',
			`received_by` = '$this->received_by',
			`receipt_no` = '$this->receipt_no',
			`receipt_amount` = '$this->receipt_amount',
			`line_no` = '$this->line_no',
			`contact_id` = '$this->contact_id',
			`motivation_id` = '$this->motivation_id',
			`contribution_id` = '$this->contribution_id',
			`is_deleted` = '$this->is_deleted'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_receipt_entry` (
                        `receipt_id`,
			`received_datetime`,
			`received_by`,
			`receipt_no`,
			`receipt_amount`,
			`line_no`,
			`contact_id`,
			`motivation_id`,
			`contribution_id`,
			`is_deleted`
                    ) VALUES (
                        '$this->receipt_id',
			'$this->received_datetime',
			'$this->received_by',
			'$this->receipt_no',
			'$this->receipt_amount',
			'$this->line_no',
			'$this->contact_id',
			'$this->motivation_id',
			'$this->contribution_id',
			'$this->is_deleted'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}