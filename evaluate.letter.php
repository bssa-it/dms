<?php 

/**
 * @description
 * This script loads the unevaluated letter in the letter editor for evaluation.
 * 
 * @author      Chezre Fredericks
 * @date_created 26/11/2013
 * @Changes
 * 
 */

#   BOOTSTRAP
include("inc/globals.php");
$xmlConfig = simplexml_load_file("inc/config.xml");
$curScript = basename(__FILE__, '.php');

#   MENU AND HEADING
$menu = $GLOBALS['functions']->createMenu();
$pageHeading = $title = 'Evaluate Letters';
$notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   SAVE TEMPLATE
if (isset($_POST['letterEditor'])&&!empty($_POST['letterEditor'])) {
    $template = $_POST['letterEditor'];
    if (isset($_POST['saveTemplate'])&&$_POST['saveTemplate']=='Y') {
        $tpl = new template();
        if(isset($_POST['tpl_id'])&&$_POST['tpl_id']>0) 
        {
            $tpl->Load($_POST['tpl_id']);   
        } else {
            $tpl->tpl_createdByUserId = $_SESSION['dms_user']['userid'];
            $tpl->tpl_dateCreated = date("Y-m-d H:i:s");
            $tpl->tpl_accessLevel = $_POST['tpl_accessLevel'];
        }
        $tpl->tpl_name = $_POST['tpl_name'];
        $tpl->tpl_body = htmlentities($_POST['letterEditor']);
        $tpl->tpl_marginLeft = $_POST['tpl_marginLeft'];
        $tpl->tpl_marginTop = $_POST['tpl_marginTop'];
        $tpl->tpl_marginRight = $_POST['tpl_marginRight'];
        $tpl->tpl_marginBottom = $_POST['tpl_marginBottom'];
        $tpl->Save();
    }
}

#   CREATE UNEVALUATED MERGE FILES
if (isset($_POST['trxns'])&&!empty($_POST['trxns'])) {
    $template = $_POST['letterEditor'];
    $trxns = $_POST['trxns'];
    foreach ($trxns as $t) {
        $xmlFilename = 'unevaluated/'.$_SESSION['dms_user']['userid'].".$t.xml";
        if (file_exists($xmlFilename)) continue;
        
        $mr = new mergerecord;
        $mr->loadByTrnsId($t);
        
        $mergeData = $mr->getMergeRecordHeadings();
        foreach ($mr->xmlFields->field as $m) {
            $f = (string)$m['id'];
            $mergeData[(string)$m['label']] = $mr->$m['id'];
        }
        
        $pattern = $replacements = $blankAdressLine = $blankAddresReplacement = array();
        
        foreach ($mergeData as $k=>$v) {
            if (strlen(trim($v))==0&&preg_match('/Address/',$k)) {
                $blankAdressLine[] = '/##'.preg_replace('/ /','',strtolower($k)).'##\<br \/\>/';
                $blankAddresReplacement[] = '';
            }
            $pattern[] = '/##'.preg_replace('/ /','',strtolower($k)).'##/';
            $replacements[] = $v;
        }
                
        $letter = preg_replace($blankAdressLine,$blankAddresReplacement,$template);   
        $letter = preg_replace($pattern,$replacements,$letter);
        $filename = 'unevaluated/'.$_SESSION['dms_user']['userid'].".$t.html";
        file_put_contents($filename, $letter);
        
        $impersonateValue = ($_SESSION['dms_user']['config']['impersonate']==$mergeData['Department']) ? 'Y':'N';
        
        $xml = new SimpleXMLElement('<xml/>');
        $letterSettings = $xml->addChild('letter');
        $letterSettings->addChild('tpl_marginLeft', $_POST['tpl_marginLeft']);
        $letterSettings->addChild('tpl_marginTop', $_POST['tpl_marginTop']);
        $letterSettings->addChild('tpl_marginRight', $_POST['tpl_marginRight']);
        $letterSettings->addChild('tpl_marginBottom', $_POST['tpl_marginBottom']);
        $letterSettings->addChild('filename',$filename);
        $letterSettings->addChild('ready','N');
        $letterSettings->addChild('email',$mergeData['Email']);
        $letterSettings->addChild('method',$mergeData['Preferred Communication Method']);
        $letterSettings->addChild('subject',(string)$xmlConfig->acknowledgementconfig->defaultemailsubject);
        $letterSettings->addChild('emailname',htmlentities($mergeData['Emailto Name']));
        $letterSettings->addChild('impersonate',$impersonateValue);
        $letterSettings->addChild('department',$mergeData['Department']);
        $letterSettings->addChild('contact_id',$mergeData['contact_id']);
        $letterSettings->addChild('contribution_id', $mergeData['contribution_id']);
        $letterSettings->addChild('region', $mergeData['Region']);
        $letterSettings->addChild('addSignature','N');
        file_put_contents($xmlFilename,$xml->asXML());
    }   
}

