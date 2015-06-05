<?php

$xml = file_get_contents('php://input');
file_put_contents('logs/grapevine/responses/'.date("YmdHisu").'.xml',$xml);