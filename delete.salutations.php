<?php

/**
 * @description
 * This script deletes a salutation rule
 * 
 * @author      Chezre Fredericks
 * @date_created 09/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

if (!empty($_POST['sal_id'])) {
    $s = new salutations();
    $s->Delete($_POST['sal_id']);
}

header("location:salutations.php");