#   GET ALL UNEVALUATED FILES
$u = $_SESSION['dms_user']['userid'];
$fileList = glob("unevaluated/$u.*.xml");
if (!empty($fileList)) {
    
    $jDepartments = "\t\tvar departmentList = [];";
    foreach ($_SESSION['dms_user']['config']['departments'] as $d=>$dp) {
        $jDepartments .= "\n\t\tdepartmentList.push(['" . $d ."','" . trim($dp) . "']);"; 
    }
    
    $jFilelist = "\t\tvar fileList = [];";
    foreach ($fileList as $f) {
        $jFilelist .= "\n\t\tfileList.push(['" . $f ."']);"; 
    }
    
    $fileXML = simplexml_load_file($fileList[0]);
    $html = file_get_contents((string)$fileXML->letter->filename);
    $marginLeft = (string)$fileXML->letter->tpl_marginLeft;
    $marginTop = (string)$fileXML->letter->tpl_marginTop; 
    $marginRight = (string)$fileXML->letter->tpl_marginRight; 
    $marginBottom = (string)$fileXML->letter->tpl_marginBottom;  
    $emailAddressOpts = '';
    $contactId = (int)$fileXML->letter->contact_id;
    $emailAdresses = $GLOBALS['functions']->getCiviContactEmailAddresses($contactId);
    if (!empty($emailAdresses)) {
        foreach ($emailAdresses as $k=>$v) {
            $selected = ((string)$fileXML->letter->email==$v['emailAddress']) ? ' SELECTED':'';
            $emailAddressOpts .= '<option value="'.$v['emailAddress'].'"'.$selected.'>'.$v['emailAddress'].'</option>';
        }
    }
    $method = (string)$fileXML->letter->method;
    $ready = (string)$fileXML->letter->ready;
    $emailSubject = (string)$fileXML->letter->subject;
    $emailToName = (string)$fileXML->letter->emailname;
    $htmlFilename = (string)$fileXML->letter->filename;
    $impersonate = (string)$fileXML->letter->impersonate;
    $dept = (string)$fileXML->letter->department;
    $filename = preg_replace('/unevaluated\//','',(string)$fileXML->letter->filename);
    $filename = preg_replace('/.html/','',$filename);
    $contact_id = (string)$fileXML->letter->contact_id;
    $contribution_id = (string)$fileXML->letter->contribution_id;
    $region = (string)$fileXML->letter->region;
    
    $commMethods = array('Postal Mail','Email');
    $methods = '';
    foreach ($commMethods as $m) {
        $selected = ($method == $m) ? ' SELECTED':'';
        $methods .= '<option value="'.$m.'"'.$selected.'>'.$m.'</option>';    
    }
    
    $exportImage = 'print-pdf.png';
    if ($method=='email') $exportImage = 'email.png';
    switch ($method) {
        case 'Postal Mail':
            $showPrintDiv = '';
            $showEmailDiv = ' style="display:none"';
            break;
        case 'Email':
            $showPrintDiv = ' style="display:none"';
            $showEmailDiv = '';
            break;
    }
    $isReadyChecked = ($ready=='N') ? '':' CHECKED'; 
    $showExportBtn = ($ready=='N') ? 'display:none;':'';
    $d = new department();
    $d->Load($dept);
    $departmentFromName = $d->dep_fromEmailName;
    
    $userFromName = $_SESSION['dms_user']['fullname'];
    if ($impersonate=='N') {
        $mustImpersonate = '';
        $dontImpersonate = ' CHECKED';
    }  else {
        $mustImpersonate = ' CHECKED';
        $dontImpersonate = '';
    }
    
    $java = "\t" . 'var mergeGroups = [];';
    $java .= "\n\t\tmergeGroups.push(['acknowledgements','Acknowledgements']);";
    $java .= "\n\t\t".'var acknowledgements = [];';
    $java .= "\n\t\tacknowledgements.push(['".date("d F Y")."', 'Today', 'Today']);"; 
    $totalLetters = count($fileList);
    
    require('html/'.$curScript.'.htm');
} else {
    require('html/no.letter.htm');
}