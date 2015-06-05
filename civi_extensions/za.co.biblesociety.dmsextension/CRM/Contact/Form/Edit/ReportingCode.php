<?php

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2014
 * $Id$
 *
 */

/**
 * form helper class for an Demographics object
 */
class CRM_Contact_Form_Edit_ReportingCode {
  
  /**
   * build the form elements for Demographics object
   *
   * @param CRM_Core_Form $form       reference to the form object
   *
   * @return void
   * @access public
   * @static
   */
  static function buildQuickForm(&$form) {
    
    $params = array('contact_id' => $form->_contactId);
    $_reportingCodes = CRM_Dmsextension_BAO_ContactReportingCode::getValues($params);
    
    $organisationId = $organisationName = $categoryId = $contactReportingCodeId = '';
    if (count($_reportingCodes)==1) {
        $organisationId = $_reportingCodes[0]['organisation_id'];
        $organisationName = $_reportingCodes[0]['org_name'];
        $categoryId = $_reportingCodes[0]['category_id'];
        $contactReportingCodeId = $_reportingCodes[0]['id'];
    }
    
    $form->_contactReportingCodeId = $contactReportingCodeId; 
    $form->addElement('text', 'organisation_id', ts('Congregation'), array('value'=>$organisationId, 'onkeyup'=>'getOrganisationName()','id'=>'divOrganisationId'));
    $form->addElement('text', 'organisationName', ts('Congregation Name'),array('value'=>$organisationName));
    
    /* category dropdown */
    $params = array();
    $categories = CRM_Dmsextension_BAO_Category::getValues($params);
    foreach ($categories as $c) {
        $categoryOptions[$c['cat_id']] = $c['cat_id'] . ' - ' . $c['cat_name'];    
    }
    $s = $form->addElement('select', 'category_id', ts('Category'),$categoryOptions);
    $s->setValue($categoryId);
  }

  /**
   * This function sets the default values for the form. Note that in edit/view mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @param $form
   * @param $defaults
   *
   * @return void
   */
  static function setDefaultValues(&$form, &$defaults) {}
}

