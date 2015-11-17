<?php

Class civicrm_dms_batch_header { 

        var $id;
	var $statement_entry_id;
	var $created_datetime;
	var $created_by;
	var $batch_title;
	var $batch_type_id;
	var $batch_status;
	var $batch_total;
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
            $sql = "SELECT * FROM `civicrm_dms_batch_header` where `id` = $id";
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
                    UPDATE `civicrm_dms_batch_header` SET
                        `statement_entry_id` = '$this->statement_entry_id',
			`created_datetime` = '$this->created_datetime',
			`created_by` = '$this->created_by',
			`batch_title` = '$this->batch_title',
			`batch_type_id` = '$this->batch_type_id',
			`batch_status` = '$this->batch_status',
			`batch_total` = '$this->batch_total',
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
                    INSERT INTO `civicrm_dms_batch_header` (
                        `statement_entry_id`,
			`created_datetime`,
			`created_by`,
			`batch_title`,
			`batch_type_id`,
			`batch_status`,
			`batch_total`,
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
			'$this->batch_title',
			'$this->batch_type_id',
			'$this->batch_status',
			'$this->batch_total',
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