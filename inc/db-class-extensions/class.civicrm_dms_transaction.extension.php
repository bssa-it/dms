<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_transaction.php");
Class civicrm_dms_transaction_extension extends civicrm_dms_transaction { 
    
    function __construct($contributionId = null) {
        if (!is_null($contributionId)) $this->LoadByContributionId($contributionId);
    }
    
    function LoadByContributionId($contributionId) {
        $sql = "SELECT * FROM `civicrm_dms_transaction` where `contribution_id` = '$contributionId'";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->contribution_id = $contributionId;
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                $this->$k = stripslashes($v);
            }
            return true;
        }
    }

    function Save() {
        $this->MySqlEscape();
        $completedDate = (empty($this->completed_date)) ? 'null':"'{$this->completed_date}'";
        $completedByUserId = (empty($this->completed_by_user_id)) ? 'null':$this->completed_by_user_id;
        if (isset($this->id) && !empty($this->id)) {
            $sql = "
                UPDATE `civicrm_dms_transaction` SET
                    `contribution_id` = '$this->contribution_id',
                    `motivation_id` = '$this->motivation_id',
                    `category_id` = '$this->category_id',
                    `region_id` = '$this->region_id',
                    `organisation_id` = '$this->organisation_id',
                    `must_acknowledge` = '$this->must_acknowledge',
                    `completed_date` = $completedDate,
                    `completed_by_user_id` = $completedByUserId,
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
                    $completedDate,
                    $completedByUserId,
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