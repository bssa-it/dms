<?php

/**
 * @description
 * This script loads the activities for the contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/01/2014
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
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
$contactId = $dnr['contact_id'];
$isDeleted = ($dnr['contact_is_deleted']=='1');

$activities = $GLOBALS['functions']->getCiviContactActivities($contactId);
$optionGroupId = (string)$GLOBALS['xmlConfig']->activityconfig->civiStatusOptionGroupId;
$activityStatuses = $GLOBALS['functions']->getCiviOptionValues($optionGroupId);
$activitiesDontEdit = $GLOBALS['xmlConfig']->activityconfig->activitiesExcludedFromStatusUpdate->activity;

usort($activities, function($a, $b) {
    $aDate = new DateTime($a['activity_date_time']);
    $bDate = new DateTime($b['activity_date_time']);
    return $bDate>$aDate;
});

$activityRows = '<tr><td colspan="4">No interactions found</td></tr>';
$jqueryBindings = '';
if (!empty($activities)) {
    $activityRows = '';
    $comingSoonDivs = '';
    foreach ($activities as $a) {
        $today = new DateTime();
        $aDate = new DateTime($a['activity_date_time']);
        $sourceContact = $GLOBALS['functions']->getCiviContact($a['source_contact_id']);
        if ($today>$aDate||$a['status_id']==2||$a['status_id']==3) {
            $opts = '';
            $canEdit = true;
            foreach ($activitiesDontEdit as $ade) {
                if ($ade['value']==$a['activity_type_id']) {
                    $canEdit = false;
                    break;   
                }
            }
            foreach ($activityStatuses as $s) {
                $selected = ($a['status_id']==$s['value']) ? ' SELECTED':'';
                $opts .= '<option value="' . $s['value'] . '"'.$selected.'>'.$s['label'].'</option>';
            }
            $disabled = ($canEdit) ? '':' DISABLED';
            $class = ($canEdit) ? 'editable':'dsbld';
            $activityRows .= ($canEdit) ? "\n" . '<tr aid="' . $a['id'] . '" class="editActivity">':"\n" . '<tr>';
            $activityRows .= '<td>' . $a['activity_date_time'] . '</td>';
            $activityRows .= '<td>' . $a['activity_name'] . '</td>';
            $activityRows .= '<td>';
            $activityRows .= (empty($a['details'])) ? $a['subject'] : $a['details'];
            $activityRows .= '</td>';
            $activityRows .= '<td>'.$sourceContact['display_name'] .'</td>';
            $activityRows .= '<td><select id="id-' . $a['id'] . '" class="'.$class.'"'.$disabled.'>' .$opts. '</select></td>';
            $activityRows .= '</tr>';
            
            #$jqueryBindings .= "\n" . '$("#id-' . $a['id'] . '").bind("change",updateStatus);';   
        } else {
            
            $comingSoonDivs = '<div class="nextDateDiv" id="aId-'.$a['id'].'">
        <div class="dt">
            <div class="d">'.date("d",strtotime($a['activity_date_time'])).'</div>
            <div class="m">'.date("M",strtotime($a['activity_date_time'])).'</div>
            <div class="y">'.date("Y",strtotime($a['activity_date_time'])).'</div>
        </div>
        <div class="action">
            <div class="aType">'.$a['activity_name'].' <span class="t">'.date("H:i A",strtotime($a['activity_date_time'])).'</span></div>
            <div class="cont">Added By: <a href="load.activity.php?d='.$sourceContact['external_identifier'].'&s=civicrm" class="relationshipLink">'.$sourceContact['display_name'].'</a></div>
        </div>
    </div>'.$comingSoonDivs;
        }
    }
}
if (empty($comingSoonDivs)) $comingSoonDivs = '<span style="color: #FFF">No upcoming activities</span>';

#   ADD THE HTML
require('html/'.$curScript.'.htm');