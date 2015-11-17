<?php

/**
 * @description
 * This script exports any array stored in the $_SESSION['export'] variable
 * 
 * @author      Chezre Fredericks
 * @date_created 21/01/2014
 * @Changes
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

#   BOOTSTRAP
include("inc/globals.php");
if (empty($_GET['language'])) {
    $curScript = basename(__FILE__, '.php');

    $menu = new menu;
    $pageHeading = $title = 'Operation POPI';

    $notificationsValue = ($GLOBALS['functions']->hasUserGotNotifications()) ? 'Y':'N';
    require('html/'.$curScript.'.htm');
    exit();
}

$dp = $_SESSION['dms_user']['config']['impersonate'];
$language = ($_GET['language']=='A') ? "'af_ZA'":"'en_ZA','en_GB'";
$filename = 'personal-dp-'.$_SESSION['dms_user']['config']['impersonate'].'-'.$_GET['language'].'.txt';

/* 
 *  
 * Rules:
 * Only people with postal mail = Y  EG 158239  
 * "Not Deleted" 
 * "Individuals"
 * For the user's primary department
 * 
 *  */

if ($dp!='9') {
    $bamJoin = '';
    $bamWhere = 'and C.`id` NOT in (select contact_id from civicrm_membership where `membership_type_id` = 1 and contact_id = C.id)';
    $searchDept = "AND SUBSTR(R.`organisation_id`,1,1) = '$dp'";
} else {
    $bamJoin = 'inner join civicrm_membership M on M.`membership_type_id` = 1 and M.`contact_id` = C.id';
    $bamWhere = '';
    $searchDept = '';
}
$sql = "SELECT 
        C.`contact_type`,
        C.`contact_sub_type`,
        C.id `contact id`,
        C.external_identifier `dnr_no`,
        first_name `Name`,
        last_name `Surname`,
        C.`organization_name`,
        C.household_name,
        T.`label` `Title`,
        L.`label` `Language`,
        DAY(birth_date) `Day`,
        LPAD(MONTH(`birth_date`),2,'0') `Month`,
        YEAR(birth_date) `Year`,
        C.`birth_date` `Date of Birth`,
        O.`id_number`,
        A.`street_address` `Address Line 1`,
        A.`supplemental_address_1` `Address Line 2`,
        A.`supplemental_address_2` `Address Line 3`,
        A.`supplemental_address_3` `Address Line 4`,
        A.`city` `Town`,
        LPAD(A.`postal_code`,4,'0') `Postal Code`,
        E.`email` `E-mail`,
        PC.`phone` `Cellphone`,
        PT.`phone` `Home`,
        PW.`phone` `Work`,
        LPAD(R.`category_id`,4,'0') `category_id`,
        R.`organisation_id`
FROM civicrm_contact C
INNER JOIN `civicrm_dms_contact_reporting_code` R ON R.`contact_id` = C.`id`
LEFT JOIN `civicrm_dms_contact_other_data` O ON O.`contact_id` = C.id
LEFT JOIN civicrm_address A ON A.`contact_id` = C.id AND A.`is_primary` = 1
LEFT JOIN civicrm_email E ON E.`contact_id` = C.`id` AND E.`is_primary` = 1
LEFT JOIN `civicrm_phone` PC ON PC.`contact_id` = C.id AND PC.`phone_type_id` = 2
LEFT JOIN `civicrm_phone` PT ON PT.`contact_id` = C.id AND PT.`is_primary` = 1 AND PT.`phone_type_id` != 2
LEFT JOIN `civicrm_phone` PW ON PW.`contact_id` = C.id AND PW.`is_primary` = 0 AND PW.`phone_type_id` != 2
LEFT JOIN `civicrm_option_value` T ON T.`value` = C.`prefix_id` AND T.`option_group_id` = 6
LEFT JOIN `civicrm_option_value` L ON L.`name` = C.`preferred_language` AND L.`option_group_id` = 75
$bamJoin
WHERE `preferred_language` IN ($language) AND `is_deleted` = 0 AND do_not_mail = 0 
AND contact_type = 'Individual' $searchDept
$bamWhere
order by R.`category_id`,C.`last_name`;";
$result = $GLOBALS['civiDb']->select($sql);

if (!$result) {
    header("location: /dms/operation.personalInfo.php");
    exit();
} else {
    #   DOWNLOAD THE CURRENT ARRAY TO A CSV FILE
    $GLOBALS['functions']->exportArrayToCSV($filename,$result,true);
}