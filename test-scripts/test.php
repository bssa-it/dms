<?php
include("inc/globals.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$email = new dmsEmail('unevaluated/1118.2709869.xml');
$mandrill = new dmsMandrill();
$sendResult = $mandrill->messages->send($email->message);

echo "<pre />";
print_r($sendResult);