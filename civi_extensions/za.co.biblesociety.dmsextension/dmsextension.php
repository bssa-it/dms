<?php

require_once 'dmsextension.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dmsextension_civicrm_config(&$config) {
  _dmsextension_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function dmsextension_civicrm_xmlMenu(&$files) {
  _dmsextension_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function dmsextension_civicrm_install() {
  _dmsextension_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dmsextension_civicrm_uninstall() {
  _dmsextension_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dmsextension_civicrm_enable() {
  _dmsextension_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dmsextension_civicrm_disable() {
  _dmsextension_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function dmsextension_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _dmsextension_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function dmsextension_civicrm_managed(&$entities) {
  _dmsextension_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dmsextension_civicrm_caseTypes(&$caseTypes) {
  _dmsextension_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function dmsextension_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _dmsextension_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * This file has the ninja functions for the DMS Extension
 */
include("api/api.functions.php");

function dmsextension_civicrm_navigationMenu(&$params) {
 
  // Check that our item doesn't already exist
  $menu_item_search = array('url' => 'civicrm/reporting-codes');
  $menu_items = array();
  CRM_Core_BAO_Navigation::retrieve($menu_item_search, $menu_items);
 
  if ( ! empty($menu_items) ) { 
    return;
  }
 
  $navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
  if (is_integer($navId)) {
    $navId++;
  }
  // Find the Report menu
  $administerID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Administer', 'id', 'name');
    
  
  
    $params[$administerID]['child'][$navId] = array (
        'attributes' => array (
          'label' => ts('DMS Settings',array('domain' => 'donation.biblesociety.co.za')),
          'name' => 'DMS Settings',
          'url' => null,
          'permission' => 'access CiviCRM',
          'operator' => null,
          'separator' => 1,
          'parentID' => $administerID,
          'navID' => $navId,
          'active' => 1
    ),
        'child' => dms::addChildrenItems($navId)
  );

}
/*function dmsextension_civicrm_summary( $contactID, &$content, &$contentPlacement = CRM_Utils_Hook::SUMMARY_BELOW ) {
    $options = dms::getFrequencyValues();
    echo "<pre>";
    print_r($options);
    echo "</pre>";
}*/

/**
 * This hook extension adds the DMS fields to the donor summary screen
 * 
 
function dmsextension_civicrm_summary( $contactID, &$content, &$contentPlacement = CRM_Utils_Hook::SUMMARY_BELOW ) {

    $params = array('contact_id' => $contactID);
    $values = array();
    $_reportingCodes = CRM_Dmsextension_BAO_ContactReportingCode::getValues($params, $values);
    
    echo "<pre />";
    print_r($_reportingCodes);
    
    $reportingCodesForm = <<<EOT
        <h3>DMS</h3>
        <div class="contact_panel">
            <div class="contactCardLeft">
                left column
            </div>
            <div class="contactCardRight">
                right column
            </div>
        </div>
EOT;
    $content = $reportingCodesForm;
    
    
    $mParams['motivation_id'] = '1033';
      $m = CRM_Dmsextension_BAO_Motivation::getValues($mParams);
      $values['motivation_description'] = (count($m)===1) ? $m[0]['description']:'';
      
      echo "<pre />";
      print_r($m);
}
*/