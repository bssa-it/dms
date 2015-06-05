<?php

$inputStream = file_get_contents('php://input');
$xml = simplexml_load_string($inputStream);
$filename = date("YmdHis") . str_pad(rand(0,999),3,'0');
if (!$xml) {
    $fullPath = 'logs/grapevine/responses/'.$filename.'.txt';
    file_put_contents($fullPath,$inputStream);
    if (!empty($_SERVER['HTTP_ORIGIN'])) file_put_contents($fullPath,'HTTP Origin:' . $_SERVER['HTTP_ORIGIN'] , FILE_APPEND);
    if (!empty($_POST)) {
        $post = print_r($_POST,TRUE);
        file_put_contents($fullPath, $post, FILE_APPEND);
        if (!empty($_POST['XML'])) {
            $xml = simplexml_load_string($_POST['XML']);
            file_put_contents('logs/grapevine/responses/'.$filename.'.xml',$xml->asXML());
        }
    }
} else {
    file_put_contents('logs/grapevine/responses/'.$filename.'.xml',$xml->asXML());
}
$response['message'] = "response received... Thank you!";
$response['process_date'] = date("Y-m-d H:i:s");
$json = json_encode($response);
echo $json;