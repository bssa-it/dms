<?php

Class donor { 

var $dnr_no;
var $dnr_name;
var $dnr_inits;
var $dnr_title;
var $dnr_salut;
var $dnr_formal;
var $dnr_birth_date;
var $dnr_id;
var $dnr_occup_cd;
var $dnr_lang;
var $dnr_remnd_mnth;
var $dnr_pattern;
var $dnr_addr;
var $dnr_post_cd;
var $dnr_alt_addr;
var $dnr_cntr_tp;
var $dnr_first_date;
var $dnr_first_amnt;
var $dnr_tot_ytd;
var $dnr_tot_pyr;
var $dnr_tot_mtd;
var $dnr_tot_pmt;
var $dnr_tot;
var $dnr_sower;
var $dnr_thank;
var $dnr_bible_cl;
var $dnr_last_date;
var $dnr_last_amnt;
var $dnr_next_letter;
var $dnr_org_id;
var $dnr_tax_certf;
var $dnr_deleted;
var $dnr_del_date;
var $rep_org_id;
var $civ_contact_id;
var $civ_last_update;
var $dms_preferred_communication_type_id;

function Load($id) {

    $sql = "SELECT * FROM `dms_donor` where `dnr_no` = $id";
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
    $repOrgId = (empty($this->rep_org_id)) ? 'NULL':"'$this->rep_org_id'";
    $dnrOrgId = (empty($this->dnr_org_id)) ? 'NULL':"'$this->dnr_org_id'";
	if (isset($this->dnr_no) && !empty($this->dnr_no)) {
	$sql = "
            UPDATE `dms_donor` SET
              `dnr_name` = '$this->dnr_name',
              `dnr_inits` = '$this->dnr_inits',
              `dnr_title` = '$this->dnr_title',
              `dnr_salut` = '$this->dnr_salut',
              `dnr_formal` = '$this->dnr_formal',
              `dnr_birth_date` = '$this->dnr_birth_date',
              `dnr_id` = '$this->dnr_id',
              `dnr_occup_cd` = '$this->dnr_occup_cd',
              `dnr_lang` = '$this->dnr_lang',
              `dnr_remnd_mnth` = '$this->dnr_remnd_mnth',
              `dnr_pattern` = '$this->dnr_pattern',
              `dnr_addr` = '$this->dnr_addr',
              `dnr_post_cd` = '$this->dnr_post_cd',
              `dnr_alt_addr` = '$this->dnr_alt_addr',
              `dnr_cntr_tp` = '$this->dnr_cntr_tp',
              `dnr_first_date` = '$this->dnr_first_date',
              `dnr_first_amnt` = '$this->dnr_first_amnt',
              `dnr_tot_ytd` = '$this->dnr_tot_ytd',
              `dnr_tot_pyr` = '$this->dnr_tot_pyr',
              `dnr_tot_mtd` = '$this->dnr_tot_mtd',
              `dnr_tot_pmt` = '$this->dnr_tot_pmt',
              `dnr_tot` = '$this->dnr_tot',
              `dnr_sower` = '$this->dnr_sower',
              `dnr_thank` = '$this->dnr_thank',
              `dnr_bible_cl` = '$this->dnr_bible_cl',
              `dnr_last_date` = '$this->dnr_last_date',
              `dnr_last_amnt` = '$this->dnr_last_amnt',
              `dnr_next_letter` = '$this->dnr_next_letter',
              `dnr_org_id` = $dnrOrgId,
              `dnr_tax_certf` = '$this->dnr_tax_certf',
              `dnr_deleted` = '$this->dnr_deleted',
              `dnr_del_date` = '$this->dnr_del_date',
              `rep_org_id` = $repOrgId,
              `civ_contact_id` = '$this->civ_contact_id',
              `civ_last_update` = '$this->civ_last_update',
              `dms_preferred_communication_type_id` = '$this->dms_preferred_communication_type_id'
            WHERE
                `dnr_no` = '$this->dnr_no';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_donor` (
              `dnr_name`,
              `dnr_inits`,
              `dnr_title`,
              `dnr_salut`,
              `dnr_formal`,
              `dnr_birth_date`,
              `dnr_id`,
              `dnr_occup_cd`,
              `dnr_lang`,
              `dnr_remnd_mnth`,
              `dnr_pattern`,
              `dnr_addr`,
              `dnr_post_cd`,
              `dnr_alt_addr`,
              `dnr_cntr_tp`,
              `dnr_first_date`,
              `dnr_first_amnt`,
              `dnr_tot_ytd`,
              `dnr_tot_pyr`,
              `dnr_tot_mtd`,
              `dnr_tot_pmt`,
              `dnr_tot`,
              `dnr_sower`,
              `dnr_thank`,
              `dnr_bible_cl`,
              `dnr_last_date`,
              `dnr_last_amnt`,
              `dnr_next_letter`,
              `dnr_org_id`,
              `dnr_tax_certf`,
              `dnr_deleted`,
              `dnr_del_date`,
              `rep_org_id`,
              `civ_contact_id`,
              `civ_last_update`,
              `dms_preferred_communication_type_id`
    ) VALUES (
              '$this->dnr_name',
              '$this->dnr_inits',
              '$this->dnr_title',
              '$this->dnr_salut',
              '$this->dnr_formal',
              '$this->dnr_birth_date',
              '$this->dnr_id',
              '$this->dnr_occup_cd',
              '$this->dnr_lang',
              '$this->dnr_remnd_mnth',
              '$this->dnr_pattern',
              '$this->dnr_addr',
              '$this->dnr_post_cd',
              '$this->dnr_alt_addr',
              '$this->dnr_cntr_tp',
              '$this->dnr_first_date',
              '$this->dnr_first_amnt',
              '$this->dnr_tot_ytd',
              '$this->dnr_tot_pyr',
              '$this->dnr_tot_mtd',
              '$this->dnr_tot_pmt',
              '$this->dnr_tot',
              '$this->dnr_sower',
              '$this->dnr_thank',
              '$this->dnr_bible_cl',
              '$this->dnr_last_date',
              '$this->dnr_last_amnt',
              '$this->dnr_next_letter',
              $dnrOrgId,
              '$this->dnr_tax_certf',
              '$this->dnr_deleted',
              '$this->dnr_del_date',
              $repOrgId,
              '$this->civ_contact_id',
              '$this->civ_last_update',
              '$this->dms_preferred_communication_type_id'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->dnr_no = (empty($this->dnr_no)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->dnr_no;
	
}

# end class	
}

?>
