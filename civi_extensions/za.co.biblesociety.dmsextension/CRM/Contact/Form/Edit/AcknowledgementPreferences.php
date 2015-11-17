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
class CRM_Contact_Form_Edit_AcknowledgementPreferences {
  
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
    $_acknowledgementPreferences = CRM_Dmsextension_BAO_AcknowledgementPreferences::getValues($params);
    
    $frequency = $mustAcknowledge = $preferredMethod = '';
    if (count($_acknowledgementPreferences)==1) {
        $frequency = $_acknowledgementPreferences[0]['frequency'];
        $mustAcknowledge = $_acknowledgementPreferences[0]['must_acknowledge'];
        $preferredMethod = $_acknowledgementPreferences[0]['preferred_method'];
        $id = $_acknowledgementPreferences[0]['id'];
    }
    
    $form->_id = $id; 
    
    $values = dms::getFrequencyValues();
    foreach ($values as $v) {
        $frequencyOptions[$v['value']] = $v['label'];
    }
    $f = $form->addElement('select', 'frequency', ts('Frequency'), $frequencyOptions);
    $f->setValue($frequency);
    
    /* Must Acknowledge Drop down */
    $values = array();
    $values['Y'] = 'Yes';
    $values['N'] = 'No';
    $m = $form->addElement('select', 'must_acknowledge', ts('Must Acknowledge'),$values);
    $m->setValue($mustAcknowledge);
    
    /* communication methods dropdown */
    $dmsConfig = dms::getConfig();
    $options = dms::getOptionValues($dmsConfig->communicationMethodsGroupId);
    foreach ($options as $o) {
        $selectOptions[$o['label']] = $o['label'];    
    }
    $s = $form->addElement('select', 'preferred_method', ts('Preferred Method'),$selectOptions);
    $s->setValue($preferredMethod);
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

