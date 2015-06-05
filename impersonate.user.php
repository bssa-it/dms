<?php

/**
 * @description
 * This script adds the impersonation user id to the dms_user SESSION variable
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$_SESSION['dms_user']['impersonateUserId'] = $_POST['impUserId'];
header("location:index.php");