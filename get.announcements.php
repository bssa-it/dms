<?php

/**
 * @description
 * This script retrieves the group totals
 * 
 * @author      Chezre Fredericks
 * @date_created 15/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$files = glob('announcements/*.{php,htm,html}',GLOB_BRACE);
if (!empty($files)) {
    $return['message'] = count($files) . ' announcements found.';
    foreach ($files as $f) {
        $info = pathinfo($f);
        $return['files'][] = array('fname'=>basename($f,'.'.$info['extension']),'path'=>$f); 
    }
} else {
    $return['files'] = array();
    $return['message'] = 'No Announcements';
}
print json_encode($return);