<?php

include("inc/globals.php");

# ALWAYS INCLUDE THIS LINE FOR TESTING
#$_SESSION['dms_user']['authorisation']->printPermissions();

$parms['version'] = '3';
$parms['first_name'] = 'John';
$parms['last_name'] = 'Burgon';
$parms['contact_type'] = 'Individual';
$parms['api.email.create'] = array(array('email'=>'john@newscast.com','is_primary'=>1),array('email'=>'burgon@newscast.com','is_primary'=>0));

$result = civicrm_api('Contact','create',$parms);
echo "<pre> \n";
print_r($result);
echo "</pre>";

?>