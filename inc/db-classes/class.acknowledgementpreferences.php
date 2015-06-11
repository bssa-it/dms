<?php

Class acknowledgementpreferences { 

var $apr_id;
var $apr_contact_id;
var $apr_dnr_no;
var $apr_must_acknowledge;
var $apr_frequency;
var $apr_preferred_method;
var $apr_last_acknowledgement_date;
var $apr_last_acknowledgement_trns_id;
var $apr_unacknowledged_total;
var $apr_last_trns_date;

function Load($id) {

    $sql = "SELECT * FROM `dms_acknowledgementPreferences` where `apr_id` = $id";
    $result = $GLOBALS['db']->select($sql);
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
    foreach ($this as $k => $v) {
    	$this->$k = mysql_real_escape_string($v,$GLOBALS['db']->connection);
    }
}

function Save() {
	
	$this->MySqlEscape();
	if (isset($this->apr_id) && !empty($this->apr_id)) {
	$sql = "
            UPDATE `dms_acknowledgementPreferences` SET
              `apr_contact_id` = '$this->apr_contact_id',
              `apr_dnr_no` = '$this->apr_dnr_no',
              `apr_must_acknowledge` = '$this->apr_must_acknowledge',
              `apr_frequency` = '$this->apr_frequency',
              `apr_preferred_method` = '$this->apr_preferred_method',
              `apr_last_acknowledgement_date` = '$this->apr_last_acknowledgement_date',
              `apr_last_acknowledgement_trns_id` = '$this->apr_last_acknowledgement_trns_id',
              `apr_unacknowledged_total` = '$this->apr_unacknowledged_total',
              `apr_last_trns_date` = '$this->apr_last_trns_date'
            WHERE
                `apr_id` = '$this->apr_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_acknowledgementPreferences` (
              `apr_contact_id`,
              `apr_dnr_no`,
              `apr_must_acknowledge`,
              `apr_frequency`,
              `apr_preferred_method`,
              `apr_last_acknowledgement_date`,
              `apr_last_acknowledgement_trns_id`,
              `apr_unacknowledged_total`,
              `apr_last_trns_date`
    ) VALUES (
              '$this->apr_contact_id',
              '$this->apr_dnr_no',
              '$this->apr_must_acknowledge',
              '$this->apr_frequency',
              '$this->apr_preferred_method',
              '$this->apr_last_acknowledgement_date',
              '$this->apr_last_acknowledgement_trns_id',
              '$this->apr_unacknowledged_total',
              '$this->apr_last_trns_date'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->apr_id = (empty($this->apr_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->apr_id;
}

function LoadByContactId($contactId) {
    $sql = "SELECT * FROM `dms_acknowledgementPreferences` where `apr_contact_id` = $contactId";
    $result = $GLOBALS['db']->select($sql);
    if (!$result) {
    	return false;
    } else {
    	foreach ($result[0] as $k => $v) {
    		$this->$k = stripslashes($v);
    	}
    	return true;
    }
}

function getTotalContributions() {
    $sql = "SELECT SUM(`trns_amount_received`) `total` FROM dms_transaction WHERE civ_contact_id = $this->apr_contact_id AND `trns_id` > '$this->apr_last_acknowledgement_trns_id'";
    $result = $GLOBALS['db']->select($sql);
    if (!$result) {
            return false;
    } else {
            return $result[0]['total'];
    }
}

function setUnacknowledgedTotal() {
    $sql = "UPDATE dms_acknowledgementPreferences inner join  (SELECT trns_dnr_no,SUM(`trns_amount_received`) `total` FROM dms_transaction WHERE civ_contact_id = $this->apr_contact_id AND `trns_id` > '$this->apr_last_acknowledgement_trns_id' group by 1) `tmp` on `tmp`.`trns_dnr_no` = apr_dnr_no SET apr_unacknowledged_total = total WHERE apr_contact_id = $this->apr_contact_id";
    $result = $GLOBALS['db']->execute($sql);
}

function isTimeForAcknowledgment() {
    if (empty($this->apr_last_acknowledgement_date)) $this->apr_last_acknowledgement_date = $this->apr_last_trns_date;
    $daysToAdd = $this->apr_frequency*31;
    $adjustedDate = new Datetime(date("Y-m-d",strtotime($this->apr_last_acknowledgement_date . ' + ' . $daysToAdd)));
    $today = new Datetime();
    return ($today>=$adjustedDate);
}

# end class	
}

?>