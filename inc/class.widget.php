<?php

Class widget { 

var $wid_id;
var $wid_name;
var $wid_xmlFilename;
var $wid_isTemplate;
var $wid_editForm;
var $wid_display;
var $wid_script;
var $wid_directory;

function Load($id) {

    $sql = "SELECT * FROM `dms_widget` where `wid_id` = $id";
    $result = $GLOBALS['db']->select($sql);
    if (!$result) {
    	return false;
    } else {
    	foreach ($result[0] as $k => $v) {
    		$this->$k = stripslashes($v);
    	}
    	return true;
    }
}
	
function MySqlEscape() {
    foreach ($this as $k => $v) {
    	$this->$k = mysql_real_escape_string($v,$GLOBALS['db']->connection);
    }
}

function Save() {
	
	$this->MySqlEscape();
	if (isset($this->wid_id) && !empty($this->wid_id)) {
	$sql = "
            UPDATE `dms_widget` SET
              `wid_name` = '$this->wid_name',
              `wid_xmlFilename` = '$this->wid_xmlFilename',
              `wid_isTemplate` = '$this->wid_isTemplate',
              `wid_editForm` = '$this->wid_editForm',
              `wid_display` = '$this->wid_display',
              `wid_script` = '$this->wid_script',
              `wid_directory` = '$this->wid_directory'
            WHERE
                `wid_id` = '$this->wid_id';";
    } ELSE {
    $sql = "
            INSERT INTO `dms_widget` (
              `wid_name`,
              `wid_xmlFilename`,
              `wid_isTemplate`,
              `wid_editForm`,
              `wid_display`,
              `wid_script`,
              `wid_directory`
    ) VALUES (
              '$this->wid_name',
              '$this->wid_xmlFilename',
              '$this->wid_isTemplate',
              '$this->wid_editForm',
              '$this->wid_display',
              '$this->wid_script',
              '$this->wid_directory'
    );";
    }
    $result = $GLOBALS['db']->execute($sql);
    $this->wid_id = (empty($this->wid_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->wid_id;
	
}

function copyTemplate($widgetName,$jmlUserId) {
    
    $sql = "SELECT * FROM `dms_widget` WHERE `wid_directory` = '$widgetName' AND `wid_xmlFilename` = '$jmlUserId.$widgetName.xml'";
    $result = $GLOBALS['db']->select($sql);
    if (!$result) {
    	$sql = "SELECT wid_name,REPLACE(`wid_xmlFilename`,'tmpl','$jmlUserId') `wid_xmlFilename`,'N' `wid_isTemplate`,`wid_editForm`,wid_display,wid_script,wid_directory FROM `dms_widget` where `wid_directory` = '$widgetName' and wid_isTemplate = 'Y'";
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
            return false;
        } else {
            foreach ($result[0] as $k => $v) {
                $this->$k = stripslashes($v);
            }
            $this->wid_id = null;
            $this->Save();
            return true;
        }
    } else {
    	foreach ($result[0] as $k => $v) {
            $this->$k = stripslashes($v);
    	}
        return true;
    }
}

# end class	
}