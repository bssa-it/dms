<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$logs = glob("*htm");

if (!empty($logs)) {
    foreach ($logs as $f) {
        unlink($f);
    }
}
echo "done " . date("Y-m-d H:i:s");