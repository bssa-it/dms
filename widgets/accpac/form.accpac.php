<?php

/**
 * @description
 * This script loads the current user settings for the announcements widget.
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * @version     5.0.1
 * 
 * @Changes
 * 18/05/2015 - Chezre Fredericks:
 * File created
 * 
 */

$xml = simplexml_load_file('widgets/accpac/otd.connection.xml');

?>
<style type="text/css">
    #wid_name {
        margin-left: 15px;
    }
    .frmLabel {
        float: left;
        width: 100px;
        font-size: 14pt;
        padding: 5px;
        clear: left;
    }
    .frmInput {
        float: left;
        width: 700px;
        padding: 5px;
    }
    #frmAccpacSettings input {
        padding: 2px;
        font-size: 12pt;
        border: 3px solid #254B7C;
    }
</style>
<script type="text/javascript" language="javascript">
    $("#btnSaveOtdSettings").click(function(){
        document.frmAccpacSettings.submit();
    });
</script>
<form action="widgets/accpac/save.accpac.settings.php" method="POST" id="frmAccpacSettings" name="frmAccpacSettings">
    <input type="hidden" name="wid_id" value="<?php echo $w->wid_id; ?>" />
    <table width="98%" align="left" cellpadding="5" cellspacing="0">
        <tr>
            <td style="font-size: 16pt;font-weight: bold;">Widget Title</td>
            <td colspan="2"><input type="text" id="wid_name" name="wid_name" value="<?php echo $w->wid_name; ?>" style="width: 400px;" /></td>
        </tr>
        <tr>
            <td colspan="3" style="font-size: 12pt;font-weight: bold;">MySQL settings</td>
        </tr>
        <tr>
            <td colspan="3" valign="top" style="font-size: 9pt;border: 2px solid #254B7C">
                <div class="frmLabel">Username</div><div class="frmInput"><input type="text" name="username" id="username" value="<?php echo (string)$xml->username; ?>" /></div>
                <div class="frmLabel">Password</div><div class="frmInput"><input type="password" name="password" id="password" value="<?php echo (string)$xml->password; ?>" /></div>
                <div class="frmLabel">Host</div><div class="frmInput"><input type="text" name="host" id="host" value="<?php echo (string)$xml->host; ?>" /></div>
                <div class="frmLabel">Database</div><div class="frmInput"><input type="text" name="database" id="database" value="<?php echo (string)$xml->database; ?>" /></div>
            </td> 
        </tr>
        <tr>
            <td colspan="3"><div class="btn" id="btnSaveOtdSettings" >Save</div></td>
        </tr>
    </table>
</form>