<?php

/**
 * @description
 * This script lets user log into Joomla and then forwards them to the dashboard
 * 
 * @author      Chezre Fredericks
 * @date_created 27/03/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
$curScript = basename(__FILE__, '.php');

$greeting = 'Day';
if (date("H")<12) $greeting = 'Morning';
if (date("H")>=12&&date("H")<18) $greeting = 'Afternoon';
if (date("H")>18) $greeting = 'Evening';

require('html/'.$curScript.'.htm');