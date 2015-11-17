<?php

/**
 * @description
 * This script loads the reports dashboard.
 * 
 * @author      Chezre Fredericks
 * @date_created 22/04/2015
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("../inc/globals.php");
$curScript = basename(__FILE__, '.php');

$menu = new menu;
$pageHeading = $title = 'Reports';

$directories = glob('reports/*',GLOB_ONLYDIR);
$reportList = $prompts = '';
foreach ($directories as $dir) {
    $reportGroup = "\n" . '<div class="reportGroup">';
    $reportGroup .= '<div class="reportGroupHeading">'.preg_replace('#reports/#','',$dir).'</div>';
    $reports = glob($dir.'/*.xml');
    $cnt = 0;
    $validation = '';
    if (!empty($reports)) {
        foreach ($reports as $report) {
            $xml = simplexml_load_file($report);
            if ((string)$xml->active=='N') continue;
            if (count($xml->authorisation->children())>0) {
                $userAllowed = false;
                foreach ($xml->authorisation->children() as $a) {
                    if (in_array($a['group'],$_SESSION['dms_user']['authorisation']->userGroups)) {
                        $userAllowed = true;
                        continue;
                    }
                }
                if (!$userAllowed) continue;
            }
            $prompt = $GLOBALS['functions']->createReportForm($xml->prompts,$report);
            $patterns = array('#'.$dir.'/#','#.xml#');
            $replacements = array('','');
            $hasPrompts = false;
            $promptName = '';
            if (!empty($prompt['form'])) {
                $promptName = 'p'.ucfirst(preg_replace($patterns,$replacements,$report));
                $prompts .=  '<tr id="'.$promptName.'" style="display:none"><td>' . $prompt['form'] . '</td></tr>';
                $hasPrompts = true;
            }
            $validateFunction = 'validate_'.preg_replace($patterns,$replacements,$report);
            $validation .= "\n" . 'function '.$validateFunction.'() {';
            if ($hasPrompts && !empty($prompt['validation'])) {
                $validation .= $prompt['validation'];
            }
            $frmId = 'frm' . ucfirst(basename($report,'.xml'));
            $validation .= "\n\t" . 'submitForm("'.$frmId.'");';
            $validation .= "\n}";
            $patterns[] = '#_#';
            $replacements[] = ' ';
            $reportGroup .= '<div class="report" fn="'.$report.'" frmid="'.$frmId.'" frm="'.$promptName.'" validate="'.$validateFunction.'">'.preg_replace($patterns,$replacements,$report).'</div>';
            $cnt++;
        }
    }
    $reportGroup .= '</div>';
    if ($cnt>0) $reportList .= $reportGroup;
}

$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
require('html/'.$curScript.'.htm');