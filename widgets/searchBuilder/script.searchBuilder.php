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

#   CHECK IF USER HAS HIS/HER OWN SEARCH FIELDS  
if (empty($prompts)) {
    # IF USER WIDGET CONFIG FILE IS EMPTY, THEN LOAD THE contact/inc/search.items.xml LIST OF FIELDS
    $xmlFields = new SimpleXMLElement('contacts/inc/search.items.xml', null, true);
} else {
    # IF USER WIDGET CONFIG FILE IS NOT EMPTY, THEN LOAD THE USER'S DEFINED SEARCH FIELDS 
    $xmlFields = $prompts->item;
}

#   ADD SEARCH FIELDS TO THE WIDGET
$leftColumnInputs = '';
$rightColumnInputs = '';$tabIndex = 4;
$exclude ="/(srch_donorNumber|srch_donorDeleted|srch_region)/i";
foreach ($xmlFields as $item) {
    if ($tabIndex==12) break;
    if (preg_match($exclude,$item['id'])) continue;
    if ($item->widgetDefault=='Y') {
        switch ($item['id']) {
            case 'srch_bamRefNo':
                $newInput = '<input type="text" name="'.$item['id'].'" onchange="checkBamOnly();" id="';
                $newInput .= $item['id'].'" class="colInput" placeholder="'.$item->displayName.'" tabindex="'.$tabIndex.'" />';   
                break;
            default:
                $newInput = '<input type="text" name="'.$item['id'].'" id="';
                $newInput .= $item['id'].'" class="colInput" placeholder="'.$item->displayName.'" tabindex="'.$tabIndex.'" />';   
                break;
        }
        if ($tabIndex % 2 === 0) {
            /* left hand column */
            $leftColumnInputs .= $newInput;     
        } else {
            /* right hand column */
            $rightColumnInputs .=  $newInput;
        }
        $tabIndex++;
    }
}

#   ADD THE REGION SEARCH FIELD
$regions = $GLOBALS['functions']->GetRegionList();
$regionOpts = '';
if (!empty($regions)) {
    foreach ($regions as $r) {
        $regionOpts .= '<option value="'.$r['region_id'].'">'.$r['region_id']. ' ' .$r['region_name'].'</option>';
    }
}
$regionField = '<select id="srch_region" name="srch_region" class="colInput" tabindex="3" style="width:120px;font-size: 9pt;padding: 3px;margin-bottom:15px;">
                <option value="">Region</option>
                '.$regionOpts.'
            </select>';

#   ADD USER DEFAULTS FOR THE DATABASE, DELETED STATUS AND BAM ONLY SEARCH FIELDS.  
$isCiviChecked = ($_SESSION['dms_user']['config']['donorSearch']['defaultDatabase']=='civiDb') ? ' CHECKED':'';
$isArchChecked = ($_SESSION['dms_user']['config']['donorSearch']['defaultDatabase']=='archDb') ? ' CHECKED':'';

$dnrDeletedValues = array(''=>'Donor Deleted','A'=>'All','Y'=>'Yes','N'=>'No');
$dnrDeletedOpts = '';
foreach ($dnrDeletedValues as $k=>$v) {
    $selected = ($_SESSION['dms_user']['config']['donorSearch']['defaultDonorDeleted']==$k) ? ' SELECTED':'';  
    $dnrDeletedOpts .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}

$isBamOnly = ($_SESSION['dms_user']['config']['donorSearch']['defaultBamOnly']=='Y') ? ' CHECKED':'';

?>