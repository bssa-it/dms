<?php

Class functions { 
        
    function test() {
        /**
         * TEST FUNCTION JUST TO MAKE SURE DATABASE CONNECTION IS OK
         *
         * @param  none
         * @throws none
         * @return false if failed, array of results
         */
    	$sql = "SELECT * FROM `vw_thankyou` ORDER BY `Date Received` DESC LIMIT 10;";
        $this->showSql($sql);
    	$result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    	   return false; 
    	} else {
    		return $result;
    	}
    }
    
    function getMyTemplates(){

        /**
         * This function retrieves all the Mail Merge templates linked to the logged in user.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */        
        $sql = "SELECT *,CASE WHEN `tpl_createdByUserId` = ".$_SESSION['dms_user']['userid']." THEN 'Y' ELSE 'N' END ";
        $sql .= "`canUpdate` FROM `dms_template` WHERE `tpl_accessLevel` = 'system' or `tpl_createdByUserId` = ";
        $sql .= $_SESSION['dms_user']['userid']." ORDER BY `tpl_accessLevel`,`tpl_name`;";
        $this->showSql($sql);
    	$result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    	   return false; 
    	} else {
    		return $result;
    	}
    }
    
    function createMenu() {
        
        /**
         * This function creates a menu based on the logged in user's permissions 
         *
         * @param  none
         * @throws none
         * @return false on error;  an HTML menu for the logged in user. 
         */
        $sql = "SELECT * FROM `dms_menu` WHERE `men_active` = 'Y' ORDER BY `men_level`,`men_parent`,`men_order`;";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    	   return false; 
    	} else {
   		   $return = '<div id="navigationDiv">';
           $return .= "\n\t" . '<ul id="navigator">';
           foreach ($result as $m) {
                if ($_SESSION['dms_user']['authorisation']->$m['men_requiredAuthorisation']&&$m['men_level']==0) {
                    $menuName = strtolower(substr($m['men_name'],0,1)).preg_replace('/ /','',substr($m['men_name'],1)).'Menu';
                    $subMenuName = strtolower(substr($m['men_name'],0,1)).preg_replace('/ /','',substr($m['men_name'],1)).'SubMenu';
                    $return .= "\n\t\t" . '<li';
                    $subMenu = null;
                    foreach ($result as $s) {
                        $authOk = $_SESSION['dms_user']['authorisation']->$s['men_requiredAuthorisation'];
                        if ($authOk&&$s['men_level']==1&&$s['men_parent']==$m['men_id']) {
                            if (is_null($subMenu)) {
                                $subMenu = ' onmouseover="' . "showSubMenu('$menuName','$subMenuName');";
                                $subMenu .= '" onmouseout="' . "hideSubMenu('$subMenuName');" . '" id="'.$menuName.'">';
                                $subMenu .= $m['men_name']."\n\t\t".'<div class="submenu" id="'.$subMenuName.'"><ul>';   
                            }
                            $subMenu .= "\n\t\t\t".'<li style="padding-right: 0px;"><a href="'.$s['men_destination'].'">'.$s['men_name'].'</a></li>'; 
                        }
                    }
                    $return .= (is_null($subMenu)) ? '><a href="'.$m['men_destination'].'">'.$m['men_name']."</a>\n\t\t</li>" : "$subMenu\n\t\t</ul></div>\n\t\t</li>";
                } 
           }
           $return .= "\n\t</ul>\n</div>";
           
           /*  PERSONAL SETTINGS LINK */
           $return .= "\n\t" . '<img src="/dms/img/logout.png" title="log out" align="middle" id="imgLogout" /><div id="myConfigurationDiv"><img src="/dms/img/settings.png" width="16" height="16" alt="my configuration" /> '.$_SESSION['dms_user']['fullname'].'</div><div id="notificationLinkDiv"><img src="/dms/img/notification.png" width="16" height="16" /></div>';
           
           return $return;
    	}
    }
    
    function createFooter($dnrNo=null) {
        $sql = (!empty($_SESSION['dms_user']['sql'])) ? $_SESSION['dms_user']['sql'] : '';
        $_SESSION['dms_user']['sql'] = '';
        $showSearch = (!empty($_SESSION['dmsDonorSearchResultset'])) ? 'srchRed' : 'searchBlue';
        
        $showNextPrev = 'style="display:none"';
        $marginLeft = 'style="margin-left:58px"';
        $next = $prev = '';
        $showNext = $showPrev = false;
        if (!empty($dnrNo)){
            $showNextPrev = (!empty($_SESSION['dmsDonorSearchResultset'])&&count($_SESSION['dmsDonorSearchResultset'])>1) ? '':'style="display:none"';
            $marginLeft = (empty($_SESSION['dmsDonorSearchResultset'])||count($_SESSION['dmsDonorSearchResultset'])==1) ? 'style="margin-left:58px"':'';
            $next = $this->getNextContact($dnrNo);
            $prev = $this->getPrevContact($dnrNo);
        
            $showNext = (empty($next)) ? '':'next';
            $showPrev = (empty($prev)) ? '':'previous';
            $marginLeft = (empty($next)&&empty($prev)) ? 'style="margin-left:58px"':'';
            $showNextPrev = (empty($next)&&empty($prev)) ? 'style="display:none"':'';
        }
        
        return '
            <div id="permanentSearchDiv">
                <div class="circle" id="'.$showSearch.'"><img src="/dms/img/search-white-32.png" id="imgUserSearch" title="Search Results" /></div>
                <div id="seshNextPrevDiv" '.$showNextPrev.'>
                    <div id="divPrevRecord" d="'.$prev.'">'.$showPrev.'</div>
                    <div id="divNextRecord" d="'.$next.'">'.$showNext.'</div>
                </div>
                <div id="frmQuickSearchDiv">
                    <form name="frmQuickSearch" id="frmQuickSearch" action="/dms/contacts/find.contact.php" method="POST" onsubmit="return validateQuickSearch()" />
                        <input type="hidden" name="srch_database" value="'.$_SESSION['dms_user']['config']['donorSearch']['defaultDatabase'].'" />
                        <input type="hidden" name="srch_bamOnly" value="'.$_SESSION['dms_user']['config']['donorSearch']['defaultBamOnly'].'" />
                        <input type="hidden" name="srch_donorDeleted" value="'.$_SESSION['dms_user']['config']['donorSearch']['defaultDonorDeleted'].'" />
                        <input type="text" class="inp" name="qck_search" id="qck_search" placeholder="quick search" '.$marginLeft.' />
                    </form>
                </div>
            </div>
            <div id="titleDiv">
                <img src="/dms/img/sower-white-bg.png" height="24" width="24" style="vertical-align: middle;" /> Donor Management System
                v '.$GLOBALS['currentDmsVersion'].'
                <img src="/dms/img/civi-logo.png" height="24" width="24" style="vertical-align: middle;" /> CiviCRM 
                v '.$GLOBALS['currentCivicrmVersion'].'
            </div>
            <div id="rightInnerFoooterDiv">&nbsp;</div>' . $sql;
    }
    
    function contactUserEditHtml() {
        $sql = (!empty($_SESSION['dms_user']['sql'])) ? $_SESSION['dms_user']['sql'] : '';
        $_SESSION['dms_user']['sql'] = '';
        return '<div id="titleDiv">
                <img src="/dms/img/sower-white-bg.png" height="24" width="24" style="vertical-align: middle;" /> Donor Management System
                v '.$GLOBALS['currentDmsVersion'].'
                <img src="/dms/img/civi-logo.png" height="24" width="24" style="vertical-align: middle;" /> CiviCRM 
                v '.$GLOBALS['currentCivicrmVersion'].'
            </div>' . $sql;
    }
    
    function cleanContactNumber($contactNumber){
        
        /**
         * This function removes any non-numeric characters from a phone number.
         *
         * @param  unsanitized phone number string
         * @throws none
         * @return a numeric string
         */
        $return = trim($contactNumber);
        $patterns = array('/ /','/-/','/27\(0\)/','/\+/','/\(/','/\)/');
        $replacements = array('','','0','','','');
        $return = preg_replace($patterns,$replacements,$return);
        if (substr($return,0,2)=='27') $return = substr($return,2);
        return $return;
    }
    
    function goToIndexPage() {
        
        /**
         * This function redirects he current user to the dashboard of the DMS.
         *
         * @param  none
         * @throws none
         * @return none
         */
        header('location:/index.php');
        exit();  
    }
    
    function getDonorsLanguageFromCivi($civ_contact_id) {
        
        /**
         * This function retrieves the specified contact's preferred language from the CiviCRM database.
         *
         * @param  $civ_contact_id - id field of the civicrm_contact table for the contact 
         * @throws none
         * @return false on error;  (string) the contact's preferred language.
         */
        $langOptGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->languageCiviOptGroupId;
        $sql = "SELECT `preferred_language`,`label` `language` FROM `civicrm_contact` 
INNER JOIN `civicrm_option_value` ON option_group_id = $langOptGroupId AND `name` = `preferred_language`
WHERE `civicrm_contact`.`id` = $civ_contact_id;";
        $db                     = new database;
        $civiDbConfig           = $GLOBALS['civiDBConnectionDetails'];
        $db->username           = (string)$civiDbConfig->username;
        $db->password           = (string)$civiDbConfig->password;
        $db->host               = (string)$civiDbConfig->host;
        $db->database           = (string)$civiDbConfig->database;
        $db->connect(true);
        $this->showSql($sql);
        $result = $db->select($sql);
        $db->disconnect();
        
    	if (!$result) {
    		return false;
    	} else {
   		   return $result[0];
    	}
    }
    
    function getPreferredCommunicationMethod($civ_contact_id) {
        
         /**
         * This function retrieves the Communication Preference for a specified contact.
         *
         * @param  $civ_contact_id - id field of the civicrm_contact table for the contact
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the contact's preferred communication method.
         */
        $sql = "SELECT `preferred_communication_method`,`label` `preferred_Method_Description` FROM `civicrm_contact` 
INNER JOIN `civicrm_option_value` ON option_group_id = 1 AND `value` = `preferred_communication_method`
WHERE `civicrm_contact`.`id` = $civ_contact_id;";
        $db                     = new database;
        $civiDbConfig           = $GLOBALS['civiDBConnectionDetails'];
        $db->username           = (string)$civiDbConfig->username;
        $db->password           = (string)$civiDbConfig->password;
        $db->host               = (string)$civiDbConfig->host;
        $db->database           = (string)$civiDbConfig->database;
        $db->connect(true);
        $this->showSql($sql);
        $result = $db->select($sql);
        $db->disconnect();
        
    	if (!$result) {
   		   $return['preferred_Method_Description'] = 'Unknown';
    	} else {
   		   $return = $result[0];
    	}
        return $return;
    }
    
    function getCongregationName($orgId) {
        
        /**
         * This function retrieves the congregation name for the specified organisation Id
         *
         * @param  $orgId - Organisation Id
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the congregation name.
         */
        $sql = "SELECT `con_name` `Congregation` FROM `dms_congregation`
        WHERE `con_org_id` = '" . substr($orgId,1) . "' and `con_dep_id` = '".substr($orgId,0,1)."';";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    	   return 'Unknown'; 
    	} else {
    		return $result[0]['Congregation'];
    	}
    }
    
    function getContributionTypeName($docType) {
        
         /**
         * This function retrieves the contribution type name for the specified contribution type.
         *
         * @param  $docType - Document type code
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the contribution type name.
         */
        $sql = "SELECT `cty_description` `name` FROM `dms_contributionTypes`
        WHERE `cty_id` = '" . $docType . "';";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    	   return 'Unknown'; 
    	} else {
    		return $result[0]['name'];
    	}
    }
    
    function getRegionFromOrgId($orgId) {
        
         /**
         * This function retrieves the region linked to the organisation id.
         *
         * @param  $orgId - organisation id
         * @throws none
         * @return (string) 'Unknown' on error;  (int) the region id.
         */
        $sql = "SELECT `org_subregion` `region` from dms_orgunit where org_id = '$orgId';";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    	   return 'Unknown'; 
    	} else {
    		return $result[0]['region'];
    	}
    }
    
    function GetCategoryName($catId) {
    
        /**
         * This function retrieves the category name for the specified category id.
         *
         * @param  $catId - category id
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the category name.
         */
    	$sql = "SELECT `cat_name` AS `name` FROM `dms_category` WHERE `cat_id` = $catId";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return 'Unknown';
    	} else {
    		return $result[0]['name'];
    	}
    
    }
    
    function GetDenominationName($den_id) {
    	
        /**
         * This function retrieves the denomination name for the specified denomination id.
         *
         * @param  $den_id - denomination id
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the denomination name.
         */
    	$sql = "SELECT `den_name` FROM `dms_denomination` WHERE `den_id` = $den_id";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return 'Unknown';
    	} else {
    		return $result[0]['den_name'];
    	}
    }
    
    function GetSynodName($orgId) {
    	
        /**
         * This function retrieves the Synod name for the specified organisation id.
         *
         * @param  $orgId - organisation id
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the synod name.
         */
    	$sql = "SELECT `syn_name` `Synod` FROM `dms_synod`
        WHERE `syn_dep_id` = '" . substr($orgId,0,1) . "' and `syn_id` = '" . substr($orgId,3,1) . "' and `syn_den_id` = '".substr($orgId,1,2)."';";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return 'Unknown';
    	} else {
    		return $result[0]['Synod'];
    	}
    }
    
    function GetCircuitName($orgId) {
    	
        /**
         * This function retrieves the Circuit name for the specified organisation id.
         *
         * @param  $orgId - organisation id
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the circuit name.
         */
    	$sql = "SELECT `cir_name` `Circuit` FROM `dms_circuit`
        WHERE `cir_dep_id` = '" . substr($orgId,0,1) . "' and `cir_id` = '" . substr($orgId,4,2) . "' and `cir_den_id` = '".substr($orgId,1,2)."' 
        and `cir_syn_id` = '".substr($orgId,3,1)."';";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return 'Unknown';
    	} else {
    		return $result[0]['Circuit'];
    	}
    }
    
    function GetRegionName($regId) {
    	
        /**
         * This function retrieves the region name for the specified region id.
         *
         * @param  $regId - region id
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the region name.
         */
    	$sql = "SELECT `region_name` FROM `dms_region` WHERE `region_id` = $regId";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return 'Unknown';
    	} else {
    		return $result[0]['region_name'];
    	}
    }
    
    function GetDepartmentName($dp) {
    
    	/**
         * This function retrieves the department name for the specified department code.
         *
         * @param  $dp - department code
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the department name.
         */
    	$sql = "SELECT `dep_name` AS `name` FROM `dms_department` WHERE `dep_id` = '$dp'";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return 'Unknown';
    	} else { 
    		return $result[0]['name'];
    	}
    }
    
    function getTransactionsForDonor($dnrId) {
        
        /**
         * This function retrieves the transactions for the specified donor.
         *
         * @param  $dnrId - donor number
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT * FROM `dms_transaction` 
LEFT JOIN `dms_acknowledgement` ON trns_id = ack_trns_id
WHERE `trns_dnr_no` = '$dnrId' 
ORDER BY `trns_date_received` DESC";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return false;
    	} else { 
    		return $result;
    	}
    }
    
    function GetRemarks($dnrNo) {
        
        /**
         * This function retrieves the remarks for the specified donor.
         *
         * @param  $dnrNo - donor number.
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT `drm_entry_date`,`drm_upd_date`,`drm_text` FROM `dms_remark` WHERE `drm_dnr_no` = $dnrNo order by drm_entry_date";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function getAPIContactRecordFromDonorNo($dnrNo) {
        
        /**
         * This function retrieves the civicrm database contact's info, for the specified donor number throught the CiviCRM API.
         *
         * @param  $dnrNo - donor number
         * @throws none
         * @return An array of the resulting dataset.
         */
        $searchParams['external_identifier'] = $dnrNo;
        try {
            $apiRecord = civicrm_api3('Contact', 'Getsingle', $searchParams);
        } catch (Exception $ex) {
            try {
            $searchParams['contact_is_deleted'] = 1;
            $apiRecord = civicrm_api3('Contact', 'Getsingle', $searchParams);
            } catch (Exception $nex) {
                return false;
            }
        }
        return $apiRecord;
        
    }
    
    
    function reSortResultsetArray($sortby,$multiArray) {
        
        /**
         * This function resorts an array .
         *
         * @param  $sortby - what key to sort by
         * @param  $multiArray - 2 dimensional array to be sorted.  
         * @throws none
         * @return the $multiArray array will be sorted and returned.
         */
         
        $tmp = Array(); 
        foreach($multiArray as &$ma) $tmp[] = &$ma[$sortby]; 
        array_multisort($tmp, $multiArray); 
        return $multiArray;
    }
    
    public static function GetDataFromSQL($sql) {
        
        /**
         * This function retrieves a recordset for a specified SQL statement from the dms database.
         *
         * @param  $sql - the SQL statement
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        self::showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    public static function GetCiviDataFromSQL($sql) {
        
        /**
         * This function retrieves a recordset for a specified SQL statement from the civicrm database.
         *
         * @param  $sql - the SQL statement
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $connectionSettings = $GLOBALS['xmlConfig']->civiConnection;
        $db = new database;
        $db->host = (string)$connectionSettings->host;
        $db->username = (string)$connectionSettings->username;
        $db->password = (string)$connectionSettings->password;
        $db->database = (string)$connectionSettings->database;
        $db->connect(true);
        self::showSql($sql);
        $result = $db->select($sql);
        $db->close(); 
        return $result;
    }
    
    function GetUnAcknowledgedContributions($departments,$fDate=null){
        
        /**
         * This function retrieves all unacknowledged contributions from the dms database.
         *
         * @param  $departments - departments that need to be retrieved
         * @param  $fDate - from date
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $bamMembershipTypeId = (int)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;     
        $civiDb = (string)$GLOBALS['xmlConfig']->civiConnection->database;
        $dmsDb = (string)$GLOBALS['xmlConfig']->databaseConnection->database;
        
        #### Get the current financial year
        $year = (date("Y-m-d")>date("Y-11-01")) ? date("Y"):date("Y")-1;
        $fromDate = (is_null($fDate)) ? $year.'-11-01':$fDate;
        
        /* CRITERIA   
        *    This financial year
        *    For user selected departments
        *    Donor's dnr_thank is Y
        *    DMS acknowledged is null and SCO dnr_thank = N 
        ***/
        
        $sql = "
SELECT 
	`trns_id`, 
	`trns_date_received`, 
	`trns_receipt_no`, 
	`trns_receipt_type`, 
	`trns_amount_received`, 
	`trns_dnr_no`, 
	`trns_organisation_id`,
	`trns_category_id`,
	`civ_trxn_id`, 
	`civ_contact_id`, 
	`trns_region_id`, 
	`dms_mtd_trxn_count`,
    `dms_is_first_trxn`,
	`display_name`,
	`preferred_language`,
    apr_preferred_method,
    `trns_motivation_id`
FROM 
	dms_transaction T 
    LEFT JOIN dms_acknowledgementPreferences P ON P.apr_dnr_no = trns_dnr_no 
	INNER JOIN `$civiDb`.`civicrm_contact` C ON C.id = civ_contact_id
WHERE 
	trns_date_received >= '$fromDate' 
	AND SUBSTR(T.`trns_organisation_id`,1,1) IN ($departments) 
    AND T.`dms_must_acknowledge` = 'Y'  
    AND T.`trns_dnr_acknowledged` = 'N'
	AND NOT EXISTS (SELECT ack_trns_id FROM dms_acknowledgement WHERE ack_trns_id = trns_id)
ORDER BY 
	SUBSTR(`trns_organisation_id`,1,1),trns_date_received, trns_receipt_type, trns_receipt_no;";
    
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}

    }
    
    function getMTDTrxnCount($trnsDnrNo,$trnsId) {
        
        /**
         * This function counts the number of contributions made by a specified donor for the current month.
         *
         * @param  $trnsDnrNo - donor number
         * @param  $trnsId - end transaction id
         * @throws none
         * @return false on error;  (int) number of transactions.
         */
        $mtd = date("Y-m-01");
        $sql = "SELECT COUNT(*) AS `trxn_no` FROM dms_transaction
WHERE `trns_date_received` >= '$mtd' AND trns_dnr_no = $trnsDnrNo AND trns_id <= $trnsId";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result[0]['trxn_no'];
    	}
    }
    
    function isFirstTrxn($trnsId) {
        
        /**
         * This function checks if the specified transaction is the first contribution for the donor.
         *
         * @param  $trnsId - transaction id
         * @throws none
         * @return false on error;  (bool) true if first contribution, false if not first contribution.
         */
        $sql = "SELECT
            CASE WHEN D.dnr_first_date = T.trns_date_received THEN 'Y' ELSE 'N' END `first_trxn`
        FROM dms_transaction T 
        INNER JOIN `dms_donor` D ON D.`dnr_no` = T.`trns_dnr_no`
        WHERE trns_id = $trnsId;";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (empty($result)) {
    		return false;
    	} else {
    		return ($result[0]['first_trxn']=='Y');
    	}
    }
    
    function exportArrayToCSV($filename,$array,$immediateDownload=false) {
        
        /**
         * This function creates a csv document for immediate download or storage on the server (based on the specified parameters)
         * from a specified data array.
         *
         * @param  $filename - the filename for the resulting download.
         * @param  $array - the data array to be exported.
         * @param  $immediateDownload - (false by default)  if true the document will be downloaded immediately onto the users
         * pc.  if false, the document will be stored on the server.
         * @throws none
         * @return no return.
         */
        if (!$immediateDownload) {
            $output = fopen($filename, 'w');
            $headings = array();
            foreach ($array[0] as $h=>$v) $headings[] = $h; 
            fputcsv($output, $headings);
            foreach ($array as $k=>$v) fputcsv($output,$v);
        } else {
            $t = $array;
        	$excelrow = '';
        	foreach ($t[0] as $k =>$v) {
        		$hdrs[] = $k;
        		$excelrow .= $k . ",";
        	}
        	$excelrow .= "\n";
        
        	foreach ($t as $k) {
        		foreach ($hdrs as $h) $excelrow .= '"' . $k[$h] . '"' . ",";
        		$excelrow .= "\n";
        	}
            //header("Content-type: application/csv"); 
                header("Content-Type:text/plain");
        	header('Content-Disposition: attachment; filename='.$filename);
        	header("Pragma: no-cache"); 
        	header("Expires: 0");
        	echo $excelrow; 
        }
    }
    
    function GetMailMergeFilenames() {
        
        /**
         * This function retrieves mail merge files created by the logged in user.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $userId = (isset($_SESSION['dms_user']['userid'])) ? ' AND `ack_usr_id` = '.$_SESSION['dms_user']['userid']:'';
        $sql = "SELECT `ack_document`,`ack_usr_id`,COUNT(*) `row count`, MAX(`ack_date`) `ack_date` FROM `dms_acknowledgement` 
        WHERE ack_method in ('export','consolidated')$userId GROUP BY 1 ORDER BY 4 DESC";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function GetAcknowledgmentsForCiviContribution($contributionId) {
        
        /**
         * This function retrieves the acknowledgements for a civicrm contribution id.
         *
         * @param  $contributionId - civicrm contribution id
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT * FROM `dms_acknowledgement` WHERE ack_civi_con_id = $contributionId";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
        
    }
    
    function GetJoomlaUserDetails($userId){
        
        /**
         * This function retrieves user details from the Joomla database.
         *
         * @param  $userId - user id
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT `name`,`username`,`email` FROM `r25_users` WHERE `id` = $userId;";
        $db                     = new database;
        $civiDbConfig           = $GLOBALS['joomlaDBConnectionDetails'];
        $db->username           = (string)$civiDbConfig->username;
        $db->password           = (string)$civiDbConfig->password;
        $db->host               = (string)$civiDbConfig->host;
        $db->database           = (string)$civiDbConfig->database;
        $db->connect(true);
        $this->showSql($sql);
        $result = $db->select($sql);
        $db->disconnect();
        if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
        
    }
    
    function getJoomlaUserGroups($jmlConnectionDetails){
        
        /**
         * This function retrieves the Joomla user groups the user is subscribed to.
         *
         * @param  $jmlConnectionDetails - Joomla database connection details
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $dbJoomla                       = new database();
        $dbJoomla->host                 = (string)$jmlConnectionDetails->host;
        $dbJoomla->password             = (string)$jmlConnectionDetails->password;
        $dbJoomla->username             = (string)$jmlConnectionDetails->username;
        $dbJoomla->database             = (string)$jmlConnectionDetails->database;
        $dbJoomla->connect();
        
        $sql = "SELECT id,title FROM r25_usergroups";
        $result = $dbJoomla->select($sql);
        if (!$result) {
    		return array();
    	} else {
    		return $result;
    	} 
    }
    
    function getJoomlaUsers($jmlConnectionDetails,$selectGroup){
        
        /**
         * This function retrieves the Joomla user groups the user is subscribed to.
         *
         * @param  $jmlConnectionDetails - Joomla database connection details
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $dbJoomla                       = new database();
        $dbJoomla->host                 = (string)$jmlConnectionDetails->host;
        $dbJoomla->password             = (string)$jmlConnectionDetails->password;
        $dbJoomla->username             = (string)$jmlConnectionDetails->username;
        $dbJoomla->database             = (string)$jmlConnectionDetails->database;
        $dbJoomla->connect();
        
        $sql = "SELECT DISTINCT `id`,`name`,`username` FROM `r25_users` `U`
INNER JOIN `r25_user_usergroup_map` `UG` ON UG.user_id = U.`id`
WHERE UG.`group_id` = $selectGroup
ORDER BY `name`";
        
        $result = $dbJoomla->select($sql);
        if (!$result) {
    		return array();
    	} else {
    		return $result;
    	} 
    }
    
    function getDonorImportStats(){
        
        /**
         * This function retrieves the current donor import status.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT 
	SUM(CASE WHEN `civ_last_update` IS NULL THEN 0 ELSE 1 END) `civi-id-cnt`,
	COUNT(*) `total`,
	SUM(CASE WHEN `civ_last_update` IS NULL THEN 0 ELSE 1 END)/COUNT(*)*100 `percentage`
FROM `dms_donor` 
WHERE ((`dnr_tax_certf` <> 'Z') AND (`dnr_last_date` > '1999-10-31'))";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function getContributionImportStats(){
        
        /**
         * This function retrieves the current contribution import status.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT 
	SUM(CASE WHEN `civ_contribution_id` IS NOT NULL THEN 1 ELSE 0 END) `inserted`,
	COUNT(*) `total`,
	SUM(CASE WHEN `civ_contribution_id` IS NOT NULL THEN 1 ELSE 0 END)/COUNT(*)*100 `percentage`
 FROM `dms_transaction` `T`
 INNER JOIN `dms_donor` `D` ON trns_dnr_no = dnr_no
 WHERE `D`.`civ_contact_id` IS NOT NULL";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function GetRegionList() {
        
        /**
         * This function retrieves all the regions in the dms_region table.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT * FROM `dms_region` ORDER BY `region_id`";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function GetDepartmentList() {
        
        /**
         * This function retrieves all the departments in the dms_department table.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
    	$sql = "SELECT * FROM `civicrm_dms_department` ORDER BY `dep_id`";
    	$this->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function getBudgets()
    {
        /**
         * This function retrieves all the budgets
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT bud_id,bud_region,region_name,bud_department,dep_name,bud_category,cat_name,bud_amount,dep_chartColor FROM `dms_budget` 
    INNER JOIN `dms_region` ON region_id = bud_region
    INNER JOIN `dms_department` ON dep_id = bud_department
    INNER JOIN `dms_category` ON cat_id = bud_category
    ORDER BY bud_department,bud_region,bud_category";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result || is_null($result)) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function GetDenominationList() {
    	
        /**
         * This function retrieves all the denominations in the dms_denomination table.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
    	$sql = "SELECT * FROM `dms_denomination` ORDER BY `den_id`";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function GetCategoryList($isForBudget=false,$categoryGroupFor=null) {
    	
        /**
         * This function retrieves all the categories in the dms_category table.
         *
         * @param  $isForBudget (default = false) - this parameter removes certain categories from the resultset if true.
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $where = ($isForBudget) ? 'WHERE `cat_id` not between 2000 and 4999 and `cat_id` not in (0,1000,2000,3000,4000,5000,6000,7000,8000,9000,9001)' : '';
    	$sql = "SELECT * FROM `civicrm_dms_category` $where ORDER BY `cat_id`";
    	$this->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function getRandomColorString(){
        
        /**
         * This function creates a random RGB color for HTML display.
         *
         * @param  none
         * @throws none
         * @return (string) RGB color string.
         */
        $r = rand(0,255);
        $g = rand(0,255);
        $b = rand(0,255);
        
        return $r.','.$g.','.$b;
    }
    
    function isNationalDepartment($dep_id) {
      	
        /**
         * This function checks if a department is a national department.
         *
         * @param  $dep_id - department code
         * @throws none
         * @return false on error;  (bool)  true if national, false if regional only.
         */
    	$sql = "SELECT `dep_isNational` FROM `dms_department` WHERE `dep_id` = '$dep_id'";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return ($result[0]['dep_isNational']=='Y');
    	}  
    }
    
    function getMotivationCodeDescription($motivationCode) {
        
        /**
         * This function retrieves the motivation code's description for the specified motivation code.
         *
         * @param  $motivationCode - motivation code
         * @throws none
         * @return (string) 'Unknown' on error;  (string) the motivation code description.
         */
        $sql = "SELECT mot_name FROM `dms_motivationCodes` WHERE mot_id = $motivationCode";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (empty($result)||!$result) {
    		return 'Unknown';
    	} else {
    		return $result[0]['mot_name'];
    	}
    }
    
    function getContributionsFromMergeFilename($filename) {
        
        /**
         * This function retrieves all the transaction ids linked to the specified mail merge filename.
         *
         * @param  $filename - mail merge filename
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
        $sql = "SELECT `trns_id`
FROM dms_acknowledgement
INNER JOIN dms_transaction ON trns_id = ack_trns_id
WHERE `ack_document` = '$filename'";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (empty($result)||!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function getRealIpAddr()
    {
        /**
         * This function returns the IP address of the client connected to the DMS.
         *
         * @param  none
         * @throws none
         * @return (string)  IP address
         */
         
        # check ip from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        { $ip=$_SERVER['HTTP_CLIENT_IP']; }
        # to check ip is pass from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        { $ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
        else 
        { $ip=$_SERVER['REMOTE_ADDR']; }
        
        # return the IP address
        return $ip;
    }
    
    ####  V3.0.2  Updates ####
    
    function calculate_age($fromDate,$toDate=null)
    {
        $today = (is_null($toDate)) ? new DateTime():new DateTime($toDate);
        $diff = $today->diff(new DateTime($fromDate));
        return $diff;
    }
    
    function getCiviContactTypes()
    {
        /**
         * This function returns the contact types from CiviCRM.
         *
         * @param  none
         * @throws none
         * @return (array)  Contact Types; (bool) false on error
         */
         
        $params['version'] = 3;
        $results = civicrm_api('ContactType','get',$params);
        return ($results['is_error']===1) ? false :$results['values'];
         
    }
    
    function getCiviOptionValues($optGroupId)
    {
        /**
         * This function returns values for an option group from the CiviCRM database
         *
         * @param  $optGroupId - option group id
         * @throws none
         * @return (array)  Option Group Values; (bool) false on error
         */
         
        $params['version'] = 3;
        $params['option_group_id'] = $optGroupId;
        $params['options']['limit'] = 10000;
        $params['options']['sort'] = 'label';
        $params['is_active'] = '1';
        $results = civicrm_api('OptionValue','get',$params);

        return ($results['is_error']===1) ? false :$results['values'];
         
    }
    
    function getCiviContactCustomValues($contactId)
    {
        /**
         * This function returns the custom values for a contact record in the CiviCRM database.
         *
         * @param  $contactId - civicrm contact id
         * @throws none
         * @return (array)  Custom Values; (bool) false on error
         */
         
        $params['version'] = 3;
        $params['entity_id'] = $contactId;
        $results = civicrm_api('CustomValue','get',$params);

        return ($results['is_error']===1) ? false :$results['values'];
         
    }
    
    function getDonorsEditDetailsFromCivi($civ_contact_id) {
        
        /**
         * This function retrieves the specified contact's preffered language from the CiviCRM database.
         *
         * @param  $civ_contact_id - id field of the civicrm_contact table for the contact 
         * @throws none
         * @return false on error;  (string) the contact's preferred language.
         */
        $apiParams['version'] = 3;
        $apiParams['id'] = $civ_contact_id;
        $apiParams['sequential'] = 1;
        $apiParams['return'] = "custom_20,custom_19,created_date,modified_date";
        $result = civicrm_api('Contact','get',$apiParams);
        
        $userParams['version'] = 3;
        $userParams['sequential'] = 1;
        $userParams['return'] = "display_name";
        
        $html = '';
        
        if (empty($result['is_error'])&&count($result['values'])) {
            $editDetails = $modifiedDetail = $created_usr_name = $modified_usr_name = '';
            $createdDetail = '&nbsp;';
            
            # created user details
            if (!empty($result['values'][0]['custom_19'])) {
                $userParams['id'] = $result['values'][0]['custom_19'];
                $user = civicrm_api('Contact','getsingle',$userParams);
                if (empty($user['is_error'])) $created_usr_name = $user['display_name'];
            }
            if (!empty($result['values'][0]['created_date'])) {
                $createdDetail = 'Created ' . $result['values'][0]['created_date'];
                if (!empty($result['values'][0]['custom_19'])) $createdDetail .= ' by '. $created_usr_name;
            }
            
            # modified user detail
            if (!empty($result['values'][0]['custom_20'])) {
                $userParams['id'] = $result['values'][0]['custom_20'];
                $user = civicrm_api('Contact','getsingle',$userParams);
                if (empty($user['is_error'])) $modified_usr_name = $user['display_name'];
            }
            if (!empty($result['values'][0]['modified_date'])) {
                $modifiedDetail = 'Last modified ' . $result['values'][0]['modified_date'];
                if (!empty($result['values'][0]['custom_20'])) $modifiedDetail .= ' by '. $modified_usr_name;
            }
            
            $html = '
                    <div style="float:left;" id="createdUserId">'.$createdDetail.'</div>
            <div style="float: right;margin-right: 5px;" id="lastEditUserId">'.$modifiedDetail.'</div>
            </div>';
            
        }
        
        return $html;
    }
    
    function getFullNameFromCiviContactId($contactId) {
        $insertUser['version'] = 3;
        $insertUser['id'] = $contactId;
        $result = civicrm_api('Contact','Getsingle',$insertUser);
        
        $user = (!empty($result['is_error'])) ? 'Unknown':$result['first_name'].' '.$result['last_name'];
        return $user;
    }
    
    function getCiviContactPhoneNos($contactId) {
        $searchPhoneParams['version'] = 3;
        $searchPhoneParams['contact_id'] = $contactId;
        $dnrPhoneNos = civicrm_api('Phone', 'get', $searchPhoneParams);
        $return = array();
        if ($dnrPhoneNos['count']>0) {
            foreach ($dnrPhoneNos['values'] as $k=>$p) {
                $phone['id'] = $p['id'];
                $phone['contact_id'] = $p['contact_id'];
                $phone['locationType'] = (empty($p['location_type_id'])) ? 'Unknown' : $this->getCiviLocationType($p['location_type_id']);
                $phone['phoneType'] = (empty($p['phone_type_id'])) ? 'Unknown' : $this->getCiviPhoneType($p['phone_type_id']);
                $phone['phone'] = $p['phone'];
                $phone['isPrimary'] = $p['is_primary'];
                $return[] = $phone;
            }
        }
        return $return;
    }
    
    function getCiviLocationType($typeId) {
        $ltypeParams['version'] = 3;
        $ltypeParams['id'] = $typeId;
        $result = civicrm_api('LocationType', 'getsingle', $ltypeParams);
        return (!empty($result['name'])) ? $result['name']:'Unknown';
    }
    
    function getCiviLocationTypes() {
        $ltypeParams['version'] = 3;
        $result = civicrm_api('LocationType', 'get', $ltypeParams);
        return (!empty($result['values'])) ? $result['values']:array();
    }
    
    function getCiviPhoneType($typeId) {
        $ptypeParams['version'] = 3;
        $ptypeParams['option_group_id'] = (string)$GLOBALS['xmlConfig']->civiOptionGroups->phonetypes;
        $ptypeParams['value'] = $typeId;
        $result = civicrm_api('OptionValue', 'getsingle', $ptypeParams);
        return (empty($result['label'])) ? 'Unknown':$result['label'];
    }
    
    function getCiviContactAddresses($contactId) {
        $searchAddressParams['version'] = 3;
        $searchAddressParams['contact_id'] = $contactId;
        $dnrAddresses = civicrm_api('Address', 'get', $searchAddressParams);
        if ($dnrAddresses['count']>0) {
            foreach ($dnrAddresses['values'] as $k=>$a) {
                $address['id'] = $a['id'];
                $address['contact_id'] = $a['contact_id'];
                $address['locationTypeId'] = $a['location_type_id'];
                $address['locationType'] = (empty($a['location_type_id'])) ? 'Unknown' : $this->getCiviLocationType($a['location_type_id']);
                $address['address1'] = (!empty($a['street_address'])) ? $a['street_address']:''; 
                $address['address2'] = (!empty($a['supplemental_address_1'])) ? $a['supplemental_address_1']:'';
                $address['address3'] = (!empty($a['supplemental_address_2'])) ? $a['supplemental_address_2']:'';
                $address['address4'] = (!empty($a['supplemental_address_3'])) ? $a['supplemental_address_3']:'';
                $address['city'] = (!empty($a['city'])) ? $a['city'] : '';
                $address['postalCode'] = (!empty($a['postal_code'])) ? $a['postal_code']:'';
                $address['isPrimary'] = $a['is_primary'];
                $return[] = $address;
            }
            return $return;
        } else {
            return array();
        }
    }
    
    function getCiviContactEmailAddresses($contactId,$primary=false) {
        $searchEmailParams['version'] = 3;
        $searchEmailParams['contact_id'] = $contactId;
        if ($primary) $searchEmailParams['is_primary'] = 1;
        $dnrEmailAddress = civicrm_api('Email', 'get', $searchEmailParams);
        if ($dnrEmailAddress['count']>0) {
            foreach ($dnrEmailAddress['values'] as $e) {
                $email['id'] = $e['id'];
                $email['contact_id'] = $e['contact_id'];
                $email['locationType'] = (empty($e['location_type_id'])) ? 'Unknown' : $this->getCiviLocationType($e['location_type_id']); 
                $email['emailAddress'] = $e['email'];
                $email['isPrimary'] = $e['is_primary'];
                $result[] = $email;
            }
            return $result;
        } else {
            return array();
        }
    }
       
    function getCiviContactMemberships($contactId) {
        $params['version'] = 3;
        $params['contact_id'] = $contactId;
        $records = civicrm_api('Membership', 'get', $params);
        if ($records['count']>0) {
            $bamMembershipTypeId = (int)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;
            foreach ($records['values'] as $m) {
                $membership['id'] = $m['id'];
                $membership['contact_id'] = $m['contact_id'];
                $membership['membership_type_id'] = $m['membership_type_id'];
                $membership['membership_name'] = $m['membership_name'];
                $membership['join_date'] = $m['join_date'];
                $membership['start_date'] = (!empty($m['start_date'])) ? $m['start_date']:null;
                $membership['end_date'] = (!empty($m['end_date'])) ? $m['end_date']:null;
                $membership['status'] = $this->getCiviMembershipStatusFromId($m['status_id']);
                # custom membership data
                $customIds = array(15,16);
                $customValues = $this->getCiviCustomValues($m['id'],$customIds);
                $membership['bam_ref_no'] = (!empty($customValues)) ? $customValues[15]:'';
                $membership['bam_certificate_printed'] = (!empty($customValues)) ? $customValues[16]:'';
                $membership['isBam'] = ($bamMembershipTypeId==$m['membership_type_id']) ? 'Y':'N';
                $result[] = $membership;
            }
            return $result;
        } else {
            return array();
        }
    }
    
    function getCiviCustomValues($entityId,$arrayCustomValueIds) {
        $params['version'] = 3;
        $params['entity_id'] = $entityId;
        foreach ($arrayCustomValueIds as $c) $params['return.custom_'.$c] = 1;
        $customData = civicrm_api('CustomValue', 'Get', $params);
        if ($customData['count']>0) {
            foreach ($customData['values'] as $k=>$v) $result[$k] = $v[0];
            return $result;
        } else {
            return array();   
        } 
    }
    
    function getCiviMembershipStatusFromId($statusId) {
        $params['version'] = 3;
        $params['id'] = $statusId;
        $result = civicrm_api('MembershipStatus', 'getsingle', $params);
        return (empty($result['label'])) ? 'Unknown':$result['label'];
    }
    
    function getAllCiviMembershipStatuses() {
        $params['version'] = 3;
        $params['is_active'] = '1';
        $result = civicrm_api('MembershipStatus', 'get', $params);
        return (empty($result['values'])) ? array():$result['values'];
    }
    
    function getAllCiviMembershipTypes() {
        $params['version'] = 3;
        $params['is_active'] = '1';
        $result = civicrm_api('MembershipType', 'get', $params);
        return (empty($result['values'])) ? array():$result['values'];
    }
    
    function getCiviMembership($membershipId) {
        $params['version'] = 3;
        $params['id'] = $membershipId;
        $result = civicrm_api('Membership', 'getsingle', $params);
        return $result;
    }
    
    function getCiviMembershipStatus($membership) {
        $sql = "SELECT 
	id `statusId`,
    label,
	start_event,
	CASE 
		WHEN start_event_adjust_unit IS NULL THEN 0 
		ELSE CASE 
			WHEN start_event_adjust_unit = 'year' THEN 365 
			WHEN start_event_adjust_unit = 'month' THEN 30 
			ELSE 1
		      END * start_event_adjust_interval 
	END `start_add_days`,
	end_event,
	CASE 
		WHEN end_event_adjust_unit IS NULL THEN 0 
		ELSE CASE 
			WHEN end_event_adjust_unit = 'year' THEN 365 
			WHEN end_event_adjust_unit = 'month' THEN 30 
			ELSE 1
		      END * end_event_adjust_interval 
	END `end_add_days`
FROM `civicrm_membership_status` WHERE is_active = 1
ORDER BY `weight`";
        $this->showSql($sql);
        $statuses = $this->GetCiviDataFromSQL($sql);
        $today = new DateTime();
        $status = null;
        foreach ($statuses as $s) {
            if (!empty($status)) break;
            if ((empty($s['start_event'])&&empty($s['end_event']))||(empty($membership['start_date'])&&empty($membership['end_date']))) {
                $status = $s;
                break;  
            } 
            $startDateOk = true;
            if (!empty($membership[$s['start_event']])) {
                $adjustedDate = date("Y-m-d",strtotime($membership[$s['start_event']] . ' + ' . $s['start_add_days'] . ' days'));
                $startDate = new DateTime($adjustedDate);
                $startDateOk = ($today>=$startDate);  
            } 
            if (!$startDateOk) continue;
            $endDateOk = true;
            if (!empty($membership[$s['end_event']])) {
                $adjustedDate = date("Y-m-d",strtotime($membership[$s['end_event']] . ' + ' . $s['end_add_days'] . ' days'));
                $endDate = new DateTime($adjustedDate);
                $endDateOk = ($today<=$endDate);  
            }
            if (!$endDateOk) continue;

            $status = $s;
        }
        return $status;
    }
       
    function getDistinctDepartmentDenominations() {
        /**
         * This function retrieves all the departments in the dms_department table.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
    	$sql = "SELECT DISTINCT SUBSTR(org_id,1,1) `dep_id`,`dep_name`, SUBSTR(org_id,2,2) `den_id`,`den_name` 
FROM dms_orgunit OU
LEFT JOIN `dms_denomination` DN ON SUBSTR(org_id,2,2) = `DN`.`den_id`
LEFT JOIN `dms_department` DP ON SUBSTR(org_id,1,1) = `DP`.`dep_id`";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function getDistinctDenominationCongregations() {
        /**
         * This function retrieves all the departments in the dms_department table.
         *
         * @param  none
         * @throws none
         * @return false on error;  An array of the resulting dataset.
         */
    	$sql = "SELECT DISTINCT SUBSTR(org_id,1,3) `den`, SUBSTR(org_id,4) `con_id`,`org_name`,org_subregion `region`
FROM dms_orgunit order by SUBSTR(org_id,4)";
    	$this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    
    function getCiviContactRelationships($contactId) {
        $params['version'] = 3;
        $params['contact_id_a'] = $contactId;
        $params['is_active'] = '1';
        $records_a = civicrm_api('Relationship', 'get', $params);
        $result = array();
        if ($records_a['count']>0) $result = array_merge($result,$records_a['values']);
        
        $bparams['version'] = 3;
        $bparams['contact_id_b'] = $contactId;
        $bparams['is_active'] = '1';
        $records_b = civicrm_api('Relationship', 'get', $bparams);
        if ($records_b['count']>0) $result = array_merge($result,$records_b['values']);
        
        return $result;
    } 
    
    function getAllCiviRelationshipTypes() {
        $params['version'] = 3;
        $params['is_active'] = '1';
        $result = civicrm_api('RelationshipType', 'get', $params);
        return (empty($result['values'])) ? array():$result['values'];
    }
    
    function getCiviContact($contactId) {
        $params['version'] = 3;
        $params['id'] = $contactId;
        $result = civicrm_api('Contact', 'getsingle', $params);
        return $result;
    }
    
    function getCiviContactContributions($contactId) {
        $params['version'] = 3;
        $params['id'] = $contactId;
        $result = civicrm_api('Contributions', 'get', $params);
        return (empty($result['values'])) ? array():$result['values'];
    }
    
    function getCiviContactActivities($contactId) {
        $activities = $GLOBALS['xmlConfig']->activityconfig->activities->activity;
        $params['version'] = 3;
        $params['contact_id'] = $contactId;
        $params['options']['sort'] = ' activity_date_time DESC';
        $result = civicrm_api('Activity', 'get', $params);
        if (empty($result['values'])) {
            return array();
        } else {
            foreach ($result['values'] as $r) {
                foreach ($activities as $a) {
                    if ($a['value']==$r['activity_type_id']) {
                        $return[] = $r;
                        break;
                    }   
                }    
            }
            return $return;
        }
    }
    
    public static function isCiviBamMember($contactId) {
        $bamMembershipTypeId = (int)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;
        $sql = "SELECT id FROM civicrm_membership WHERE contact_id = $contactId AND membership_type_id = $bamMembershipTypeId;";
    	self::showSql($sql);
        $result = self::GetCiviDataFromSQL($sql);
    	return (!empty($result));
    }
    
    function getCiviPrimaryAddress($contactId) {
        $sql = "SELECT street_address,supplemental_address_1,supplemental_address_2,supplemental_address_3,city,postal_code FROM civicrm_address WHERE contact_id = $contactId AND is_primary = 1;";
    	$this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    function getCiviContactByQuery($contactId) {
        $titleOptGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->titles;
        $sql = "
            SELECT C.*,`DO`.*,`DR`.*,`O`.`label` `title` FROM `civicrm_contact` `C` 
                LEFT JOIN `civicrm_dms_contact_other_data` `DO` ON `DO`.`contact_id` = `C`.`id`
                LEFT JOIN `civicrm_dms_contact_reporting_code` `DR` ON `DR`.`contact_id` = `C`.`id`
                LEFT JOIN `civicrm_option_value` `O` ON `option_group_id` = $titleOptGroupId AND `prefix_id` = `O`.`value`
                WHERE `C`.`id` = $contactId";
    	$this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    function getCiviPrimaryEmail($contactId) {
        $sql = "SELECT * FROM civicrm_email WHERE contact_id = $contactId AND is_primary = 1;";
        $this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    function getCiviCellPhone($contactId) {
        $sql = "SELECT * FROM civicrm_phone WHERE contact_id = $contactId AND phone_type_id = 2;";
    	$this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    
    function addCiviActivityContact($activityId,$contactId,$recordTypeId) {
        $sql = "insert into civicrm_activity_contact (activity_id,contact_id,record_type_id) values ($activityId,$contactId,$recordTypeId);";
        $this->showSql($sql);
        $result = $this->executeCiviDbSql($sql);
        return $result;
    }
    
    function removeCiviActivityContacts($activityId) {
        $sql = "delete from civicrm_activity_contact where activity_id = $activityId;";
        $this->showSql($sql);
        $result = $this->executeCiviDbSql($sql);
        return $result;
    }
    
    function executeCiviDbSql($sql) {
        $connectionSettings = $GLOBALS['civiDBConnectionDetails'];
        $db = new database;
        $db->host = (string)$connectionSettings->host;
        $db->username = (string)$connectionSettings->username;
        $db->password = (string)$connectionSettings->password;
        $db->database = (string)$connectionSettings->database;
        $db->connect(true);
        $this->showSql($sql);
        $result = $db->execute($sql);
        $db->close(); 
        return $result;
    }
    
    function getUserNotifications() {
        $activities = $this->getCiviContactActivities($_SESSION['dms_user']['civ_contact_id']);
        $notificationsDivs = '';
        $primaryDepartment = $_SESSION['dms_user']['config']['impersonate'];
        $departmentName = $this->GetDepartmentName($primaryDepartment);
        #$reminderTotal = $this->getReminderMonthContactTotal($primaryDepartment);
        $reminderTotal=0;
        if ($reminderTotal>0) {
            $currentMonth = date("M",strtotime('last month'));
            $notificationsDivs = '<div class="notificationReminderChkBox">&nbsp;</div><div id="remindersActionDiv" onclick="alert(\'coming soon\')">
                <div class="reminderCnt">'.$reminderTotal.'</div>
                <div class="action">
                    <div class="aType">Reminders to send</div>
                    <div class="cont">'.$primaryDepartment.' - '.$departmentName.' <span class="t">('.date("F").')</span></div>
                </div>
            </div>';   
        } 
        $birthdaysToday = $GLOBALS['functions']->getTodaysBirthdays($primaryDepartment);
        if (!empty($birthdaysToday)) {
            $notificationsDivs = '
                <div class="divNotification" a="birthday">
                    <div class="birthdayCnt">'.$birthdaysToday.'</div>
                    <div class="action">
                        
                        <div class="aType">Birthdays Today</div>
                        <div class="cont">'.$primaryDepartment.' - '.$departmentName.' <span class="t">('.date("d F").')</span></div>
                    </div>
                    <div id="prezzieDiv"><img src="/dms/img/gift.png" width="32" height="32" /></div>
                </div>
                <form action="/dms/contacts/find.contact.php" method="POST" style="hidden" id="frmBirthday" name="frmBirthday">
                    <input type="hidden" name="srch_database" value="civicrm" />
                    <input type="hidden" name="srch_donorDeleted" value="A" />
                    <input type="hidden" name="srch_birthday" value="'.date("-m-d").'" />
                    <input type="hidden" name="srch_orgId" value="'.$primaryDepartment.'" />
                </form>';
        }
        if (!empty($activities)) {
            foreach ($activities as $n) {
                $isAssignedToMe = $this->isActivityAssignedToContact($n['id'],$_SESSION['dms_user']['civ_contact_id']);
                if (!$isAssignedToMe) continue;
                if ($n['status_id']!=1) continue;
                    
                    $cid = $GLOBALS['functions']->getActivityWithContact($n['id']);
                    if (empty($cid)) $cid = $_SESSION['dms_user']['civ_contact_id'];
                    $sourceContact = $GLOBALS['functions']->getCiviContact($cid);
                    $notificationsDivs = '<input type="checkbox" name="status_id" value="'.$n['id'].'" id="status_id-'.$n['id'].'" class="notificationStatusChkBox" /><div class="nextDateDiv" onclick="window.location=\'load.activity.php?d='.$sourceContact['external_identifier'].'&s=civicrm&a='.$n['id'].'\'">
                <div class="dt">
                    <div class="d">'.date("d",strtotime($n['activity_date_time'])).'</div>
                    <div class="m">'.date("M",strtotime($n['activity_date_time'])).'</div>
                    <div class="y">'.date("Y",strtotime($n['activity_date_time'])).'</div>
                </div>
                <div class="action">
                    <div class="aType">'.$n['activity_name'].' <span class="t">'.date("H:i A",strtotime($n['activity_date_time'])).'</span></div>
                    <div class="cont">'.$n['subject'].'</div>
                </div>
            </div>'.$notificationsDivs;
            #<div class="cont">Added By: <a href="load.activity.php?d='.$sourceContact['external_identifier'].'&s=civicrm" class="relationshipLink">'.$sourceContact['display_name'].'</a></div>
            }
        }
        if (empty($notificationsDivs)) $notificationsDivs = '<span style="color: #FFF;">No New Notifications</span>';
        $notificationsDivs = '<div id="notificationsHeadingDiv">Notifications<div class="xClose" id="xCloseNotifications">x</div></div>'.$notificationsDivs;
        $notificationsDivs = '<script type="text/javascript" language="javascript" src="/dms/scripts/notifications.js"></script>'.$notificationsDivs;
        return $notificationsDivs;
    }
    
    function getReminderMonthContactTotal($dept) {
        $month = date("m"); 
        $sql = "SELECT COUNT(*) total FROM civicrm_contact C 
                    INNER JOIN `civicrm_dms_contact_other_data` CV ON CV.contact_id = C.id 
                    INNER JOIN `civicrm_dms_contact_reporting_code` CR ON CR.`contact_id` = C.id 
                    WHERE reminder_month = $month
                    AND SUBSTR(organisation_id,1,1) = '$dept';";
        $this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return 0;
    	} else {
    		return $result[0]['total'];
    	}
    }
    
    function insertCiviContactPhoneNo($contactId,$phoneNo) {
        $existingPhoneNos = $this->getCiviContactPhoneNos($contactId);
        foreach ($existingPhoneNos as $p) {
            if ($p['phone']==$phoneNo) return  $p['id'];
        }
        
        $apiParams['version'] = 3;
        $apiParams['phone'] = $phoneNo;
        $apiParams['contact_id'] = $contactId;
        $apiParams['location_type_id'] = 1;
        $apiParams['phone_type_id'] = 1;
        $result = civicrm_api('Phone','create',$apiParams);
        if (!empty($result['id'])){
            return $result['id'];
        }
    }
    
    function insertCiviContactEmail($contactId,$email) {
        $existingEmails = $this->getCiviContactEmailAddresses($contactId);
        foreach ($existingEmails as $e) {
            if ($e['emailAddress']==$email) return $e['id'];
        }
        
        $apiParams['version'] = 3;
        $apiParams['email'] = $email;
        $apiParams['contact_id'] = $contactId;
        $apiParams['location_type_id'] = 1;
        $result = civicrm_api('Email','create',$apiParams);
        if (!empty($result['id'])){
            return $result['id'];
        }
    }
    
    function getActivity($activityId) {
        $params['version'] = 3;
        $params['id'] = $activityId;
        $result = civicrm_api('Activity', 'getsingle', $params);
        if (empty($result['id'])) {
            return array();
        } else {
            $activityRecordTypes = $GLOBALS['xmlConfig']->activityconfig->civiActivityRecordTypeIds->typeId;
            $sql = "select * from civicrm_activity_contact where activity_id = $activityId";
            $contacts = $this->GetCiviDataFromSQL($sql);
            if (!empty($contacts)) {
                foreach ($contacts as $c) {
                    foreach ($activityRecordTypes as $t) {
                        if ($t['value']==$c['record_type_id']) {
                            $type = (string)$t['desc'];
                            $result['contacts'][$type][] = $c['contact_id'];   
                        }   
                    }
                }
            }
            return $result;
        }    
    }
    
    function isActivityAssignedToContact($activityId,$contactId) {
        if (empty($activityId)||empty($contactId)) return false;
        $activityRecordTypes = $GLOBALS['xmlConfig']->activityconfig->civiActivityRecordTypeIds->typeId;
        foreach ($activityRecordTypes as $at) { 
            if ($at['desc']=='assigned_to') $assignedTypeId = $at['value'];
            if ($at['desc']=='source_contact_id') $sourceTypeId = $at['value'];
        }  
        $sql = "select * from civicrm_activity_contact where activity_id = $activityId and contact_id = $contactId";
        $contacts = $this->GetCiviDataFromSQL($sql);
        if (!empty($contacts)) {
            if (count($contacts)!=1) return false;
            if ($contacts[0]['record_type_id']==$assignedTypeId||$contacts[0]['record_type_id']==$sourceTypeId) return true;
        }
        return false;
    }
    
    function getLatLonFromPostalCode($postalCode) {
        $sql = "select DISTINCT pco_lat `lat`,pco_lon `lon` from dms_postalCodes where pco_postal_code = '".str_pad($postalCode,4,'0',STR_PAD_LEFT)."';";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    
    function getFinancialYear() {
        $year_1 = (date("m")>10) ? date("Y") : date("Y") - 1;
        $year_2 = (date("m")>10) ? date("y") + 1 : date("y");
        $return = $year_1 . '/' . $year_2;
        return $return;
    }
    
    public static function showSql($sql) {
        $showFlag = (string)$GLOBALS['xmlConfig']->showSql['value'];
        $authorised = (empty($_SESSION['dms_user']['authorisation'])) ? false : $_SESSION['dms_user']['authorisation']->isAdmin;
        
        if (empty($_SESSION['dms_user']['sql'])) $_SESSION['dms_user']['sql'] = '';
        if ($showFlag=='Y'&&$authorised) $_SESSION['dms_user']['sql'] .= '<div class="sql"><pre />'.$sql.'</div>';
    }
    
    function getCiviDmsTransactionDetail($id) {
        $sql = "SELECT * FROM civicrm_dms_transaction where contribution_id = $id;";
        $this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    
    function getCiviDmsDonorOtherDetail($id) {
        $sql = "SELECT * FROM `civicrm_dms_contact_other_data` where contact_id = $id;";
        $this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    
    function getCiviDmsDonorReportDetail($id) {
        $sql = "SELECT * FROM `civicrm_dms_contact_reporting_code` where contact_id = $id;";
        $this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    
    function getCiviDmsOrganisation($orgId) {
        $sql = "SELECT * FROM `civicrm_dms_organisation` where org_id = '$orgId';";
        $this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		if (count($result)==1) {
                    return $result[0];
                } else {
                    return false;
                }
    	}
    }
    
    function GetMailMergeRecordCount($filename) {
        $sql = "SELECT COUNT(*) `row_count` FROM `dms_acknowledgement` 
        WHERE ack_document = '$filename'";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            return false;
    	} else {
            return $result[0]['row_count'];
    	}
    }
    
    function getActivityWithContact($activityId) {
        $recordTypes = $GLOBALS['xmlConfig']->activityconfig->civiActivityRecordTypeIds->typeId;
        foreach ($recordTypes as $r) {
            if ($r['desc']=='with_contact_id') $withContactTypeId = $r['value'];
        }
        
        $sql = "select contact_id from civicrm_activity_contact where record_type_id = $withContactTypeId and activity_id = $activityId;";
        $result = $this->GetCiviDataFromSQL($sql);
        if (!$result) {
            return false;
    	} else {
            return $result[0]['contact_id'];
    	}
    }
    
    ####  New 4.1.1 Functions
    function getMySalutations() {
        $sql = "SELECT * FROM `dms_salutations`";
        $this->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            return false;
    	} else {
            return $result;
    	}
    }
    
    function addSowerAddress($contactId) {
        # Delete all current sower addresses
        $this->deleteSowerAddresses($contactId);
        
        # Get primary address
        $parms['version'] = 3;
        $parms['sequential'] = 1;
        $parms['contact_id'] = $contactId;
        $parms['is_primary'] = 1;
        $result = civicrm_api('Address','get',$parms);
        if ($result['is_error']===0&&$result['count']===1) {
            $n = $result['values'][0];
            unset($n['id']);
            $n['location_type_id'] = (int)$GLOBALS['xmlConfig']->sowerLocationTypeId['id'];
            $n['is_primary'] = 0;
            $n['version'] = 3;
            $insertResult = civicrm_api('Address','create',$n);
            return $insertResult;
        }
        return false;
    }
    
    function deleteSowerAddresses($contactId) {
        $parms['version'] = 3;
        $parms['location_type_id'] = (int)$GLOBALS['xmlConfig']->sowerLocationTypeId['id'];
        $parms['contact_id'] = $contactId;
        $getAddresses = civicrm_api('Address','get',$parms);
        $result = array();
        if ($getAddresses['count']>0) {
            foreach ($getAddresses['values'] as $a) {
                $delete['version'] = 3;
                $delete['id'] = $a['id'];
                $result[] = civicrm_api('Address','delete',$delete);
            }
        }
        return $result;
    }
    
    function deleteFromCiviGroup($contactGroupId) {
        $group['version'] = 3;
        $group['id'] = $contactGroupId;
        $result = civicrm_api('GroupContact', 'delete', $group);
        return $result;
    }   
    
    function getPrevContact($dnrNo) {
        if (empty($_SESSION['dmsDonorSearchResultset'])) return null;
        $current = $previous = null;
        foreach($_SESSION['dmsDonorSearchResultset'] as $k=>$v) {
            $previous = $current;
            $current = $v['dnr_no'];
            if ($current==$dnrNo) return $previous;
         }
         return null;
    }
    function getNextContact($dnrNo) {
        if (empty($_SESSION['dmsDonorSearchResultset'])) return null;
        $current = $next = null;
        foreach($_SESSION['dmsDonorSearchResultset'] as $k=>$v) {
            $current = $next;
            $next = $v['dnr_no'];
            if ($current==$dnrNo&&$next!=$dnrNo) return $next;
         }
         return null;
    }
    
    function getNextBAMCertificates($lang,$limit) {
        $condition = ($lang=='afr') ? '=':'!=';
        $returnLimit = ($lang=='mix') ? '2':'4';
        $sql = "SELECT M.id `mid`,C.id `contact_id`,C.external_identifier `dnr_no`,first_name,last_name,B.`bam_club__15` `ref`,preferred_language `lang` FROM civicrm_membership M
INNER JOIN civicrm_value_membership_custom_data_4 B ON M.id = B.`entity_id`
INNER JOIN civicrm_contact C ON C.id = M.`contact_id`
WHERE membership_type_id = 1 AND B.`bam_certificate_printed_16` = 0 AND preferred_language $condition 'af_ZA'
ORDER BY preferred_language,bam_club__15
LIMIT $limit,$returnLimit";
        
        if ($lang=='mix') {
            $sql_O = 'SELECT * FROM (' . $sql . ') `O`';
            $sql_A = "SELECT * FROM (SELECT M.id `mid`,C.id `contact_id`,C.external_identifier `dnr_no`,first_name,last_name,B.`bam_club__15` `ref`,preferred_language `lang` FROM civicrm_membership M
INNER JOIN civicrm_value_membership_custom_data_4 B ON M.id = B.`entity_id`
INNER JOIN civicrm_contact C ON C.id = M.`contact_id`
WHERE membership_type_id = 1 AND B.`bam_certificate_printed_16` = 0 AND preferred_language = 'af_ZA'
ORDER BY preferred_language,bam_club__15
LIMIT $limit,$returnLimit) `A`";
            $sql = $sql_A . ' UNION ALL ' . $sql_O;
        }
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
    
    function getOrderAccounts($dnrNo) {
        $sql = "SELECT * FROM dms_orders WHERE ordr_dnr_no = $dnrNo";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result;
        }
    }
    
    ####   4.3.1 Functions
    function getTodaysBirthdays($primaryDepartment) {
        $birthday = date("-m-d");
        $sql = "SELECT count(*) `total` FROM civicrm_contact C "
                . "INNER JOIN civicrm_dms_contact_reporting_code R ON contact_id = C.id "
                . "WHERE substr(organisation_id,1,1) = '$primaryDepartment' "
                . "AND birth_date LIKE '%$birthday%'";
        $GLOBALS['functions']->showSql($sql);
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
        } else {
            return $result[0]['total'];
        }
    }
    function hasUserGotNotifications() {
        
        if (!empty($_SESSION['dms_user']['civ_contact_id'])) {
            $activities = $this->getCiviContactActivities($_SESSION['dms_user']['civ_contact_id']);
            $exclude = array('Completed','Cancelled');
            if (!empty($activities)) {
                foreach ($activities as $k) if (!in_array($k['status'],$exclude)) return true;
            }
        }
        
        if (!empty($_SESSION['dms_user']['config']['impersonate'])) {
            $primaryDepartment = $_SESSION['dms_user']['config']['impersonate'];
            $birthdaysToday = $GLOBALS['functions']->getTodaysBirthdays($primaryDepartment);
            if (!empty($birthdaysToday)) return true;
        }
        
        return false;
    }
    function getCiviContributionByQuery($contributionId) {
        $sql = "
            SELECT * FROM `civicrm_contribution` `C` 
                LEFT JOIN `civicrm_dms_transaction` `T` ON `T`.`contribution_id` = `C`.`id`
                WHERE `C`.`id` = $contributionId";
    	$this->showSql($sql);
        $result = $this->GetCiviDataFromSQL($sql);
    	if (!$result) {
    		return false;
    	} else {
    		return $result[0];
    	}
    }
    function addSignatureToBody($region,$html,$contact_id) {
        $contact = $this->getCiviContact($contact_id);
        $r = new region;
        $r->Load($region);
        $language = ($contact['preferred_language']=='af_ZA') ? 'afr':'eng';
        $address = 'region_address_'.$language;
        $patterns = array('/###tel###/','/###fax###/','/###address###/');
        $replacements = array($r->region_telephone,$r->region_fax,$r->$address);
        $signatureFile = $language . '_signature.htm';
        $signature = preg_replace($patterns,$replacements,file_get_contents('email/signatures/'.$signatureFile));
        return $html.$signature;
    }
    
    function getFinancialYearStartDate() {
        $year = (date("Y-m-d")>date("Y-11-01")) ? date("Y"):date("Y")-1;
        return $year . '-11-01';
    }
    
    function xml2array ( $xmlObject, $out = array () )  
    {  
       foreach ( (array) $xmlObject as $index => $node )  
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;  
       return $out;  
    }
    function getAllWidgetTemplates() {
        $sql = "SELECT * FROM dms_widget WHERE wid_isTemplate = 'Y';";
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		return $result;
    	}
    }
    function getAllUsers() {
        $sql = "SELECT * FROM dms_user";
        $GLOBALS['db']->select($sql);
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            return false;
    	} else {
            return $result;
    	}
    }
    
    ####   5.0.1 Functions
    function getSowerEmail($contactId,$groupId) {
        $sql = "SELECT email_id,email FROM civicrm_group_contact G "
                . "INNER JOIN civicrm_email E ON G.email_id = E.id "
                . "WHERE G.contact_id = $contactId AND G.group_id = $groupId;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
    	} else {
            return $result[0];
    	}
    }
    
    function getGroups() {
        $parms['version'] = 3;
        $parms['is_active'] = 1;
        $result = civicrm_api('group','get',$parms);
        return $result;
    }
    
    function getTotalContactsInGroup($groupId) {
        $sql = "SELECT count(*) `cnt` FROM civicrm_group_contact WHERE group_id = $groupId and `status` = 'Added'";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
    	} else {
            return $result[0]['cnt'];
    	}
    }
    
    function createReportForm($xmlPrompts,$report) {
        
        $basename = basename($report,'.xml');
        $name = 'frm' . ucfirst($basename);
        $form = "\n" . '<form method="POST" action="create.report.php" name="' . $name . '" id="' . $name . '">';
        $form .= "\n" . '<input type="hidden" name="filename" />';
        $form .= "\n\t" .  '<table cellspacing="0" cellpadding="5" width="100%" id="tbl'.ucfirst($basename).'">';
        $validation = '';
        if (count($xmlPrompts->children())>0) {
            foreach ($xmlPrompts->children() as $p) {
                $fieldId = $basename.'_'.$p['name'];
                $visible = ($p['visible']=='Y') ? '':'style="display:none"';
                $form .= "\n\t\t" . '<tr '.$visible.'><td><label for="'.$fieldId.'">'.$p['label'].'</label></td>';
                $parms = null;
                if (empty($p['function'])) {
                    $input = $this->getInput($p['name'],$fieldId,$p['defaultValue'],$parms);
                } else {
                    if (!empty($p->otherParameters)) $parms = $this->xml2array($p->otherParameters);
                    $input = $this->{(string)$p['function']}($p['name'],$fieldId,$p['defaultValue'],$parms);
                }
                $form .= "\n\t\t" . '<td align="right">' .$input. '</td></tr>';
                
                if ($p['validate']=='Y') {
                    $validation .= "\n\t" . 'if ($("#'.$fieldId.'").val().length==0) {';
                    $validation .= "\n\t\t" . 'alert("Please populate the ' . $p['label'] . ' field");';
                    $validation .= "\n\t\t" . '$("#'.$fieldId.'").focus();';
                    $validation .= "\n\t\t" . 'return;';
                    $validation .= "\n\t" . '}';
                }
            }
        }
        $form .= "\n\t</table>\n</form>";
        return array("form" => $form, "validation" => $validation);
    }
    
    function getDepartmentSelect($name,$id,$defaultValue,$parms) {
        $sql = "SELECT * FROM civicrm_dms_department";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) return '';
        
        $emptyOption = $allOption = '';
        if (!empty($parms)) {
            if (!empty($parms['showAll'])&&$parms['showAll']=='Y') $allOption = "\n" . '<option value="All">All</option>';
            if (!empty($parms['showEmpty'])&&$parms['showEmpty']=='Y') $emptyOption = "\n" . '<option value="">-- select --</option>';
        }
        
        $select = '<select name="'.$name.'" id="'.$id.'">' . $emptyOption . $allOption;
        foreach ($result as $d) {
            $selected = ($defaultValue==$d['dep_id']) ? ' SELECTED':'';
            $select .= "\n" . '<option value="'.$d['dep_id'].'"'.$selected.'>'.$d['dep_name'].'</option>';
        }
        $select .= '</select>';
        return $select;
    }
    
    function getInput($name,$id,$defaultValue,$parms) {
        
        $type = 'text';
        if (!empty($parms)) {
            if (!empty($parms['type'])) $type = $parms['type'];
        }
        
        $input = '<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="'.$defaultValue.'" />';
        return $input;
    }
    
    function getSelect($name,$id,$defaultValue,$parms) {
        
        $emptyOption = $allOption = $options = '';
        if (!empty($parms['showAll'])&&$parms['showAll']=='Y') $allOption = "\n" . '<option value="All">All</option>';
        if (!empty($parms['showEmpty'])&&$parms['showEmpty']=='Y') $emptyOption = "\n" . '<option value="">-- select --</option>';
        $showValue = (!empty($parms['showValueWithText'])&&$parms['showValueWithText']=='Y');
        if (!empty($parms['options'])) {
            
            foreach ($parms['options']['option'] as $k=>$v) {
                if (is_object($v)) {
                    $opts[] = $this->xml2array($v);
                } else {
                    if (!is_array($v)) {
                        $o[$k] = $v;
                    } else {
                        $opts[] = $v;
                    }
                }
            }
            if (isset($o)) $opts[] = $o;
            
            foreach ($opts as $opt) {
                if (!empty($opt['value'])) {
                    $selected = ($defaultValue==$opt['value']) ? ' SELECTED':'';
                    $txt = ($showValue) ? $opt['value'] . ' - ' . $opt['text']:$opt['text'];
                    
                    $options .= "\n" . '<option value="'.$opt['value'].'"'.$selected.'>'.$txt.'</option>'; 
                    
                }
                if (!empty($opt['db'])) {
                    $options .= $this->getSelectOptionsFromDb($opt['db'],$opt['tableName'],$opt['valueField'],$opt['textField'],$defaultValue,$showValue);
                }
            }
        }
        
        $select = '<select name="'.$name.'" id="'.$id.'">' . $emptyOption . $allOption;
        $select .= $options . '</select>';
 
        return $select;
    }
    
    function getSelectOptionsFromDb($db,$tableName,$valueField,$textField,$defaultValue,$includeValue=false) {
        $orderby = ($includeValue) ? $valueField:$textField;
        $sql = "SELECT * FROM $tableName order by $orderby";
        $result = $GLOBALS[$db]->select($sql);
        $options = '';
        if (!empty($result)) {
            foreach ($result as $d) {
                $selected = ($defaultValue==$d[$valueField]) ? ' SELECTED':'';
                $txt = ($includeValue) ? $d[$valueField] . ' - ' . $d[$textField]:$d[$textField];
                $options .= "\n" . '<option value="'.$d[$valueField].'"'.$selected.'>'.$txt.'</option>'; 
            }
        }
        return $options;
    }
    
    function getBudgetTotal() {
        $sql = "SELECT SUM(bud_amount) `total` FROM dms_budget";
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            return false;
    	} else {
            return $result[0]['total'];
    	}
    }
    
    static function pre_array($array,$exit=false) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
        if ($exit) exit();
    }
    
    function getSowerData() {
        $sowerGroupId = (string)$GLOBALS['xmlConfig']->civiGroups->sower;
        $sowerLocationTypeId = (string)$GLOBALS['xmlConfig']->sowerLocationTypeId['id'];
        $bamMembershipId = (string)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;
        $titlesOptionGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->titles;
        $sql = "SELECT 
	DISTINCT org_region `region`, 
        IF (b.id IS NOT NULL,'9', SUBSTR(r.`organisation_id`,1,1)) `DP`,
	UCASE(SUBSTR(c.preferred_language,1,1)) `lang`, 
	external_identifier `dnr_no`, 
	j.`label` `title`,
	c.first_name `inits`,
	c.last_name `name`, 
    	CONCAT(
	RPAD( 
	IF (a.`street_address` IS NULL OR SUBSTR(a.`street_address`,1,1) = ' ',
	  IF (a.`supplemental_address_1` IS NULL OR SUBSTR(a.`supplemental_address_1`,1,1) = ' ',
	    IF (a.`supplemental_address_2` IS NULL OR SUBSTR(a.`supplemental_address_2`,1,1) = ' ',a.`city`,a.`supplemental_address_2`)
	    , a.`supplemental_address_1` )
	  ,a.`street_address`),30,' '
            ),
 	RPAD( 
	IF (a.`supplemental_address_1` IS NULL OR SUBSTR(a.`supplemental_address_1`,1,1) = ' ',
	  IF (a.`supplemental_address_2` IS NULL OR SUBSTR(a.`supplemental_address_2`, 1,1) = ' ',
	    IF (a.`street_address` IS NULL OR SUBSTR(a.`street_address`,1,1) = ' ', ' ', a.`city`),a.`supplemental_address_2`)
	  , a.`supplemental_address_1` ),30,' '
            ), 
 	RPAD( 
	IF (a.`supplemental_address_2` IS NULL OR SUBSTR(a.`supplemental_address_2`, 1,1) = ' ', 	
	  IF (a.`street_address` IS  NULL OR a.`supplemental_address_1` IS NULL,  ' ', 
	    IF (a.`supplemental_address_1` IS NULL OR SUBSTR(a.`supplemental_address_1`,1,1) = ' ', ' ', a.`city`)),
	  IF (a.`supplemental_address_1` IS NULL OR SUBSTR(a.`supplemental_address_1`,1,1) = ' ', a.`city`,a.`supplemental_address_2`)),30,' '
            ), 
 	RPAD(  
	IF (a.`street_address` IS NULL OR SUBSTR(a.`street_address`,1,1) = ' '
	  OR a.`supplemental_address_1` IS NULL OR SUBSTR(a.`supplemental_address_1`,1,1) = ' '
	    OR a.`supplemental_address_2` IS NULL OR SUBSTR(a.`supplemental_address_2`,1,1) = ' ', ' ', 
	    IF(a.`city` IS NULL OR SUBSTR(a.`city`,1,1) = ' ', ' ',a.`city`)),30,' '
            )
           ) address,
          LPAD(a.postal_code,4, '0') pcode,
	\"0\" `status`,
	\"Y\" `sower`,
	\"L\" `foreign`
FROM civicrm_group_contact G
INNER JOIN civicrm_contact c ON c.id = G.`contact_id`
LEFT JOIN `civicrm_address` a ON c.`id` = a.`contact_id` AND a.location_type_id = $sowerLocationTypeId
LEFT JOIN `civicrm_dms_contact_reporting_code` r ON c.`id` = r.`contact_id`
LEFT JOIN `civicrm_dms_organisation` org ON org.org_id = r.organisation_id
LEFT JOIN `civicrm_option_value` j ON c.`prefix_id` = j.`value` AND j.`option_group_id` = $titlesOptionGroupId
LEFT JOIN `civicrm_membership` b ON b.`contact_id` = c.`id` AND b.`membership_type_id` = '$bamMembershipId'
WHERE group_id = $sowerGroupId AND `status` = 'Added'
ORDER BY 1,2,3;";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return false;
    	} else {
            return $result;
    	}
    }
    
    
    function getEmailAddresses($emailAddress) {
        $sql = "select `email`,`display_name` from `civicrm_email` `e`
inner join `civicrm_contact` `c` on `c`.`id` = `e`.`contact_id`
where `email` = '$emailAddress'";
        $result = $GLOBALS['civiDb']->select($sql);
        if (!$result) {
            return array('values'=>'Nothing found','count'=>0);
    	} else {
            return array_merge(array('values'=>$result),array('count'=>count($result)));
    	}
    }
    
    function markSowerAsPreviousAddresses($contactId) {
        $parms['version'] = 3;
        $parms['location_type_id'] = (int)$GLOBALS['xmlConfig']->sowerLocationTypeId['id'];
        $parms['contact_id'] = $contactId;
        $getAddresses = civicrm_api('Address','get',$parms);
        $result = array();
        if ($getAddresses['count']>0) {
            foreach ($getAddresses['values'] as $a) {
                $edit['version'] = 3;
                $edit['id'] = $a['id'];
                $edit['location_type_id'] = (int)$GLOBALS['xmlConfig']->previousSowerLocationTypeId['id'];
                $result[] = civicrm_api('Address','create',$edit);
            }
        }
        return $result;
    }
}