<?php

/**
 * @description
 * This script loads the widget config settings editor for a specified widget.
 * 
 * @author      Chezre Fredericks
 * @date_created 12/05/2014
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");

#   LOAD WIDGET CONFIG SETTINGS
if (empty($_GET['i'])) $GLOBALS['functions']->goToIndexPage();
$wId = $_GET['i'];
$w = new widget;
$widgetExists = $w->Load($wId);
if (!empty($w->wid_editForm)) {
    require("widgets/".$w->wid_directory."/".$w->wid_editForm);
} else {
    echo ($widgetExists) ? 'No Configuration for this widget':'Select a widget to add to this box';
}