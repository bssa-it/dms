<?php

/**
 * @description
 * This script REMOVES the impersonation from the dms_user SESSION variable
 * 
 * @author      Chezre Fredericks
 * @date_created 03/06/2014
 * @Changes
 * 
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$_SESSION['dms_user']['impersonateUserId'] = null;
header("location:index.php");