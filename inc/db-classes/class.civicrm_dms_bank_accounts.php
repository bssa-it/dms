<?php

Class civicrm_dms_bank_accounts { 

        var $id;
	var $bank_id;
	var $name;
	var $branch_code;
	var $account_no;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_bank_accounts` where `id` = $id";
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
                    UPDATE `civicrm_dms_bank_accounts` SET
                        `bank_id` = '$this->bank_id',
			`name` = '$this->name',
			`branch_code` = '$this->branch_code',
			`account_no` = '$this->account_no'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_bank_accounts` (
                        `bank_id`,
			`name`,
			`branch_code`,
			`account_no`
                    ) VALUES (
                        '$this->bank_id',
			'$this->name',
			'$this->branch_code',
			'$this->account_no'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}