<?php

/**
 * @description
 * This script creates the form to edit the donor search widget details.
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

#   CREATE HTML
?>
<style type="text/css">
    #frmDnrSearchSettings table {
        font-size: 16pt;
        font-weight: bold;
    }
    #frmDnrSearchSettings input {
        border: 3px solid #254B7C;
        padding: 3px;
        width:  25px;
        font-size: 14pt;
    }
</style>
<script type="text/javascript" language="javascript">
    function saveDnrSearchSettings(){
        var widName = document.getElementById('wid_name');
        if (widName.value.trim().length==0) {
            alert('Please enter your Widget\'s name');
            widName.focus();
            return;
        }
        document.frmDnrSearchSettings.submit();
    }
</script>
<form action="save.dnrsearch.settings.php" method="POST" name="frmDnrSearchSettings" id="frmDnrSearchSettings">
    <input type="hidden" id="wid_id" name="wid_id" value="<?php echo $w->wid_id; ?>" />
    <table width="800" align="left" cellpadding="5" cellspacing="0">
        <tr>
            <td>Widget Title</td>
            <td><input type="text" id="wid_name" name="wid_name" value="<?php echo $w->wid_name; ?>" style="width: 400px;margin-left: 15px;" /></td>
        </tr>
        <tr>
            <td colspan="2"><div class="btn" onclick="saveDnrSearchSettings()">Save</div></td>
        </tr>
    </table>
</form>