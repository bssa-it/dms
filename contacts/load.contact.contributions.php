<?php

/**
 * @description
 * This is the contributions of contact detail page.
 * 
 * @author      Chezre Fredericks
 * @date_created 13/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if ((empty($_SESSION['dmsDonorSearchCriteria']['srch_database'])&&empty($_GET['s']))
        ||!isset($_GET['d'])||empty($_GET['d'])) $GLOBALS['functions']->goToIndexPage();

#   SET VITAL VARIABLES
$database = (!empty($_GET['s'])) ? $_GET['s'] : $_SESSION['dmsDonorSearchCriteria']['srch_database'];
$dnrNo = $_GET['d'];

$contributionRows = '';

#   LOAD CONTRIBUTION ROWS
switch ($database) {
    case "archive":
        $dnr = new donor();
        if (!$dnr->Load($dnrNo)) $GLOBALS['functions']->goToIndexPage();
        
        $isDeleted = ($dnr->dnr_deleted=='Y');
        
        $trxns = $GLOBALS['functions']->getTransactionsForDonor($dnrNo);
        if (count($trxns)>0&&!empty($trxns)) {
            foreach ($trxns as $k=>$v) {
                $amnt = $v['trns_amount_received'];
                $userFullname = (!is_null($v['ack_usr_id'])) ? $GLOBALS['functions']->GetJoomlaUserDetails($v['ack_usr_id']):array('name'=>'&nbsp;');
                $contributionRows .= '<tr>';
                $contributionRows .= '<td>' . date("d-m-Y",strtotime($v['trns_date_received'])) . '</td>';
                $contributionRows .= '<td>' . $v['civ_trxn_id'] . '</td>';
                $contributionRows .= '<td>' . $GLOBALS['functions']->getContributionTypeName($v['trns_receipt_type']) . '</td>';
                $contributionRows .= '<td align="right" style="padding-right: 15px;"> R ';
                $contributionRows .=  number_format((float)$amnt,2,'.',',');
                $contributionRows .= '</td>';
                $motivation = ($v['trns_motivation_id']==9000) ? '':$v['trns_motivation_id'] . ' - '.$GLOBALS['functions']->getMotivationCodeDescription($v['trns_motivation_id']);
                $contributionRows .= '<td>' . $motivation .'</td>';
                $contributionRows .= '<td>' . str_pad($v['trns_category_id'],4,'0',STR_PAD_LEFT) . '</td>';
                $contributionRows .= '<td>' . $v['trns_region_id'] . '</td>';
                $contributionRows .= '<td>' . $v['trns_organisation_id'] . '</td>';
                if (!empty($v['trns_acknowledgement_date'])) {
                    $adate = ($v['trns_acknowledgement_date']=='0000-00-00') ? '' : date("d-m-Y",strtotime($v['trns_acknowledgement_date']));
                    $amethod = ($v['trns_acknowledgement_date']=='0000-00-00') ? '' : 'SCO';
                } else {
                    $adate = (is_null(($v['ack_date']))) ? '' : date("d-m-Y H:i:s",strtotime($v['ack_date']));
                    $amethod = $v['ack_method'];
                }
                $contributionRows .= '<td>' . $adate . '</td>';
                $contributionRows .= '<td>'.$userFullname['name'].'</td>';
                $contributionRows .= '<td>'.$amethod.'</td>';
                $document = (strpos($v['ack_document'],'.pdf')) ? '<a href="../acklists/'.$v['ack_document'].'">'.$v['ack_document'].'</a>':$v['ack_document'];
                $contributionRows .= '<td>'.$document.'</td>';
                $contributionRows .= '</tr>';
            }
        } else {
            $contributionRows = '<td colspan="12">No transactions found.</td>';
        }
        
        break;
    case "civicrm":
        $dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
        if (!$dnr) $GLOBALS['functions']->goToIndexPage();
        $contactId = $dnr['contact_id'];
        $isDeleted = ($dnr['contact_is_deleted']==1);
        
        $contParams['version'] = 3;
        $contParams['contact_id'] = $contactId;
        $contParams['options']['limit'] = 100000;
        $contParams['options']['sort'] = ' receive_date DESC';
        $apiRecord = civicrm_api('Contribution', 'Get', $contParams);
        if ($apiRecord['count']>0) {
            foreach ($apiRecord['values'] as $k=>$v) {
                #   Get Custom Civi Data
                $dmsDetail = $GLOBALS['functions']->getCiviDmsTransactionDetail($v['id']);
                $acknowledgement = $GLOBALS['functions']->GetAcknowledgmentsForCiviContribution($v['id']);
                if (empty($acknowledgement)) {
                    $contributionDate = (empty($v['thankyou_date'])) ? '&nbsp;':'SCO ' . date("d-m-Y",strtotime($v['thankyou_date']));
                    $method = (empty($v['thankyou_date'])) ? '&nbsp;':'SCO';
                    $acknowledgement = array("ack_method"=>$method,"ack_document"=>'&nbsp;',"ack_date"=>$contributionDate);
                    $document = '&nbsp;';
                    $userFullname = array('name'=>'&nbsp;');   
                } else {
                    $contributionDate = (is_null($acknowledgement['ack_date'])) ? '&nbsp;' : 'DMS ' . date("d-m-Y H:i:s",strtotime($acknowledgement['ack_date']));
                    $method = $acknowledgement['ack_method'];
                    $document = (strpos($acknowledgement['ack_document'],'.pdf')) ? '<a href="../acklists/'.$acknowledgement['ack_document'].'">'.$acknowledgement['ack_document'].'</a>':$acknowledgement['ack_document'];
                    $userFullname = $GLOBALS['functions']->GetJoomlaUserDetails($acknowledgement['ack_usr_id']);
                }
                $contributionRows .= '<tr>';
                $contributionRows .= '<td>' . date("d-m-Y",strtotime($v['receive_date'])) . '</td>';
                $contributionRows .= '<td>' . $v['trxn_id'] . '</td>';
                $contributionRows .= '<td>' . $v['payment_instrument'] . '</td>';
                $contributionRows .= '<td align="right" style="padding-right: 15px;"> R ';
                $contributionRows .=  number_format((float)$v['total_amount'],2,'.',',');
                $contributionRows .= '</td>';
                $motivation = ($dmsDetail['motivation_id']==9000) ? '':$dmsDetail['motivation_id'] . ' - '.$GLOBALS['functions']->getMotivationCodeDescription($dmsDetail['motivation_id']);
                $contributionRows .= '<td>' . $motivation .'</td>';
                $contributionRows .= '<td>' . str_pad($dmsDetail['category_id'],4,'0',STR_PAD_LEFT) . '</td>';
                $contributionRows .= '<td>' . $dmsDetail['region_id'] . '</td>';
                $contributionRows .= '<td>' . $dmsDetail['organisation_id'] . '</td>';
                $contributionRows .= '<td>' . $contributionDate . '</td>';
                $contributionRows .= '<td>'.$userFullname['name'].'</td>';
                $contributionRows .= '<td>'.$method.'</td>';
                $contributionRows .= '<td>'.$document.'</td>';
                $contributionRows .= '</tr>';
                 
            }
        } else {
            $contributionRows = '<td colspan="12">No transactions found.</td>';
        }
        break;
}

require('html/'.$curScript.'.htm');