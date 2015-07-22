<?php

Class civicrm_dms_contact_other_data { 

        var $id;
	var $contact_id;
	var $do_not_thank;
	var $reminder_month;
	var $id_number;
	var $last_contribution_date;
	var $last_contribution_amount;
	var $inserted_by_contact_id;
	var $modified_by_contact_id;

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_contact_other_data` where `id` = $id";
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
                    UPDATE `civicrm_dms_contact_other_data` SET
                        `contact_id` = '$this->contact_id',
			`do_not_thank` = '$this->do_not_thank',
			`reminder_month` = '$this->reminder_month',
			`id_number` = '$this->id_number',
			`last_contribution_date` = '$this->last_contribution_date',
			`last_contribution_amount` = '$this->last_contribution_amount',
			`inserted_by_contact_id` = '$this->inserted_by_contact_id',
			`modified_by_contact_id` = '$this->modified_by_contact_id'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_contact_other_data` (
                        `contact_id`,
			`do_not_thank`,
			`reminder_month`,
			`id_number`,
			`last_contribution_date`,
			`last_contribution_amount`,
			`inserted_by_contact_id`,
			`modified_by_contact_id`
                    ) VALUES (
                        '$this->contact_id',
			'$this->do_not_thank',
			'$this->reminder_month',
			'$this->id_number',
			'$this->last_contribution_date',
			'$this->last_contribution_amount',
			'$this->inserted_by_contact_id',
			'$this->modified_by_contact_id'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}