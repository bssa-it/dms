<?php

Class joomlauser {
    
    var $id;
    var $username;
    var $name;
    var $groups;
    
    function Load($userId) {
        
        $db                     = new database;
        $civiDbConfig           = $GLOBALS['joomlaDBConnectionDetails'];
        $db->username           = (string)$civiDbConfig->username;
        $db->password           = (string)$civiDbConfig->password;
        $db->host               = (string)$civiDbConfig->host;
        $db->database           = (string)$civiDbConfig->database;
        $db->connect(true);
        
        $this->loadUserDetails($userId,$db);
        $this->loadUserGroups($userId,$db);
        
        $db->disconnect();
    }
    
    function loadUserDetails($userId,$db){
        
        $sql = "SELECT `name`,`username`,`email` FROM `r25_users` WHERE `id` = $userId;";
        $result = $db->select($sql);
        if (!$result) {
    		return false;
    	} else {
    		$this->name = $result[0]['name'];
            $this->username = $result[0]['username'];
            $this->id = $userId;
    	}
        
    }
    
    function loadUserGroups($userId,$db){
        
        $sql = "SELECT group_id FROM r25_user_usergroup_map WHERE user_id = $userId";
        $result = $db->select($sql);
        if (!$result) {
    		return false;
    	} else {
    	    foreach ($result as $k) $this->groups[$k['group_id']] = $k['group_id'];
    	}
        
    }
}

?>