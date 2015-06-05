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

$myXml = new SimpleXMLElement('widgets/'.$w->wid_directory. '/'.$w->wid_xmlFilename,null,true); 
$floorContributionAmount = (int)$myXml->contributionFloorValue;

?>
<style type="text/css">
    #frmBamAckSettings table {
        font-size: 16pt;
        font-weight: bold;
    }
    #frmBamAckSettings input {
        border: 3px solid #254B7C;
        padding: 3px;
        width:  25px;
        font-size: 14pt;
    }
</style>
<script type="text/javascript" language="javascript">
    function saveBamSettings(){
        var widName = document.getElementById('wid_name');
        if (widName.value.trim().length==0) {
            alert('Please enter your Widget\'s name');
            widName.focus();
            return;
        }
        var conFlrAmnt = document.getElementById('contributionFloorValue');
        if (conFlrAmnt.value.trim().length==0) {
            alert('Please enter the minimum value for contributions to show in this widget');
            conFlrAmnt.focus();
            return;
        }
        document.frmBamAckSettings.submit();
    }
</script>
<form action="widgets/bamacknowledgement/save.bamacknowledgement.settings.php" method="POST" name="frmBamAckSettings" id="frmBamAckSettings">
    <input type="hidden" id="wid_id" name="wid_id" value="<?php echo $w->wid_id; ?>" />
    <table width="800" align="left" cellpadding="5" cellspacing="0">
        <tr>
            <td>Widget Title</td>
            <td><input type="text" id="wid_name" name="wid_name" value="<?php echo $w->wid_name; ?>" style="width: 400px;margin-left: 15px;" /></td>
        </tr>
        <tr>
            <td>Show contributions greater than</td>
            <td><input type="number" value="<?php echo $floorContributionAmount; ?>" name="contributionFloorValue" id="contributionFloorValue" style="width: 400px;margin-left: 15px;"  /></td>
        </tr>
        <tr>
            <td colspan="2"><div class="btn" onclick="saveBamSettings()">Save</div></td>
        </tr>
    </table>
</form>