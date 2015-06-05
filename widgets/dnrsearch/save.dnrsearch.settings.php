<?php

/**
 * @description
 * This script saves the widget details for the donor search widget.
 * 
 * @author      Chezre Fredericks
 * @date_created 22/05/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include '../../inc/globals.php';

#   LOAD, EDIT AND SAVE WIDGET DETAILS
$w = new widget;
$w->Load($_POST['wid_id']);
$w->wid_name = $_POST['wid_name'];
$w->Save();

#   GO BACK TO DASHBOARD
header('location:index.php');