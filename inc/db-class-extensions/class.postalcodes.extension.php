<?php

Class extendedPostalcodes extends postalcodes { 

    function ajaxSearchBySuburb($suburb,$bounds) {
        $sql = "SELECT DISTINCT pco_suburb,pco_postal_code,substr(pco_type,1,3) `type` FROM `dms_postalCodes` where `pco_suburb` LIKE '%$suburb%' limit $bounds";
        #echo $sql;
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
        	return false;
        } else {
        	foreach ($result as $r) {
        		foreach ($r as $k=>$v) $newRecord[$k] = stripslashes($v);
                $return[] = $newRecord;
        	}
            return $return;
        }
    }
    
    function ajaxSearchByPostalCode($postalCode,$bounds) {
        $sql = "SELECT DISTINCT pco_suburb,pco_postal_code,substr(pco_type,1,3) `type` FROM `dms_postalCodes` where `pco_postal_code` LIKE '%$postalCode%' limit $bounds";
        $result = $GLOBALS['db']->select($sql);
        if (!$result) {
        	return false;
        } else {
        	foreach ($result as $r) {
        		foreach ($r as $k=>$v) $newRecord[$k] = stripslashes($v);
                $return[] = $newRecord;
        	}
            return $return;
        }
    }
    # end class	
}

?>