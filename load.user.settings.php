<?php

/**
 * @description
 * This script loads the user config settings editor.
 * 
 * @author      Chezre Fredericks
 * @date_created 12/05/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

#   LOAD USER CONFIG SETTINGS
$myConfigFilename = "users/".$_SESSION['dms_user']['userid'].".user.xml";
$myConfig = simplexml_load_file($myConfigFilename); 
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

#   LIST ALL DEPARTMENTS AND CHECK SELECTED DEPARTMENTS
$allDepartments = $GLOBALS['functions']->GetDepartmentList();
$departmentChks = '';
$cnt = 0;
foreach ($allDepartments as $k=>$v) {
    $checked = '';
    foreach ($myConfig->acknowledgement->departments->department as $dpt) {
           $d = (string)$dpt['code'];
           if ($v['dep_id']==$d) {
               $checked = ' CHECKED';
               break;
           }
    }
    $departmentChks .= '<div class="dptChckDiv"><input type="checkbox" name="departments[]" id="dp'.$v['dep_id'];
    $departmentChks .= '" alt="'.$v['dep_id']. ' - '.$v['dep_name'].'" value="'.$v['dep_id'];
    $departmentChks .= '" onchange="addDepartmentToImpersonate()"'.$checked.' />';
    $departmentChks .= '<label for="dp'.$v['dep_id'].'"> '.$v['dep_id']. ' - '.$v['dep_name'].'</label>&nbsp;</div>';
    $cnt++;
}

#   POPULATE THE PRIMARY DEPARTMENT
$impersonateOpts = '';
$currentEmail = '';
foreach ($myConfig->acknowledgement->departments->department as $dpt) {
    $selected = ($dpt['impersonate']=='Y') ? ' SELECTED':'';
    if (empty($currentEmail)) $currentEmail = ($dpt['impersonate']=='Y') ? $dpt['code'] : '';
    $impersonateOpts .= '<option value="'.$dpt['code'].'"'.$selected.'>'.$dpt['code']. ' - '.$dpt->secretary .'</option>';    
}

#   SEARCH DEFAULTS
$isCiviChecked = ($myConfig->donorSearch->defaultDatabase=='civiDb') ? ' CHECKED':'';
$isArchChecked = ($myConfig->donorSearch->defaultDatabase=='archDb') ? ' CHECKED':'';

$dnrDeletedValues = array(''=>'-- none --','A'=>'All','N'=>'No','Y'=>'Yes');
$dnrDeletedOpts = '';
foreach ($dnrDeletedValues as $k=>$v) {
    $selected = ($myConfig->donorSearch->defaultDonorDeleted==$k) ? ' SELECTED':'';  
    $dnrDeletedOpts .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}

$isBamOnly = ($myConfig->donorSearch->defaultBamOnly=='Y') ? ' CHECKED':'';

?>
<style type="text/css">
    #frmUserSettings table {
        font-size: 11pt;
    }
    #frmUserSettings input {
        width:  15px;
    }
    #frmUserSettings td {
        padding: 5px;
    }
    #impersonateDpt {
        font-size: 11pt;
        color: #254B7C;
        font-weight: bold;
        background-color: rgba(37,75,124,0.1);
        padding: 3px;
    }
    #impersonateDiv {
        padding-top: 10px;
        clear:both;
        font-size: 12pt;
    }
    #searchDiv {
        width: 420px;
    }
    #defaultDonorDeleted {
        font-size: 9pt;
        color: #254B7C;
        background-color: rgba(37,75,124,0.1);
        padding: 3px;
        width: 100px;   
    }
    .dptChckDiv {
        width: 260px;
        float: left;
        font-size: 11pt;
    }
</style>
<script type="text/javascript" language="javascript">
    function saveUserSettings(){
        if (countChks()==0) {
            alert('Please select at least 1 department');
            return;
        }
        document.frmUserSettings.submit();
    }
    function checkAllDepts() {
        var sa = document.getElementById('chkAll');
        var chks = document.getElementsByName('departments[]');
        for (c=0;c<chks.length;c++) {
            chks[c].checked = sa.checked;
        }
        addDepartmentToImpersonate();
    }
    function countChks() {
        var chks = document.getElementsByName('departments[]');
        var total = 0;
        for (c=0;c<chks.length;c++) {
            if (chks[c].checked) total++;
        }
        return total;
    }
    function addDepartmentToImpersonate() {
        var x = document.getElementById("impersonateDpt");
        var curDpt = document.getElementById('currentEmailDpt');
        var xLen = x.length;
        for (var i=xLen; i>-1; i--) x.remove(i); 
        var chks = document.getElementsByName('departments[]');
        for (c=0;c<chks.length;c++) {
            if (chks[c].checked) {
                var option = document.createElement("option");
                option.text = chks[c].alt;
                option.value = chks[c].value;
                x.add(option); 
            }
        }
        if (x.length==0) curDpt.value = '';
        x.value = curDpt.value;
        if (x.length>0&&x.value.length==0) {
            x.value = x[0].value;
            curDpt.value = x.value;   
        }
    }
    function updateEmailDpt(el) {
        document.getElementById('currentEmailDpt').value = el.value;
    }
</script>
<form action="save.user.settings.php" method="POST" id="frmUserSettings" name="frmUserSettings">
    <input type="hidden" id="currentEmailDpt" value="<?php echo $currentEmail; ?>" />
    <table width="100%" align="left" cellpadding="5" cellspacing="0">
        <tr>
            <td style="font-size: 16pt;font-weight: bold;">My Detail<div class="btn" style="float:right;margin-right:0px" onclick="saveUserSettings()" >Save</div></td>
        </tr>
        <tr>
            <td style="background-color:#254B7C;color: #FFF;font-size: 12pt;font-weight: bold;">Acknowledgement Settings</td>
        </tr>
        <tr>
            <td  valign="top" style="font-size: 9pt;border: 2px solid #254B7C">
                <div><div style="background-color: rgba(37,75,124,0.1);padding:4px;font-weight: bold;"><input type="checkbox" id="chkAll" onclick="checkAllDepts();" /><label for="chkAll">CHECK ALL</label></div><?php echo $departmentChks; ?></div>
                <div id="impersonateDiv">
                    Primary Department: 
                    <select name="impersonateDpt" id="impersonateDpt" onchange="updateEmailDpt(this)">
                        <?php echo $impersonateOpts; ?>
                    </select>
                </div>
            </td> 
        </tr>
        <tr>
            <td style="background-color:#254B7C;color: #FFF;font-size: 12pt;font-weight: bold;">Search Settings</td>
        </tr>
        <tr>
            <td style="border: 2px solid #254B7C;">
                <div id="searchDiv">
                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 9pt;padding: 3px;">
                        <tr>
                            <td>Default Database</td>
                            <td>
                                <input type="radio" name="defaultDatabase" id="civiDb" value="civiDb"<?php echo $isCiviChecked; ?> /> <label for="civiDb">CiviCRM</label> 
                                <input type="radio" name="defaultDatabase" id="archDb" value="archDb"<?php echo $isArchChecked; ?> /> <label for="archDb">Archive</label>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td><td><input type="checkbox" name="defaultBamOnly" id="defaultBamOnly" value="Y"<?php echo $isBamOnly; ?> /><label for="defaultBamOnly">Search BAM records Only</label></td>
                        </tr>
                        <tr>
                            <td><label for="defaultDonorDeleted">Default Donor Deleted Status</label></td>
                            <td>
                                <select id="defaultDonorDeleted" name="defaultDonorDeleted">
                                    <?php echo $dnrDeletedOpts; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</form>