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
 * 13/05/2014 - Chezre Fredericks:
 * File created
 * 
 */

$myXml = new SimpleXMLElement('widgets/'.$w->wid_directory.'/'.$w->wid_xmlFilename,null,true); 
$mySearchFields = (empty($myXml)) ? array():$myXml->item;

$xmlFields = new SimpleXMLElement('contacts/inc/search.items.xml', null, true);
$searchFields = '';
foreach ($xmlFields as $item) 
    $allFields[(string)$item->displayGroup][(string)$item['id']] = array((string)$item->displayName,(string)$item->widgetDefault);
    
$compulsory ="/(srch_donorNumber|srch_donorDeleted|srch_region)/i"; 
$exclude ="/(srch_group)/i";
foreach ($allFields as $k=>$group) {
    $searchFields .= '<div class="searchGroupingDiv">';
    $searchFields .= "\n\t".'<div class="searchGroupingHeadingDiv">'.$k.'</div>';
    foreach ($group as $fieldId=>$fieldDesc) {
        if (preg_match($exclude,$fieldId)) continue;
        if (empty($mySearchFields)) {
            $checked = ($fieldDesc[1]==='Y') ? ' CHECKED':'';
        } else {
            foreach ($mySearchFields as $fld) {
                $checked = (preg_match($compulsory,$fieldId)) ? ' CHECKED':'';
                if ($fld['id']==$fieldId) {
                    $checked = ' CHECKED';
                    break;    
                }
            } 
        }
        $isDefault = ($fieldDesc[1]==='Y') ? ' &#42;':'';
        $isHidden = (preg_match($compulsory,$fieldId)) ? ' DISABLED':'';
        $searchFields .= "\n\t" . '<input type="checkbox" name="dnrSearchFields[]" id="'.$fieldId.'" value="'.$fieldId.'" ';
        $searchFields .= $checked.$isHidden.' onchange="updateSelectedTotal();"  /> <label for="'.$fieldId.'">'.$fieldDesc[0].' '.$isDefault.'</label><br />';
    }
    $searchFields .= '</div>';
}

$isCiviChecked = ($_SESSION['dms_user']['config']['donorSearch']['defaultDatabase']=='civiDb') ? ' CHECKED':'';
$isArchChecked = ($_SESSION['dms_user']['config']['donorSearch']['defaultDatabase']=='archDb') ? ' CHECKED':'';

$dnrDeletedValues = array(''=>'-- select --','A'=>'All','Y'=>'Yes','N'=>'No');
$dnrDeletedOpts = '';
foreach ($dnrDeletedValues as $k=>$v) {
    $selected = ($_SESSION['dms_user']['config']['donorSearch']['defaultDonorDeleted']==$k) ? ' SELECTED':'';  
    $dnrDeletedOpts .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}

$isBamOnly = ($_SESSION['dms_user']['config']['donorSearch']['defaultBamOnly']=='Y') ? ' CHECKED':'';

?>
<style type="text/css">
    #frmDonorSearchSettings input {
        border: 3px solid #254B7C;
        padding: 3px;
        width:  25px;
    }
    #wid_name {
        font-size: 14pt;
    }
    .searchGroupingDiv {
        float:left;
        padding: 10px;
        width: 200px;
        min-height: 180px;
        height: auto !important;
    }
    .searchGroupingHeadingDiv {
        font-weight: bold;
        padding-bottom: 5px;
        border-bottom: 3px solid #254B7C;
        margin-bottom: 5px;
        font-size: 13pt;
    }
</style>
<script type="text/javascript" language="javascript">
    function validate() { 
        var widTitle = document.getElementById('wid_name');
        if (widTitle.value.trim().length==0) {
            alert('Please provide your widget with a title');
            widTitle.focus();
            return;
        }
        var chkd = countSelectedFields();
        if (chkd!=11) {
            alert('Please select 8 fields to use in your donor search box');
            return;
        }
        document.frmDonorSearchSettings.submit();
    }
    function countSelectedFields() {
        var chkd = 0;
        var dnrSearchFldsLength = document.frmDonorSearchSettings["dnrSearchFields[]"].length;
        for (var i=0;i<dnrSearchFldsLength;i++) if (document.frmDonorSearchSettings["dnrSearchFields[]"][i].checked) chkd++;
        return chkd;
    }
    function updateSelectedTotal() {
        var cntSpan = document.getElementById('selectedFieldCnt');
        cntSpan.innerHTML = countSelectedFields()-3;
    }
</script>
<form action="widgets/searchBuilder/save.searchbuilder.settings.php" method="POST" id="frmDonorSearchSettings" name="frmDonorSearchSettings">
    <input type="hidden" id="wid_id" name="wid_id" value="<?php echo $w->wid_id; ?>" />
    <div style="font-size: 20pt;font-weight: bold;margin-bottom:20px;">
        Widget Title 
        <input type="text" id="wid_name" name="wid_name" value="<?php echo $w->wid_name; ?>" style="width: 250px;margin-left: 15px;" />
    </div>
    <div id="selectedFieldCntDiv" style="font-size: 11pt"><span id="selectedFieldCnt" style="font-weight: bold;color: red;">8</span> fields selected</div>
    <?php echo $searchFields; ?>
    <div class="searchGroupingDiv" style="background-color: rgba(66,174,194,0.1);font-size: 9pt">
        <div class="searchGroupingHeadingDiv">Defaults</div>
        Donor Deleted
        <select name="dnrDeleted" id="dnrDeleted" style="width: 65px;font-size: 8pt;">
            <?php echo $dnrDeletedOpts; ?>
        </select>
        <br /><input type="radio" name="db" id="civiDb" value="civiDb" <?php echo $isCiviChecked; ?> /> <label for="civiDb">CiviCRM</label>
        <input type="radio" name="db" id="archDb" value="archDb" <?php echo $isArchChecked; ?> /> <label for="archDb">Archive</label>
        <br /><input type="checkbox" name="bamOnly" id="bamOnly" value="bamOnly" <?php echo $isBamOnly; ?> /> <label for="bamOnly">BAM Only</label>
    </div>
    <div class="btn" style="clear: left;margin-bottom: 20px;" onclick="validate();">Save</div>
</form>