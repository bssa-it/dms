<?php

Class civicrm_dms_receipt_header { 

        var $id;
	var $statement_entry_id;
	var $created_datetime;
	var $created_by;
	var $receipt_title;
	var $receipt_type_id;
	var $receipt_status;
	var $receipt_total;
	var $office_id;
	var $bank_account_id;
	var $deposit_date;
	var $completed_date;
	var $completed_by;
	var $accpac_batch_no;
        var $statement_reference;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_receipt_header` where `id` = $id";
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
                    UPDATE `civicrm_dms_receipt_header` SET
                        `statement_entry_id` = '$this->statement_entry_id',
			`created_datetime` = '$this->created_datetime',
			`created_by` = '$this->created_by',
			`receipt_title` = '$this->receipt_title',
			`receipt_type_id` = '$this->receipt_type_id',
			`receipt_status` = '$this->receipt_status',
			`receipt_total` = '$this->receipt_total',
			`office_id` = '$this->office_id',
			`bank_account_id` = '$this->bank_account_id',
			`deposit_date` = '$this->deposit_date',
			`completed_date` = '$this->completed_date',
			`completed_by` = '$this->completed_by',
			`accpac_batch_no` = '$this->accpac_batch_no',
                        `statement_reference` = '$this->statement_reference'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_receipt_header` (
                        `statement_entry_id`,
			`created_datetime`,
			`created_by`,
			`receipt_title`,
			`receipt_type_id`,
			`receipt_status`,
			`receipt_total`,
			`office_id`,
			`bank_account_id`,
			`deposit_date`,
			`completed_date`,
			`completed_by`,
			`accpac_batch_no`,
                        `statement_reference`
                    ) VALUES (
                        '$this->statement_entry_id',
                        '$this->created_datetime',
			'$this->created_by',
			'$this->receipt_title',
			'$this->receipt_type_id',
			'$this->receipt_status',
			'$this->receipt_total',
			'$this->office_id',
			'$this->bank_account_id',
			'$this->deposit_date',
			'$this->completed_date',
			'$this->completed_by',
			'$this->accpac_batch_no',
                        '$this->statement_reference'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}