<?php

Class database {

	var $username;
	var $password;
	var $host;
	var $database;
	var $connection;
        
        function __construct($connDetails=null) {
            if (!empty($connDetails)) {
                foreach ($connDetails as $k=>$v) $this->$k = $v;
                $this->connect(true);
            }
        }
        
	function connect($newLink=false) {
		$this->connection = mysql_connect($this->host,$this->username,$this->password,$newLink);
		mysql_select_db($this->database);
		return $this->connection;
	}

	function close() {
		$close = mysql_close($this->connection);
		return $close;
	}

	function select($sql) {
            try
            {
                $rows = mysql_query($sql,$this->connection);
                if (!$rows) {
                    return mysql_error();
                }
                if (mysql_num_rows($rows)==0) return null;
                $result = array();
                while ($row = mysql_fetch_assoc($rows)) {
                    $result[] = $row;
                }
                mysql_free_result($rows);
                return $result;
            } catch (Exception $ex) {
                echo "<pre />";
                print_r($ex);
                return false;
            }
	}

    function execute($sql) {
        $result = mysql_query($sql,$this->connection);
        if (!$result) {
                return mysql_error();
        } else {
                return true;
        }
    }
    
    function sanitized_post($POST) {
        foreach ($POST as $p=>$v) {
                $return[$p] = mysql_real_escape_string($v,$this->connection);
        }
        return $return;
    }
    
    function disconnect() {
        return mysql_close($this->connection);
    }
}

?>
