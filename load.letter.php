<?php

/**
 * @description
 * This file retrieves the html template from the database.
 * 
 * @author      Chezre Fredericks
 * @date_created 29/11/2013
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

#   GET HTML FROM DATABASE
$t = new template();
$t->Load($_GET['tpl_id']);
echo html_entity_decode($t->tpl_body);