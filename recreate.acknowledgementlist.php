<?php

/**
 * @description
 * This script recreates an acknowledgement mail merge list from a specified filename
 * 
 * @author      Chezre Fredericks
 * @date_created 21/01/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$filename = $_POST['filename'];
$cnt = 0;

#   GET THE TRANSACTIONS FROM THE DATABASE USING THE FILENAME
$trxns = $GLOBALS['functions']->getContributionsFromMergeFilename($filename);
#   REBUILD MERGE DATA
foreach ($trxns as $k=>$v) {
    $e = $GLOBALS['functions']->getMergeDataForTrxn($v['trns_id']);
    $exportArray[] = $e;
    $cnt++; 
}
#   CREATE THE NEW CSV FILE ON THE SERVER
$GLOBALS['functions']->exportArrayToCSV('acklists/'.$filename,$exportArray);

#   RETURN MESSAGE TO THE USER.
echo $cnt . ' records created';

################
#
# TODO:    ADD A PROPER HTML TEMPLATE WITH NAVIGATION
#  
################
?>