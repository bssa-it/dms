<?php

/**
 * @description
 * This script loads the donor edit form.
 * 
 * @author      Chezre Fredericks
 * @date_created 15/07/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

#   CHECK SUPERGLOBALS FOR VITAL VARIABLES. IF NOT FOUND EXIT
if (!isset($_GET['d'])||empty($_GET['d'])) {
    echo 'not found';
    exit();
}

#   SET VITAL VARIABLES
$dnrNo = $_GET['d'];

#   LOAD DONOR DETAIL
$dnr = $GLOBALS['functions']->getAPIContactRecordFromDonorNo($dnrNo);
$contactId = $dnr['contact_id'];
$phoneHeading = "New Phone Call with ".$dnr['display_name'];
$emailHeading = "New Email to " . $dnr['display_name'];

$optionGroupId = (string)$GLOBALS['xmlConfig']->activityconfig->civiOptionGroupId;
$allActivities = $GLOBALS['functions']->getCiviOptionValues($optionGroupId);

$activitiesCanEdit = $GLOBALS['xmlConfig']->activityconfig->userActivities->activity;
$activityTypeOpts = '';
$currentActivity = (!empty($_GET['a'])) ? $_GET['a']:null;
$suppliedActivityType = (!empty($_GET['t'])) ? $_GET['t']:null;

$optionGroupId = (string)$GLOBALS['xmlConfig']->activityconfig->civiStatusOptionGroupId;
$activityStatuses = $GLOBALS['functions']->getCiviOptionValues($optionGroupId);
$details = $details_phone = $details_email = '';
$activityDateTime = $activityDateTime_phone = $activityDateTime_email = ''; 
$subject = $location = '';
$assignedTo_phone = $assignedTo_phoneName = $assignedTo_email = $assignedTo_emailName =  '';
$status = $status_phone = $status_email = '';
$aTypeId = null;
$meetingHeading =  'New Meeting with ' . $dnr['display_name'];
$with_contacts = '';
if (!empty($currentActivity)) {
    $activity = $GLOBALS['functions']->getActivity($currentActivity);
    $activityIdInput = '<input type="hidden" name="id-'.$activity['activity_type_id'].'" value="'.$currentActivity.'" />';
    $meetingRows = '';
    if (!empty($activity['contacts']['with_contact_id'])) {
        $with_contacts = '';
        foreach ($activity['contacts']['with_contact_id'] as $c) {
            $d = $GLOBALS['functions']->getCiviContact($c);
            $with_contacts .= "\n" . '<input type="hidden" name="with_contact_id[]" value="' . $d['external_identifier'] . '" />';
        }
    }
    $opts = '';
    if (!empty($activity)) {
        switch ($activity['activity_type_id']) {
            case 1:
            case 49:
                $aTypeId = $activity['activity_type_id'];
                $meetingHeading =  'Meeting with ' . $dnr['display_name'];
                $with_contacts = '';
                $details = (!empty($activity['details'])) ? strip_tags($activity['details']):'';
                $activityDateTime = date("Y-m-d\TH:i",strtotime($activity['activity_date_time']));
                $location = $activity['location'];
                $subject = $activity['subject'];
                if (empty($activity['contacts']['assigned_to'])) {
                    $meetingRows .= '<tr><td>Assigned To</td><td colspan="2"><input type="text" name="assigned_to[]" placeholder="donor no" /><div class="addDiv">+ add</div></td></tr>';
                } else {
                    foreach ($activity['contacts']['assigned_to'] as $i=>$t) {
                        $d = $GLOBALS['functions']->getCiviContact($t);
                        $meetingRows .= '<tr><td>Assigned To</td><td colspan="2"><input type="text" name="assigned_to[]" placeholder="donor no" value="'.$d['external_identifier'].'" /><div class="assignedName">'.$d['display_name'].'</div><div class="addDiv">+ add</div></td></tr>';
                    }   
                }
                if (empty($activity['contacts']['with_contact_id'])) {
                    $meetingRows .= '<tr><td>Add Another Contact</td><td colspan="2"><input type="text" name="with_contact_id[]" placeholder="donor no" /><div class="addDiv">+ add</div></td></tr>';
                } else {
                    foreach ($activity['contacts']['with_contact_id'] as $i=>$t) {
                        $d = $GLOBALS['functions']->getCiviContact($t);
                        $meetingRows .= '<tr style="display:none"><td>Contact</td><td colspan="2"><input type="text" name="with_contact_id[]" placeholder="donor no" value="'.$d['external_identifier'].'" /><div class="addDiv">+ add</div></td></tr>';
                    }   
                }
                foreach ($activityStatuses as $as) {
                    $selected = ($activity['status_id']==$as['value']) ? ' SELECTED':'';
                    $opts .= '<option value="'.$as['value'].'"'.$selected.'>'.$as['label'].'</option>';
                }                
                $status = '<tr><td>Status</td><td><select name="status_id" id="status_id">'.$opts.'</select></td></tr>';
                break;
            case 2:
                $details_phone = (!empty($activity['details'])) ? strip_tags($activity['details']):'';
                $activityDateTime_phone = date("Y-m-d\TH:i",strtotime($activity['activity_date_time']));
                $phoneNos = '<option value="">-- select --</option>';
                if (!empty($activity['contacts']['with_contact_id'][0])) {
                    $phones = $GLOBALS['functions']->getCiviContactPhoneNos($activity['contacts']['with_contact_id'][0]);
                    $pContact = $GLOBALS['functions']->getCiviContactByQuery($activity['contacts']['with_contact_id'][0]);
                    $phoneHeading = "Please call " . $pContact['display_name'] . " on " . $activity['subject'];
                } else {
                    $phones = $GLOBALS['functions']->getCiviContactPhoneNos($contactId);
                }
                if (!empty($phones)) {
                    foreach ($phones as $p) {
                        if (empty($activity['phone_id'])) {
                            $selected = ($activity['subject']==$p['phone']) ? ' SELECTED':'';
                        } else {
                            $selected = ($p['id']==$activity['phone_id']) ? ' SELECTED':'';   
                        }
                        $phoneNos .= '<option value="'.$p['id'].'"'.$selected.'>'.$p['phone'].'</option>';
                    }
                }
                $assignedTo_phone_id = (empty($activity['contacts']['assigned_to'][0])) ? $activity['source_contact_id'] : $activity['contacts']['assigned_to'][0];
                $d = $GLOBALS['functions']->getCiviContact($assignedTo_phone_id);
                $assignedTo_phone = $d['external_identifier'];
                $assignedTo_phoneName = $d['display_name'];
                foreach ($activityStatuses as $as) {
                    $selected = ($activity['status_id']==$as['value']) ? ' SELECTED':'';
                    $opts .= '<option value="'.$as['value'].'"'.$selected.'>'.$as['label'].'</option>';
                }                
                $status_phone = '<tr><td>Status</td><td><select name="status_id_phone" id="status_id_phone">'.$opts.'</select></td></tr>';
                break; 
            case 3:
                $details_email = (!empty($activity['details'])) ? strip_tags($activity['details']):'';
                $activityDateTime_email = date("Y-m-d\TH:i",strtotime($activity['activity_date_time']));
                $emails = '<option value="">-- select --</option>';
                if (!empty($activity['contacts']['with_contact_id'][0])) {
                    $emailAddresses = $GLOBALS['functions']->getCiviContactEmailAddresses($activity['contacts']['with_contact_id'][0]);
                    $pContact = $GLOBALS['functions']->getCiviContactByQuery($activity['contacts']['with_contact_id'][0]);
                    $emailHeading = "Please email " . $pContact['display_name'] . " on " . $activity['subject'];
                } else {
                    $emailAddresses = $GLOBALS['functions']->getCiviContactEmailAddresses($contactId);
                }
                if (!empty($emailAddresses)) {
                    foreach ($emailAddresses as $e) {
                        $suppliedEmail = (empty($activity['subject'])) ? '':$activity['subject'];
                        $selected = ($e['emailAddress']==$suppliedEmail) ? ' SELECTED':'';
                        $emails .= '<option value="'.$e['id'].'"'.$selected.'>'.$e['emailAddress'].'</option>';
                    }
                }
                $assignedTo_email_id = (empty($activity['contacts']['assigned_to'][0])) ? $activity['source_contact_id'] : $activity['contacts']['assigned_to'][0];
                $d = $GLOBALS['functions']->getCiviContact($assignedTo_email_id);
                $assignedTo_email = $d['external_identifier'];
                $assignedTo_emailName = $d['display_name'];
                foreach ($activityStatuses as $as) {
                    $selected = ($activity['status_id']==$as['value']) ? ' SELECTED':'';
                    $opts .= '<option value="'.$as['value'].'"'.$selected.'>'.$as['label'].'</option>';
                }                
                $status_email = '<tr><td>Status</td><td><select name="status_id_email" id="status_id_email">'.$opts.'</select></td></tr>';
                break; 
        }
        
        foreach ($activitiesCanEdit as $ace) {
            $valueId = (int)$ace['value'];
            $desc = 'unknown';
            $selected = ($valueId==$activity['activity_type_id']) ? ' SELECTED':'';
            $displayTable[$valueId] = ($valueId==$activity['activity_type_id']) ? '':'style="display:none"';
            
            foreach ($allActivities as $a) {
                if ($a['value']==$ace['value']) {
                    $desc = $a['label'];
                    break;   
                } 
            }
            $activityTypeOpts .= '<option value="' . $ace['value'] . '"'.$selected.'>' . $desc . '</option>'; 
        }
        
    }
    $calendar = '&nbsp;';
    $taskList = '&nbsp;';
} else {
    $calendar = '
        <script type="text/javascript" language="javascript">
        $.get( "/dms/zimbra.soap.get.calendars.php")
            .done(function( data ) {
                $( "#calendarsDiv" ).empty().append( data );
            });
        </script>
        <div id="calendarDivHeading"><img src="/dms/img/calendar.gif" style="vertical-align: text-bottom;margin-right: 5px;" /> Add to Calendar <input type="checkbox" name="addZimbraCalendar" id="chkZimbraCalendar" CHECKED /></div>
        <div id="calendarsDiv">no calendars</div>
        ';
    $taskList = '
        <script type="text/javascript" language="javascript">
        $.get( "/dms/zimbra.soap.get.taskLists.php")
            .done(function( data ) {
                $( "#taskListsDiv" ).empty().append( data );
            });
        </script>
        <div id="taskDiv" style="display:none"><div id="taskListsDivHeading"><img src="/dms/img/checklist.png" style="vertical-align: text-bottom;margin-right: 5px;" width="16" height="16" /> Add to Task List <input type="checkbox" name="addZimbraTask" id="chkZimbraTask" CHECKED /></div>
        <div id="taskListsDiv">no task lists</div></div>
        ';
    $activityIdInput = '';
    $meetingRows = '<tr><td>Assigned To</td><td colspan="2"><input type="text" name="assigned_to[]" placeholder="donor no" /><div class="addDiv">+ add</div></td></tr>
            <tr><td>Add Another Contact</td><td colspan="2"><input type="text" name="with_contact_id[]" placeholder="donor no" /><div class="addDiv">+ add</div></td></tr>';
}

if (empty($activityTypeOpts)) {
    foreach ($activitiesCanEdit as $ace) {
        $valueId = (int)$ace['value'];
        $desc = 'unknown';
        if (empty($suppliedActivityType)) {
            $displayTable[$valueId] = ($ace['default']=='Y') ? '':'style="display:none"';
        } else {
            $displayTable[$valueId] = ($ace['value']==$suppliedActivityType) ? '':'style="display:none"';   
        }
        foreach ($allActivities as $a) {
            if ($a['value']==$ace['value']) {
                $desc = $a['label'];
                break;   
            } 
        }
        $activityTypeOpts .= '<option value="' . $ace['value'] . '">' . $desc . '</option>'; 
    }
}


if (empty($phoneNos)) {
    $phoneNos = '<option value="">-- select --</option>';
    $phones = $GLOBALS['functions']->getCiviContactPhoneNos($contactId);
    if (!empty($phones)) {
        foreach ($phones as $p) {
            $selected = ($p['isPrimary']) ? ' SELECTED':'';
            $phoneNos .= '<option value="'.$p['id'].'"'.$selected.'>'.$p['phone'].'</option>';
        }
    }   
}

if (empty($emails)) {
    $emails = '<option value="">-- select --</option>';
    $emailAddresses = $GLOBALS['functions']->getCiviContactEmailAddresses($contactId);
    if (!empty($emailAddresses)) {
        foreach ($emailAddresses as $e) {
            $selected = ($e['isPrimary']) ? ' SELECTED':'';
            $emails .= '<option value="'.$e['id'].'"'.$selected.'>'.$e['emailAddress'].'</option>';
        }
    }   
}

$withContactIdCurrent = ($contactId==$_SESSION['dms_user']['civ_contact_id']) ? '':'<input type="hidden" name="with_contact_id[]" value="'.$dnrNo.'" />';

#   SHOW THE HTML
require('html/'.$curScript.'.htm');