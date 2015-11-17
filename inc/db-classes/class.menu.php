<?php

Class menu { 

    var $men_id;
    var $men_name;
    var $men_level;
    var $men_parent;
    var $men_requiredAuthorisation;
    var $men_destination;
    var $men_order;
    var $men_htmlId;
    var $men_active;
    var $men_module;
    var $men_permission;
    
    var $html;

    function Load($id) {

        $sql = "SELECT * FROM `dms_menu` where `men_id` = $id";
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
            if (isset($this->men_id) && !empty($this->men_id)) {
            $sql = "
                UPDATE `dms_menu` SET
                  `men_name` = '$this->men_name',
                  `men_level` = '$this->men_level',
                  `men_parent` = '$this->men_parent',
                  `men_requiredAuthorisation` = '$this->men_requiredAuthorisation',
                  `men_destination` = '$this->men_destination',
                  `men_order` = '$this->men_order',
                  `men_htmlId` = '$this->men_htmlId',
                  `men_active` = '$this->men_active',
                  `men_module` = '$this->men_module',
                  `men_permission` = '$this->men_permission'
                WHERE
                    `men_id` = '$this->men_id';";
        } ELSE {
        $sql = "
                INSERT INTO `dms_menu` (
                  `men_name`,
                  `men_level`,
                  `men_parent`,
                  `men_requiredAuthorisation`,
                  `men_destination`,
                  `men_order`,
                  `men_htmlId`,
                  `men_active`,
                  `men_module`,
                  `men_permission`
        ) VALUES (
                  '$this->men_name',
                  '$this->men_level',
                  '$this->men_parent',
                  '$this->men_requiredAuthorisation',
                  '$this->men_destination',
                  '$this->men_order',
                  '$this->men_htmlId',
                  '$this->men_active',
                  '$this->men_module',
                  '$this->men_permission'
        );";
        }
        $result = $GLOBALS['db']->execute($sql);
        $this->men_id = (empty($this->men_id)) ? mysql_insert_id($GLOBALS['db']->connection) : $this->men_id;

    }

    function __construct() {
        
        /**
         * This function creates a menu based on the logged in user's permissions 
         *
         * @param  none
         * @throws none
         * @return false on error;  an HTML menu for the logged in user. 
         */
        $sql = "SELECT * FROM `dms_menu` WHERE `men_active` = 'Y' ORDER BY `men_level`,`men_parent`,`men_order`;";
        $result = $GLOBALS['db']->select($sql);
    	if (!$result) {
    	    $this->html = ''; 
    	} else {
            $return = '<div id="navigationDiv">';
            $return .= "\n\t" . '<ul id="navigator">';
            foreach ($result as $m) {
                 $authorized = (empty($m['men_module'])) ? $_SESSION['dms_user']['authorisation']->$m['men_permission']:$_SESSION['dms_user']['authorisation']->{$m['men_module']}[$m['men_permission']];
                 if ($authorized&&$m['men_level']==0) {
                     $menuName = strtolower(substr($m['men_name'],0,1)).preg_replace('/ /','',substr($m['men_name'],1)).'Menu';
                     $subMenuName = strtolower(substr($m['men_name'],0,1)).preg_replace('/ /','',substr($m['men_name'],1)).'SubMenu';
                     $return .= "\n\t\t" . '<li';
                     $subMenu = null;
                     foreach ($result as $s) {
                         $authOk = (empty($s['men_module'])) ? $_SESSION['dms_user']['authorisation']->$s['men_permission']:$_SESSION['dms_user']['authorisation']->{$s['men_module']}[$s['men_permission']];
                         if ($authOk&&$s['men_level']==1&&$s['men_parent']==$m['men_id']) {
                             if (is_null($subMenu)) {
                                 $subMenu = ' onmouseover="' . "showSubMenu('$menuName','$subMenuName');";
                                 $subMenu .= '" onmouseout="' . "hideSubMenu('$subMenuName');" . '" id="'.$menuName.'">';
                                 $subMenu .= $m['men_name']."\n\t\t".'<div class="submenu" id="'.$subMenuName.'"><ul>';   
                             }
                             $subMenu .= "\n\t\t\t".'<li style="padding-right: 0px;"><a href="'.$s['men_destination'].'">'.$s['men_name'].'</a></li>'; 
                         }
                     }
                     $return .= (is_null($subMenu)) ? '><a href="'.$m['men_destination'].'">'.$m['men_name']."</a>\n\t\t</li>" : "$subMenu\n\t\t</ul></div>\n\t\t</li>";
                 } 
            }
            $return .= "\n\t</ul>\n</div>";

            /*  PERSONAL SETTINGS LINK */
            $return .= "\n\t" . '<img src="/dms/img/logout.png" title="log out" align="middle" id="imgLogout" />';
            $return .= '<div id="myConfigurationDiv"><img src="/dms/img/settings.png" width="16" height="16" alt="my configuration" /> ';
            $return .= $_SESSION['dms_user']['fullname'].'</div><div id="notificationLinkDiv"><img src="/dms/img/notification.png" width="16" height="16" /></div>';
            
            $this->html = $return;
            
    	}
    }

# end class	
}