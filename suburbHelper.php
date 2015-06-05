<?php

/**
 * @description
 * This script finds the suburb based on the search criteria
 * 
 * @author      Chezre Fredericks
 * @date_created 17/01/2014
 * @Changes
 * 
 */

include("inc/globals.php");

$pc = new extendedPostalcodes;
$bounds = (!empty($_GET['bounds'])) ? explode(',',$_GET['bounds']):array(0,20);
$queryBounds = (!empty($_GET['bounds'])) ? $_GET['bounds']:'0,20';
$lbound = $bounds[0];
$ubound = $bounds[1];
if (!empty($_GET['queryType'])) {
    $values = ($_GET['queryType']=='suburb') ? $pc->ajaxSearchBySuburb($_GET['query'],$queryBounds):$pc->ajaxSearchByPostalCode($_GET['query'],$queryBounds);
    $qtype = $_GET['queryType'];
} else {
    $values = $pc->ajaxSearchBySuburb($_GET['query'],$queryBounds);
    $qtype = 'suburb';   
}
if (!empty($values)) {
foreach ($values as $k=>$v) {
    echo '<div class="subHelp" title="'. $v['pco_postal_code'] .'" onclick="populateSuburb(this)">
                <div class="sub">' . $v['pco_suburb'] . ', </div>
                <div class="pcd">' . $v['pco_postal_code'] . '</div>
                <div class="pctype"> &nbsp;(' .$v['type'] .') </div>
        </div>';
}
$prevLbound = ($lbound>0) ? $lbound-20:0;
$prev = ($lbound>0) ? '<div style="float: left;" class="navDiv" onclick="getPostalCodes(\''.$prevLbound.','.$ubound.'\',\''.$qtype.'\');"><</div>':'';
$nextLbound = $lbound+20;
$next = (count($values)==20) ? '<div style="float:right;"  class="navDiv" onclick="getPostalCodes(\''.$nextLbound.','.$ubound.'\',\''.$qtype.'\');">></div>':'';
echo '<style>'
. '.navDiv {'
. 'width: 20px;'
. 'height: 20px;'
. 'cursor: pointer;'
. 'background-color: #254B7C;'
. 'color: #FFF;'
. 'font-size: 14pt;'
. 'border-radius: 5px;'
. '}'
. '</style>';
echo '<div id="subNav">'.$prev.$next.'</div>';
} else {
    echo '<div class="subHelp">no results found</div>';
}