<?php

Class transaction { 

var $trns_id;
var $civ_contribution_id;
var $civ_trxn_id;
var $civ_financial_type_id;
var $civ_payment_instrument_id;
var $civ_contribution_status_id;
var $civ_total_amount;
var $civ_contact_id;
var $trns_date_received;
var $trns_amount_received;
var $trns_dnr_no;
var $trns_receipt_no;
var $trns_receipt_type;
var $trns_organisation_id;
var $trns_motivation_id;
var $trns_region_id;
var $trns_category_id;
var $trns_dms_indicator;
var $trns_dnr_acknowledged;
var $trns_acknowledgement_date;
var $dms_etl_error_id;
var $dms_mtd_trxn_count;
var $dms_is_first_trxn;
var $dms_must_acknowledge;

function Load($id) {

    $sql = "SELECT * FROM `dms_transaction` where `trns_id` = $id";
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
	if (isset($this->trns_id) && !empty($this->trns_id)) {
	$sql = "
            UPDATE `dms_transaction` SET
              `civ_contribution_id` = '$this->civ_contribution_id',
              `civ_trxn_id` = '$this->civ_trxn_id',
              `civ_financial_type_id` = '$this->civ_financial_type_id',
              `civ_payment_instrument_id` = '$this->civ_payment_instrument_id',
              `civ_contribution_status_id` = '$this->civ_contribution_status_id',
              `civ_total_amount` = '$this->civ_total_amount',
              `civ_contact_id` = '$this->civ_contact_id',
              `trns_date_received` = '$this->trns_date_received',
              `trns_amount_received` = '$this->trns_amount_received',
              `trns_dnr_no` = '$this->trns_dnr_no',
              `trns_receipt_no` = '$this->trns_receipt_no',
              `trns_receipt_type` = '$this->trns_receipt_type',
              `trns_organisation_id` = '$this->trns_organisation_id',
              `trns_motivation_id` = '$this->trns_motivation_id',
              `trns_region_id` = '$this->trns_region_id',
              `trns_category_id` = '$this->trns_category_id',
              `trns_dms_indicator` = '$this->trns_dms_indicator',
              `trns_dnr_acknowledged` = '$this->trns_dnr_acknowledged',
              `trns_acknowledgement_date` = '$this->trns_acknowledgement_date',
              `dms_etl_error_id` = '$this->dms_etl_error_id',
              `dms_mtd_trxn_count` = '$this->dms_mtd_trxn_count',
              `dms_is_first_trxn` = '$this->dms_is_first_trxn',
              `dms_must_acknowledge` = '$this->dms_must_acknowledge'
            WHERE
                `trns_id` = '$this->trns_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_transaction` (
              `civ_contribution_id`,
              `civ_trxn_id`,
              `civ_financial_type_id`,
              `civ_payment_instrument_id`,
              `civ_contribution_status_id`,
              `civ_total_amount`,
              `civ_contact_id`,
              `trns_date_received`,
              `trns_amount_received`,
              `trns_dnr_no`,
              `trns_receipt_no`,
              `trns_receipt_type`,
              `trns_organisation_id`,
              `trns_motivation_id`,
              `trns_region_id`,
              `trns_category_id`,
              `trns_dms_indicator`,
              `trns_dnr_acknowledged`,
              `trns_acknowledgement_date`,
              `dms_etl_error_id`,
              `dms_mtd_trxn_count`,
              `dms_is_first_trxn`,
              `dms_must_acknowledge`
    ) VALUES (
              '$this->civ_contribution_id',
              '$this->civ_trxn_id',
              '$this->civ_financial_type_id',
              '$this->civ_payment_instrument_id',
              '$this->civ_contribution_status_id',
              '$this->civ_total_amount',
              '$this->civ_contact_id',
              '$this->trns_date_received',
              '$this->trns_amount_received',
              '$this->trns_dnr_no',
              '$this->trns_receipt_no',
              '$this->trns_receipt_type',
              '$this->trns_organisation_id',
              '$this->trns_motivation_id',
              '$this->trns_region_id',
              '$this->trns_category_id',
              '$this->trns_dms_indicator',
              '$this->trns_dnr_acknowledged',
              '$this->trns_acknowledgement_date',
              '$this->dms_etl_error_id',
              '$this->dms_mtd_trxn_count',
              '$this->dms_is_first_trxn',
              '$this->dms_must_acknowledge'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->trns_id = (empty($this->trns_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->trns_id;
	
}

# end class	
}

?>