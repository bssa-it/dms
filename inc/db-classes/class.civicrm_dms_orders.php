<?php

Class civicrm_dms_orders { 

        var $id;
	var $beneficiary_contact_id;
	var $owner_contact_id;
	var $type;
	var $order_date;
	var $amount;
	var $reference;
	var $motivation_id;
	var $approved;
	var $bank_id;
	var $acccount_type;
	var $account_no;
	var $account_branch_code;
	var $cvv;
	var $expiry_date;
	var $credit_card_type;
	

        function __construct($id=null) {
            if (!empty($id)) {
                $sql = "SELECT * FROM `civicrm_dms_orders` where `id` = $id";
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
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_orders` where `id` = $id";
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
                    UPDATE `civicrm_dms_orders` SET
                        `beneficiary_contact_id` = '$this->beneficiary_contact_id',
			`owner_contact_id` = '$this->owner_contact_id',
			`type` = '$this->type',
			`order_date` = '$this->order_date',
			`amount` = '$this->amount',
			`reference` = '$this->reference',
			`motivation_id` = '$this->motivation_id',
			`approved` = '$this->approved',
			`bank_id` = '$this->bank_id',
			`acccount_type` = '$this->acccount_type',
			`account_no` = '$this->account_no',
			`account_branch_code` = '$this->account_branch_code',
			`cvv` = '$this->cvv',
			`expiry_date` = '$this->expiry_date',
			`credit_card_type` = '$this->credit_card_type'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_orders` (
                        `beneficiary_contact_id`,
			`owner_contact_id`,
			`type`,
			`order_date`,
			`amount`,
			`reference`,
			`motivation_id`,
			`approved`,
			`bank_id`,
			`acccount_type`,
			`account_no`,
			`account_branch_code`,
			`cvv`,
			`expiry_date`,
			`credit_card_type`
                    ) VALUES (
                        '$this->beneficiary_contact_id',
			'$this->owner_contact_id',
			'$this->type',
			'$this->order_date',
			'$this->amount',
			'$this->reference',
			'$this->motivation_id',
			'$this->approved',
			'$this->bank_id',
			'$this->acccount_type',
			'$this->account_no',
			'$this->account_branch_code',
			'$this->cvv',
			'$this->expiry_date',
			'$this->credit_card_type'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}