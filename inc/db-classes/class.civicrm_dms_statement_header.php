<?php

Class civicrm_dms_statement_header { 

        var $id;
	var $imported_date;
	var $imported_usr_id;
	var $import_filename;
	var $bank_account_id;
	var $office_id;
	var $statement_date;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_statement_header` where `id` = $id";
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
                    UPDATE `civicrm_dms_statement_header` SET
                        `imported_date` = '$this->imported_date',
			`imported_usr_id` = '$this->imported_usr_id',
			`import_filename` = '$this->import_filename',
			`bank_account_id` = '$this->bank_account_id',
			`office_id` = '$this->office_id',
			`statement_date` = '$this->statement_date'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_statement_header` (
                        `imported_date`,
			`imported_usr_id`,
			`import_filename`,
			`bank_account_id`,
			`office_id`,
			`statement_date`
                    ) VALUES (
                        '$this->imported_date',
			'$this->imported_usr_id',
			'$this->import_filename',
			'$this->bank_account_id',
			'$this->office_id',
			'$this->statement_date'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}