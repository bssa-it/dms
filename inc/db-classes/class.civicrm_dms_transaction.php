<?php

Class civicrm_dms_transaction { 

        var $id;
	var $contribution_id;
	var $motivation_id;
	var $category_id;
	var $region_id;
	var $organisation_id;
	var $must_acknowledge;
	var $completed_date;
	var $completed_by_user_id;
	var $receipt_id;
	var $receipt_entry_id;
	var $contact_id;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_transaction` where `id` = $id";
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
                    UPDATE `civicrm_dms_transaction` SET
                        `contribution_id` = '$this->contribution_id',
			`motivation_id` = '$this->motivation_id',
			`category_id` = '$this->category_id',
			`region_id` = '$this->region_id',
			`organisation_id` = '$this->organisation_id',
			`must_acknowledge` = '$this->must_acknowledge',
			`completed_date` = '$this->completed_date',
			`completed_by_user_id` = '$this->completed_by_user_id',
			`receipt_id` = '$this->receipt_id',
			`receipt_entry_id` = '$this->receipt_entry_id',
			`contact_id` = '$this->contact_id'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_transaction` (
                        `contribution_id`,
			`motivation_id`,
			`category_id`,
			`region_id`,
			`organisation_id`,
			`must_acknowledge`,
			`completed_date`,
			`completed_by_user_id`,
			`receipt_id`,
			`receipt_entry_id`,
			`contact_id`
                    ) VALUES (
                        '$this->contribution_id',
			'$this->motivation_id',
			'$this->category_id',
			'$this->region_id',
			'$this->organisation_id',
			'$this->must_acknowledge',
			'$this->completed_date',
			'$this->completed_by_user_id',
			'$this->receipt_id',
			'$this->receipt_entry_id',
			'$this->contact_id'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}