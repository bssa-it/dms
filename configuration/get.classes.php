<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require('inc/class.functions.php');
$classesConfig = simplexml_load_file("../inc/classes.config.xml");
$classes = config_functions::xml2array($classesConfig->includes->children());
$extensions = config_functions::xml2array($classesConfig->extensions->children());
$classConfig = array_merge($classes,$extensions);
$tblClassesRows          = '<tr>';
$cnt                     = 1;
foreach ($classConfig as $class) {
    $newRow = ($cnt==1);
    $tblClassesRows .= ($newRow) ? '</tr><tr>':'';
    $tblClassesRows .= '<td>'.(string)$class.'</td>';
    $newRow = false;
    if ($cnt==5) {
        $cnt = 1;   
    } else {
        $cnt++;
    }
}
$tblClassesRows         .= '</tr>';

?>

<table width="100%" cellpadding="8" cellspacing="0" id="tblClasses">
    <?php echo $tblClassesRows; ?>
</table>