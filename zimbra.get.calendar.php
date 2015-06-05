<?php

/**
 * @description
 * This script fetches the zimbra calendar for the user
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

include("inc/globals.php");

if (empty($_GET['c'])) exit();

$xmlConfig = simplexml_load_file("inc/config.xml");

$PREAUTH_KEY=(string)$xmlConfig->emailconfig->zimbraPreAuthKey;
$email = $_SESSION['dms_user']['username']."@biblesociety.co.za";
$timestamp=time()*1000;
$preauthToken=hash_hmac("sha1",$email."|name|0|".$timestamp,$PREAUTH_KEY);

header('location: https://mymail.biblesociety.co.za/service/preauth?account='.$email.'&by=name&timestamp='.$timestamp."&expires=0&preauth=".$preauthToken.'&redirectURL=/home/~/'.$_GET['c'].'.html');