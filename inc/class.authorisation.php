<?php

/**
 * @author Chezre Fredericks
 * @copyright 2013
 */

class authorisation {
    
    public $accessOk;
    public $isHo;
    public $isSuperUser;
    public $userGroups;
    public $userId;
    
    function __construct($user=null){
        
        $this->userGroups = $user->groups;
        $this->userId = $user->id;
        $this->isSuperUser = $this->accessOk = $this->isHo = false;
        
        $userConfigFile = '/var/www/joomla/dms/users/'.$this->userId.'.user.xml';
        if (!file_exists($userConfigFile)) return;
        
        $this->isSuperUser = (in_array($GLOBALS['xmlConfig']->joomlaGroups->superUser, $this->userGroups));
        if (!$this->isSuperUser) {
            $this->accessOk = (in_array($GLOBALS['xmlConfig']->joomlaGroups->dms, $this->userGroups));
            $this->isHo = (in_array($GLOBALS['xmlConfig']->joomlaGroups->ho, $this->userGroups));
        } else {
            $this->accessOk = $this->isHo = true;
        }
        
        $userConfig = simplexml_load_file($userConfigFile);
        foreach ($userConfig->permissions->children() as $e) {
            foreach ($e->children() as $p) $permission[$p->getName()] = ((string)$p=='Y');
            $this->{$e->getName()} = $permission;
        } 
    }
    
    function printPermissions(){
        echo 'Permissions for: ' . $_SESSION['dms_user']['fullname'] . ' (Joomla User Id: ' . $_SESSION['dms_user']['userid'] . ') <pre />';
        print_r($this);
        /*echo '<br /><br />Groups';
        if (!is_null($this->userGroups)) foreach ($this->userGroups as $v) echo '<br />'.$v;*/
    }
}