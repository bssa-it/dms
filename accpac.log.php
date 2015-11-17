<?php

/* *
 * 
 * This Script accepts the xml from the Accpac Server
 * 
 */

$inputStream = file_get_contents('php://input');
$xml = simplexml_load_string($inputStream);
$result = file_put_contents("logs/latest.xml",$xml->asXML());

echo ($result>0) ? 'XML Log Created':'XML Log NOT Created';