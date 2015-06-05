<?php

Class mergerecord { 
    
    var $xmlFields;
    protected $contact;
    protected $primaryEmail;
    protected $cellphoneDeets;
    protected $primaryAddress;
    protected $ackPreferences;
    
    function __construct() {
        $this->xmlFields = new SimpleXMLElement('inc/merge.fields.xml', null, true);
        if ($this->xmlFields->field->count()>0) foreach ($this->xmlFields->field as $m) $this->$m['id']='';
    }

    function getMergeRecordHeadings(){
        $e = array();
        if ($this->xmlFields->field->count()>0) foreach ($this->xmlFields->field as $m) $e[(string)$m['label']] = '';
        return $e;
    }
    
    function setLanguage() {
        if (empty($this->contactId)) return false;
        $lang = $GLOBALS['xmlConfig']->acknowledgementconfig->languages->language;
        foreach ($lang as $l) {
            if ((string)$l['value']==$this->contact['preferred_language']) $this->language = (string)$l['shortdesc'];
        }
        if (empty($this->language)) $this->language = 'eng';
        return true;
    }
    
    function setSalutation() {
        if (empty($this->contact['postal_greeting_display'])) {
            if (empty($this->language)) $this->language = 'eng';
            $lang = $GLOBALS['xmlConfig']->acknowledgementconfig->languages->language;
            foreach ($lang as $l) {
                if ((string)$l['shortdesc']==$this->language) {
                    $s = $l['salutation'];
                    foreach ($this->xmlFields->field as $m) {
                        $token = '/##' . $m['id'] . '##/';
                        if (preg_match($token,$s)) {
                            $find[] = $token;
                            $replace[] = $this->$m['id'];
                        }
                    }
                    $this->salutation = preg_replace($find, $replace, $s);
                }
            }
        } else {
            $this->salutation = $this->contact['postal_greeting_display'];
        }
        return true;
    }
    
    function setExtraSalutation() {
        if (empty($this->contactId)||empty($this->organisationId)||empty($this->category)) return false;
        
        $dept = substr($this->organisationId,0,1);
        $den = substr($this->organisationId,1,2);
        $cat = $this->category;
        $lang = (empty($this->contact['preferred_language'])) ? 'en_ZA':$this->contact['preferred_language'];
        
        $sql = "SELECT sal_type, sal_text FROM dms_salutations WHERE sal_department_id = '$dept' AND sal_denomination_id = '$den' AND sal_category_id = $cat AND sal_language = '$lang'";
        $result = $GLOBALS['db']->select($sql); 
        
        if (!$result) {
            return false;
        } else {
            $salutationConfig = $GLOBALS['xmlConfig']->acknowledgementconfig->salutations->type;
            foreach ($result as $k=>$v) {
                foreach ($salutationConfig as $s) if ((string)$s['value']==$v['sal_type']) $this->$s['id'] = $v['sal_text'];
            }
        }
        return true;
    }
    
    function setPrimaryDetails() {
        if (empty($this->contactId)) return false;
        $this->primaryEmail = $GLOBALS['functions']->getCiviPrimaryEmail($this->contactId);
        $this->cellphoneDeets = $GLOBALS['functions']->getCiviCellPhone($this->contactId);
        $this->primaryAddress = $GLOBALS['functions']->getCiviPrimaryAddress($this->contactId);
        
        $this->emailToName = (!$this->primaryEmail) ? '' : trim($this->contact['first_name']) . ' ' . trim($this->contact['last_name']);
        $this->emailAddress = (!$this->primaryEmail) ? '' : $this->primaryEmail['email'];
        
        $this->addressLine1 = trim($this->primaryAddress['street_address']);
        $this->addressLine2 = trim($this->primaryAddress['supplemental_address_1']);
        $this->addressLine3 = trim($this->primaryAddress['supplemental_address_2']);
        $this->town = trim($this->primaryAddress['city']);
        $this->postalCode = str_pad($this->primaryAddress['postal_code'], 4, '0',STR_PAD_LEFT);
        
        $this->cellphone = (!$this->cellphoneDeets) ? '' : $this->cellphoneDeets['phone'];
        return true;
    }
    
    function setAcknowledgementPreferences() {
        if (empty($this->contactId)) return false;
        $this->ackPreferences = new acknowledgementpreferences();
        $this->ackPreferences->LoadByContactId($this->contactId);
        $this->preferredCommunicationMethod = $this->ackPreferences->apr_preferred_method;
        $this->unacknowledgedTotal = "R ".number_format($this->ackPreferences->apr_unacknowledged_total,2,'.',' ');
        return true;
    }
    
    function setLastContribution() {
        if (empty($this->contactId)) return false;
        $this->ackPreferences = new acknowledgementpreferences();
        $this->ackPreferences->LoadByContactId($this->contactId);
        
        $parms['version'] = 3;
        $parms['sequential'] = 1;
        $parms['return'] = "receive_date,total_amount";
        $parms['contact_id'] = $this->contactId;
        $parms['options'] = array('limit' => 1, 'sort' => "receive_date DESC");
        $result = civicrm_api('Contribution', 'get', $parms);
        
        if (!empty($result['id'])&&$result['is_error']===0) {
            $this->contributionDate = date("d/m/Y",strtotime($result['values'][0]['receive_date']));
            $this->contributionAmount = "R ".number_format($result['values'][0]['total_amount'],2,'.',' ');
        }
        return true;
        
    }
    
    function setContact() {
        if (empty($this->contactId)) return false;
        $this->contact = $GLOBALS['functions']->getCiviContactByQuery($this->contactId);
        $this->donorNo = $this->contact['external_identifier'];
        $this->displayName = trim($this->contact['display_name']);
        $this->title = trim($this->contact['title']);
        $this->firstName = trim($this->contact['first_name']);
        $this->middleName = trim($this->contact['middle_name']);
        $this->lastName = trim($this->contact['last_name']);
        $this->dob = $this->contact['birth_date'];
        $this->birthday = date("Y-m-d",strtotime(date("Y") . substr($this->contact['birth_date'],-6)));
        return true;
    }
    
    function setInitials() {
        if (empty($this->contactId)) return false;
        $this->initials = trim(ucfirst(substr($this->contact['first_name'],0,1)));
        $this->initials .= (!empty($this->initials)) ? ' ':''; 
        $this->initials .= trim(ucfirst(substr($this->contact['middle_name'],0,1)));
        $this->initials = trim($this->initials);
        return true;
    }
    
    function setCommuncationMethod() {
        if (empty($this->contactId)) return false;
        $groupId = (int)$GLOBALS['xmlConfig']->defaultPreferences->communicationMethodCiviOptGroupId;
        $methods = $GLOBALS['functions']->getCiviOptionValues($groupId);
        foreach ($methods as $m) {
            $p = preg_replace("/[^0-9]/","",$this->contact['preferred_communication_method']);
            if ($m['value']==$p) {
                $this->preferredCommunicationMethod = $m['label'];
            }
        }
        return true;
    }
    
    function setRegion() {
        if (empty($this->contactId)) return false;
        $r = new civicrm_dms_organisation;
        $r->LoadByOrgId($this->contact['organisation_id']);
        $this->region = $r->org_region;
        return true;
    }
    
    function setTotals() {
        if (empty($this->contactId)) return false;
        $sql = "SELECT COUNT(*) `cnt`, SUM(`total_amount`) `total` FROM civicrm_contribution where `contact_id` = $this->contactId";
        $result = $GLOBALS['civiDb']->select($sql); 
        if (!$result) {
            return false;
        } else {
            $this->trxnCnt = $result[0]['cnt'];
            $this->totalContributions = $result[0]['total'];
        }
        return true;
    }
    
    function loadByTrnsId($trnsId) {
        
        # Transaction Record
        $t = new transaction();
        if (!$t->Load($trnsId)) return false;
        
        $this->contactId = $t->civ_contact_id;
        $this->setContact();
        $this->donorNo = $t->trns_dnr_no;
        
        # Reporting Detail
        $this->department = substr($t->trns_organisation_id,0,1);
        $this->organisationId = $t->trns_organisation_id;
        $this->region = $t->trns_region_id;
        $this->category = $t->trns_category_id;
        
        # Contribution Detail
        $this->receiptNo = $t->trns_receipt_no;
        $this->receiptDocType = $t->trns_receipt_type;
        $this->contributionDate = date("d/m/Y",strtotime($t->trns_date_received));
        $this->contributionAmount = "R ".number_format($t->trns_amount_received,2,'.',' ');
        $this->newDonor = $t->dms_is_first_trxn;
        $this->mtdTrxnNo = $t->dms_mtd_trxn_count;
        $this->alternateReceiptNo = $this->department.'/'.$this->receiptNo;
        $this->contribution_id = $t->civ_contribution_id;
        
        $this->setAcknowledgementPreferences();
        $this->setPrimaryDetails();
        $this->setLanguage();
        $this->setSalutation();
        $this->setInitials();
        $this->setTotals();
        $this->setExtraSalutation();
        
        return true;
    }
        
    function LoadByContactId($contactId) {
        $this->contactId = $contactId;
        if (!$this->setContact()) return false;
        
        $this->department = substr($this->contact['organisation_id'],0,1);
        $this->organisationId = $this->contact['organisation_id'];
        
        $this->category = $this->contact['category_id'];

        $this->setPrimaryDetails();
        $this->setCommuncationMethod();
        $this->setLanguage();
        $this->setSalutation();
        $this->setInitials();
        $this->setRegion();
        $this->setTotals();
        $this->setExtraSalutation();
        $this->setLastContribution();
        return true;
    }
    
}

?>