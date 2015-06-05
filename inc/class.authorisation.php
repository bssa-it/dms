<?php

/**
 * @author Chezre Fredericks
 * @copyright 2013
 */

class authorisation {
    
    public $canSelect = false;
    public $canUpdate = false;
    public $canInsert = false;
    public $canDelete = false;
    public $isAdmin = false;
    public $isSuperUser = false;
    public $userGroups = null;
    
    function getPermissions(){
       
        if (!isset($GLOBALS['authorisationGroups'])||empty($this->userGroups)||is_null($this->userGroups)) {
            $this->canSelect = false;
            $this->canUpdate = false;
            $this->canInsert = false;
            $this->canDelete = false;
            $this->isAdmin = false;
            $this->isSuperUser = false;
            return;   
        }
        
        foreach ($this->userGroups as $g) {
            if (!$this->canSelect) $this->canSelect = ($GLOBALS['authorisationGroups']['select']==$g);
            if (!$this->canUpdate) $this->canUpdate = ($GLOBALS['authorisationGroups']['update']==$g);      
            if (!$this->canInsert) $this->canInsert = ($GLOBALS['authorisationGroups']['insert']==$g);
            if (!$this->canDelete) $this->canDelete = ($GLOBALS['authorisationGroups']['delete']==$g);
            if (!$this->isAdmin) $this->isAdmin = ($GLOBALS['authorisationGroups']['admin']==$g);
            if (!$this->isSuperUser) $this->isSuperUser = ($GLOBALS['authorisationGroups']['superUser']==$g);
            if ($this->isSuperUser) {
                $this->canSelect = true;
                $this->canUpdate = true;
                $this->canInsert = true;
                $this->canDelete = true;
                $this->isAdmin = true;
                return;
            }
        }
         
    }
    
    function printPermissions(){
        echo 'Permissions for: ' . $_SESSION['dms_user']['fullname'] . ' (Joomla User Id: ' . $_SESSION['dms_user']['userid'] . ') <br />';
        echo '<br/>Can Select? -> ';
        echo (!$this->canSelect) ? 'No':'Yes';
        echo '<br/>Can Update? -> ';
        echo (!$this->canUpdate) ? 'No':'Yes';
        echo '<br/>Can Insert? -> ';
        echo (!$this->canInsert) ? 'No':'Yes';
        echo '<br/>Can Delete? -> ';
        echo (!$this->canDelete) ? 'No':'Yes';
        echo '<br/>Is Admin? -> ';
        echo (!$this->isAdmin) ? 'No':'Yes';
        echo '<br/>Is Super User? -> ';
        echo (!$this->isSuperUser) ? 'No':'Yes';
        echo '<br /><br />Groups';
        if (!is_null($this->userGroups)) foreach ($this->userGroups as $v) echo '<br />'.$v;
    }
}

?>