<?php

include_once("/var/www/joomla/dms/inc/db-classes/class.civicrm_dms_acknowledgement.php");
include_once("/var/www/joomla/dms/inc/db-class-extensions/class.civicrm_dms_acknowledgement_preferences.extension.php");

Class civicrm_dms_acknowledgement_extension extends civicrm_dms_acknowledgement { 
    
    function __construct($contributionId = null) {
        if (!is_null($contributionId)) $this->LoadByContributionId($contributionId);
    }
    
    function LoadByContributionId($contributionId) {
        $sql = "SELECT * FROM `civicrm_dms_acknowledgement` where `contribution_id` = $contributionId";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            $this->contribution_id = $contributionId;
            return false;
        } else {
            foreach ($result[0] as $k => $v) $this->$k = stripslashes($v);
            return true;
        }
    }

    function mustContributionBeAcknowledged() {
        $adate = strtotime($this->acknowledgement_datetime);
        if (!empty($adate)) return false;
        if (!empty($this->contribution_id)) {
            # double check that contribution was not acknowledged yet
            $newAck = new civicrm_dms_acknowledgement_extension($this->contribution_id);
            $adate = strtotime($newAck->acknowledgement_datetime);
            if (!empty($adate)) return false;
            
            # check acknowledgement preferences
            $parms['version'] = 3;
            $parms['id'] = $this->contribution_id;
            
            $c = civicrm_api('contribution','get',$parms);
            print json_encode($parms);
                exit();
            if ($c['count']>0) {
                
                $contactId = $c['values'][$c['id']]['contact_id'];
                $a = new civicrm_dms_acknowledgement_preferences_extension($contactId);
                $mustAcknowledge = ($a->must_acknowledge=='Y');
                $isTime = $a->isTimeForAcknowledgment();
                $isBam = functions::isCiviBamMember($contactId);
                if ($mustAcknowledge&&$isTime&&!$isBam) {
                    return true;
                    $a->setUnacknowledgedTotal();
                }
                return false;
            }
        }
        return false;
        
    }

}