<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_acknowledgement_preferences.php");
Class civicrm_dms_acknowledgement_preferences_extension extends civicrm_dms_acknowledgement_preferences { 
    
    function __construct($contactId = null) {
        if (!is_null($contactId)) $this->LoadByContactId($contactId);
    }
    
    function newDefault($contactId) {
        $this->contact_id = $contactId;
        if (empty($this->contact_id)) return false;        
        
        $defaults = simplexml_load_file("/var/www/joomla/dms/contacts/inc/config.xml");
        foreach ($defaults->defaults->acknowledgementPreferences->children() as $k=>$v) {
            $key = (string)$k;
            $value = (string)$v;
            $this->$key = $value;
        }
    }

    function LoadByContactId($contactId) {
        $sql = "SELECT * FROM `civicrm_dms_acknowledgement_preferences` where `contact_id` = $contactId";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->newDefault($contactId);
            return false;
        } else {
            foreach ($result[0] as $k => $v) $this->$k = stripslashes($v);
            return true;
        }
    }

    function getTotalContributions() {
        $sql = "SELECT 
	SUM(`total_amount`) `total` 
FROM civicrm_contribution
WHERE contact_id = $this->contact_id AND `id` > $this->last_acknowledgement_contribution_id";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
                return false;
        } else {
                return $result[0]['total'];
        }
    }

    function setUnacknowledgedTotal() {
        $sql = "UPDATE civicrm_dms_acknowledgement_preferences P
INNER JOIN 
	(SELECT contact_id,
	SUM(`total_amount`) `total` 
FROM civicrm_contribution
WHERE contact_id = $this->contact_id AND `id` > $this->last_acknowledgement_contribution_id GROUP BY contact_id) `tmp` 
	ON `tmp`.`contact_id` = P.contact_id 
SET unacknowledged_total = total 
WHERE P.contact_id = $this->contact_id";
        $result = $GLOBALS['civiDb']->execute($sql);
    }

    function isTimeForAcknowledgment() {
        if (empty($this->last_acknowledgement_date)) $this->last_acknowledgement_date = $this->last_contribution_date;
        $daysToAdd = $this->frequency*31;
        $adjustedDate = new Datetime(date("Y-m-d",strtotime($this->last_acknowledgement_date . ' + ' . $daysToAdd)));
        $today = new Datetime();
        return ($today>=$adjustedDate);
    }
}