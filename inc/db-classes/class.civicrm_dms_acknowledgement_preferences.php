<?php

Class civicrm_dms_acknowledgement_preferences { 

        var $id;
	var $contact_id;
	var $must_acknowledge;
	var $frequency;
	var $preferred_method;
	var $last_acknowledgement_date;
	var $last_acknowledgement_trns_id;
	var $unacknowledged_total;
	var $last_contribution_date;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_acknowledgement_preferences` where `id` = $id";
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
                    UPDATE `civicrm_dms_acknowledgement_preferences` SET
                        `contact_id` = '$this->contact_id',
			`must_acknowledge` = '$this->must_acknowledge',
			`frequency` = '$this->frequency',
			`preferred_method` = '$this->preferred_method',
			`last_acknowledgement_date` = '$this->last_acknowledgement_date',
			`last_acknowledgement_trns_id` = '$this->last_acknowledgement_trns_id',
			`unacknowledged_total` = '$this->unacknowledged_total',
			`last_contribution_date` = '$this->last_contribution_date'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_acknowledgement_preferences` (
                        `contact_id`,
			`must_acknowledge`,
			`frequency`,
			`preferred_method`,
			`last_acknowledgement_date`,
			`last_acknowledgement_trns_id`,
			`unacknowledged_total`,
			`last_contribution_date`
                    ) VALUES (
                        '$this->contact_id',
			'$this->must_acknowledge',
			'$this->frequency',
			'$this->preferred_method',
			'$this->last_acknowledgement_date',
			'$this->last_acknowledgement_trns_id',
			'$this->unacknowledged_total',
			'$this->last_contribution_date'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}