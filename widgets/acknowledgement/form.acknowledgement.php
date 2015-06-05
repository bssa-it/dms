<?php

/**
 * @description
 * This script loads the current user settings for the acknowledgement widget.
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
 
#   LOAD USER CONFIG SETTINGS
$myConfigFilename = "users/".$_SESSION['dms_user']['userid'].".user.xml";
$myConfig = simplexml_load_file($myConfigFilename); 

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

$impersonateOpts = '';
$currentEmail = '';
foreach ($myConfig->acknowledgement->departments->department as $dpt) {
    $selected = ($dpt['impersonate']=='Y') ? ' SELECTED':'';
    if (empty($currentEmail)) $currentEmail = ($dpt['impersonate']=='Y') ? $dpt['code'] : '';
    $impersonateOpts .= '<option value="'.$dpt['code'].'"'.$selected.'>'.$dpt['code']. ' - '.$dpt->secretary .'</option>';    
}

?>
<style type="text/css">
    #frmAcknowledgementSettings table {
        font-size: 11pt;
    }
    #frmAcknowledgementSettings input {
        width:  20px;
    }
    #frmAcknowledgementSettings li {
         list-style:none;
    }
    #wid_name {
        border: 3px solid #254B7C;
        padding: 3px;
        margin-left: 15px;
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
        /*border: 2px solid #254B7C;*/
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
    function saveAckSettings(){
        var widName = document.getElementById('wid_name');
        if (widName.value.trim().length==0) {
            alert('Please enter your Widget\'s name');
            widName.focus();
            return;
        }
        if (countChks()==0) {
            alert('Please select at least 1 department');
            return;
        }
        document.frmAcknowledgementSettings.submit();
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
<form action="widgets/acknowledgement/save.acknowledgement.settings.php" method="POST" id="frmAcknowledgementSettings" name="frmAcknowledgementSettings">
    <input type="hidden" id="currentEmailDpt" value="<?php echo $currentEmail; ?>" />
    <input type="hidden" id="wid_id" name="wid_id" value="<?php echo $w->wid_id; ?>" />
    <table width="98%" align="left" cellpadding="5" cellspacing="0">
        <tr>
            <td style="font-size: 16pt;font-weight: bold;">Widget Title</td>
            <td colspan="2"><input type="text" id="wid_name" name="wid_name" value="<?php echo $w->wid_name; ?>" style="width: 400px;" /></td>
        </tr>
        <tr>
            <td colspan="3" style="font-size: 12pt;font-weight: bold;">Select Departments </td>
        </tr>
        <tr>
            <td colspan="3" valign="top" style="font-size: 9pt;border: 2px solid #254B7C">
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
            <td colspan="3"><div class="btn" onclick="saveAckSettings()" >Save</div></td>
        </tr>
    </table>
</form>