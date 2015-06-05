<?php

/**
 * @description
 * Basic Donor Search Widget File
 * 
 * @author      Chezre Fredericks
 * @package     Donor Management System
 * @copyright   None
 * @version     3.0.1
 * 
 * @Changes
 * 21/05/2014 - Chezre Fredericks:
 * File created
 * 
 * @availableVariables
 * $qtrNo   :   is the current quadrant number
 * $w       :   is the widget class object for the current widget
 * 
 */

$regions = $GLOBALS['functions']->GetRegionList();
$regionOpts = '';
if (!empty($regions)) {
    foreach ($regions as $r) {
        $regionOpts .= '<option value="'.$r['region_id'].'">'.$r['region_id']. ' ' .$r['region_name'].'</option>';
    }
}

?>