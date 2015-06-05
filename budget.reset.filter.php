<?php

/**
 * @description
 * This script clears the session variable of any budget variables
 * 
 * @author      Chezre Fredericks
 * @date_created 07/11/2013
 * @Changes
 * 
 */

#   BOOTSTRAP
require("inc/globals.php");

#   CLEAR SESSION ARRAY
$_SESSION['dmsBudget'] = array();

#   GO BACK TO BUDGET
header("location:budget.php");