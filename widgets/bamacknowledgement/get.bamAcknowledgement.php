<?php

 /**
 * @description
 * This is the dashboard of the DMS system
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * @copyright   None
 * @version     3.0.1
 * 
 * @Changes
 * 21/05/2014 - Chezre Fredericks:
 * File created
 * 
 * @availableVariables
 * $qtrNo   :   is the current quadrant number
 * $w       :   is the widget class object for the current widget
 * 
 */

/* *    CRITERIA
 * JOINS:
 * 1. INNER JOIN `$civicrmDb`.`civicrm_contact` C ON C.id = civ_contact_id 
 * 2. INNER JOIN `$civicrmDb`.`civicrm_membership` M ON M.contact_id = civ_contact_id
 * 
 * 1. Make sure the donor is in the civicrm database
 * 2. Make sure the donor is a bam member
 * 
 * WHERE 
    1. trns_date_received >= '$fromDate' 
    2. AND T.`trns_amount_received` > $floorContributionAmount
    3. AND P.apr_must_acknowledge = 'Y'
    4. AND A.`ack_date` IS NULL 
       AND T.`trns_dnr_acknowledged` = 'N' 
 * 
 * Explanation
 * 1. From date inserted in widget settings
 * 2. For contributions greater than the value set in widget settings
 * 3. Donor want's to be acknowledged
 * 4. Donor has not been acknowledged yet 
 */

include("../../inc/globals.php");
$w = new widget();
$w->Load($_GET['wid']);
$prompts = simplexml_load_file($w->wid_xmlFilename);

$floorContributionAmount = (string)$prompts->contributionFloorValue;
$fromDate = (string)$prompts->fromDate;
$civicrmDb = (string)$GLOBALS['civiDb']->database;
$bamMembershipType = (string)$GLOBALS['xmlConfig']->bam->civiMembershipTypeId;
$sql = "
SELECT
    `trns_id`,
    `display_name` `dnr_name`,
    `trns_date_received`,
    `trns_amount_received`,
    `trns_dnr_no`
FROM 
    dms_transaction T 
    INNER JOIN `$civicrmDb`.`civicrm_contact` C ON C.id = civ_contact_id
    INNER JOIN `$civicrmDb`.`civicrm_membership` M ON M.contact_id = civ_contact_id AND M.membership_type_id = $bamMembershipType
    INNER JOIN dms_acknowledgementPreferences P on P.apr_contact_id = civ_contact_id
    LEFT JOIN `dms_acknowledgement` A ON  T.`trns_id` = A.`ack_trns_id`
WHERE 
    trns_date_received >= '$fromDate' 
    AND T.`trns_amount_received` > $floorContributionAmount
    AND P.apr_must_acknowledge = 'Y'
    AND A.`ack_date` IS NULL 
    AND T.`trns_dnr_acknowledged` = 'N' 
ORDER BY 
    trns_amount_received DESC, 
    trns_date_received ASC;";
$GLOBALS['functions']->showSql($sql);
$records = $GLOBALS['functions']->GetDataFromSQL($sql);
$ackRows = '<tr><td>No Transactions Found<td></tr>';
if (!empty($records)) {
    $ackRows = '';
    foreach ($records as $k=>$v) {
        $ackRows .= '<tr>';
        $ackRows .= '<td><input type="checkbox" name="trxns[]" value="'.$v['trns_id'].'" /></td>';
        $ackRows .= '<td><a href="load.activity.php?d=' . $v['trns_dnr_no'].'&s=civicrm" target="_blank">' . $v['trns_dnr_no'].'</a></td>';
        $ackRows .= '<td>' . $v['dnr_name'].'</td>';
        $ackRows .= '<td>' . $v['trns_date_received'].'</td>';
        $ackRows .= '<td align="right" style="padding-right: 5px;"> R ' . number_format($v['trns_amount_received'],2,'.',' ').'</td>';
        $ackRows .= '</tr>';
    }
}

echo $ackRows;