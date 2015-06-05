<?php

/**
 * @description
 * This script counts unevaluated letters for the status bar while saving letters as 
 * acknowledgements.
 * 
 * @author      Chezre Fredericks
 * @date_created 14/04/2014
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");

#   CALCULATE THE UNPROCESSED LETTERS
$u = $_SESSION['dms_user']['userid'];
$fileList = glob("unevaluated/$u.*.xml");
$total = $_GET['t'];
$completed = $total - count($fileList);
$percentage = number_format($completed/$total*100,2);

#   SHOW STATUS IF THERE ARE STILL UNPROCESSED LETTERS
if ($completed<$total) {
?>
                    <div style="padding: 10px;" id="progressBar"><progress value="<?php echo $completed; ?>" max="<?php echo $total; ?>" class="html5"></progress></div>
                    <div id="cntDiv"><span id="totalCompleted"><?php echo $completed; ?></span>/<?php echo $total; ?></div>
                    <div id="perComplete"><?php echo $percentage; ?> %</div>
                    
<?php 

#   ELSE SHOW THE USER THAT THE PDF'S ARE BEING MERGED
} elseif (file_exists("acklists/$u.result.htm")) {  echo 'completed'; 
} else {
    echo 'merging pdf\'s';    
}
 
?>