<?php

/**
 * @description
 * This is the result of the system mail merge
 * 
 * @author      Chezre Fredericks
 * @date_created 14/04/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

#   USER RESULT PAGE
$u = $_SESSION['dms_user']['userid'];
$result = file_get_contents('acklists/'.$u.'.result.htm');

#   ECHO USER'S LAST RESULT PAGE
if (empty($result)) {
    header('location: index.php');
    exit();
}

echo $result;