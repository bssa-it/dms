<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include_once("/var/www/joomla/dms/inc/db-classes/class.user.php");
class createUser extends user {
    var $nextStaffNo;
    function __construct() {
        $this->getNextStaffNo();
    }
    function getNextStaffNo() {
        $settings = simplexml_load_file('user.config.xml');
        $this->nextStaffNo = (int)$settings->nextStaffNo;
        $civiDb = $GLOBALS['civiDb']->database;
        $staffnoOk = false;
        
        while (!$staffnoOk) {
            $sql = "SELECT COUNT(*) `cnt` FROM $civiDb.civicrm_contact WHERE external_identifier = " . $this->nextStaffNo;
            $result = $GLOBALS['civiDb']->select($sql);
            if (!$result) {
                return null;
            } else {
                $staffnoOk = ((int)$result[0]['cnt']<1);
                if (!$staffnoOk) $this->nextStaffNo++;
            }
        }
    }
    function doesJoomlaUserExist($email) {
        $dbConnection = $GLOBALS['functions']->xml2array($GLOBALS['joomlaDBConnectionDetails']);
        $db = new database($dbConnection);
        $sql = "SELECT id FROM r25_users WHERE email = '$email';";
        $result = $db->select($sql);
        $db->close();
        if (!$result) {
            return false;
        } else {
            return $result[0]['id'];
        }
    }
    function createJoomlaUser($email,$name,$isAdmin=false,$isSuperUser=false){
        $emailParts = explode('@', $email);
        $username = $emailParts[0];
        $regDate = date("Y-m-d H:i:s");
        
        $dbConnection = $GLOBALS['functions']->xml2array($GLOBALS['joomlaDBConnectionDetails']);
        $db = new database($dbConnection);
        $sql = "INSERT INTO r25_users (`name`,`username`,`email`,`registerDate`) VALUES ('$name','$username','$email','$regDate');";
        $result = $db->execute($sql);
        $userId = mysql_insert_id($db->connection);
        
        if (!$result) {
            $db->close();
            return false;
        } else {
            $groups = $this->getJoomlaUserGroups($isAdmin,$isSuperUser);
            foreach ($groups as $g) {
                $sql = "INSERT INTO r25_user_usergroup_map (`user_id`,`group_id`) VALUES ($userId,$g);";
                $db->execute($sql);
            }
            $db->close();
            return $userId;
        }
    }
    
    function addJoomlaUserGroups($userId,$isAdmin=false,$isSuperUser=false,$groups=null) {
        
        $dbConnection = $GLOBALS['functions']->xml2array($GLOBALS['joomlaDBConnectionDetails']);
        $db = new database($dbConnection);
        if (empty($groups)) $groups = $this->getJoomlaUserGroups($isAdmin,$isSuperUser);
        foreach ($groups as $g) {
            $sql = "INSERT INTO r25_user_usergroup_map (`user_id`,`group_id`) VALUES ($userId,$g);";
            $db->execute($sql);
        }
        $db->close();
        return true;
    }
    
    function getJoomlaUserGroups($isAdmin=false,$isSuperUser=false) {
        $groups[] = (int)$GLOBALS['xmlConfig']->authorisation->registeredGroup;
        $groups[] = (int)$GLOBALS['xmlConfig']->authorisation->select;
        if ($isAdmin) $groups[] = (int)$GLOBALS['xmlConfig']->authorisation->admin;
        if ($isSuperUser) {
            $groups[] = (int)$GLOBALS['xmlConfig']->authorisation->admin;
            $groups[] = (int)$GLOBALS['xmlConfig']->authorisation->superUser;
        }
        return $groups;
    }
    
}