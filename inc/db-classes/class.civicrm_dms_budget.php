<?php

Class civicrm_dms_budget { 

        var $id;
	var $bud_region;
	var $bud_department;
	var $bud_category;
	var $bud_amount;
	var $bud_insert_user;
	var $bud_dateinserted;
	var $bud_datelastupdated;
	var $bud_update_user;
	

        function __construct($id=null) {
            if (!empty($id)) $this->Load($id);
        }

        function Load($id) {
            $sql = "SELECT * FROM `civicrm_dms_budget` where `id` = $id";
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->select($sql);
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
            foreach ($this as $k => $v) $this->$k = mysql_real_escape_string($v,$GLOBALS['civiDb']->connection);
        }

        function Save() {
            $this->MySqlEscape();
            if (isset($this->id) && !empty($this->id)) {
                $sql = "
                    UPDATE `civicrm_dms_budget` SET
                        `bud_region` = '$this->bud_region',
			`bud_department` = '$this->bud_department',
			`bud_category` = '$this->bud_category',
			`bud_amount` = '$this->bud_amount',
			`bud_insert_user` = '$this->bud_insert_user',
			`bud_dateinserted` = '$this->bud_dateinserted',
			`bud_datelastupdated` = '$this->bud_datelastupdated',
			`bud_update_user` = '$this->bud_update_user'
                    WHERE
                      `id` = '$this->id';";
            } ELSE {
                $sql = "
                    INSERT INTO `civicrm_dms_budget` (
                        `bud_region`,
			`bud_department`,
			`bud_category`,
			`bud_amount`,
			`bud_insert_user`,
			`bud_dateinserted`,
			`bud_datelastupdated`,
			`bud_update_user`
                    ) VALUES (
                        '$this->bud_region',
			'$this->bud_department',
			'$this->bud_category',
			'$this->bud_amount',
			'$this->bud_insert_user',
			'$this->bud_dateinserted',
			'$this->bud_datelastupdated',
			'$this->bud_update_user'
                    );";
            }
            $GLOBALS['functions']->showSql($sql);
            $result = $GLOBALS['civiDb']->execute($sql);
            $this->id = (empty($this->id)) ? mysql_insert_id($GLOBALS['civiDb']->connection) : $this->id;
        }

}