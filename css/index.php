<?php

/**
 * @description
 * This script checks if you are admin.  if not, user will be redirected to dashboard
 * 
 * @author      Chezre Fredericks
 * @date_created 13/04/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

if (!empty($_SESSION['dms_user']['authorisation'])&&$_SESSION['dms_user']['authorisation']->isAdmin) {
    $files = glob('*');
    $fileList = '';
    foreach ($files as $f) {
        $fileList .= '<li class="filenam" path="'.$f.'">' . $f . '</li>';
    }
    require('../html/index.htm');
} else {
    $GLOBALS['functions']->goToIndexPage();
}