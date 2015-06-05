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
class CRM_Contact_Form_Edit_OtherData {
  
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
    $_OtherData = CRM_Dmsextension_BAO_ContactOtherData::getValues($params);
    
    $doNotThank = $reminderMonth = $idNumber = $contactOtherDataId = '';
    if (count($_OtherData)==1) {
        $doNotThank = $_OtherData[0]['do_not_thank'];
        $reminderMonth = $_OtherData[0]['reminder_month'];
        $idNumber = $_OtherData[0]['id_number'];
        $contactOtherDataId = $_OtherData[0]['id'];
    }
    
    $form->_contactOtherDataId = $contactOtherDataId; 
    
    foreach (range(0,1) as $m) {
        $yesNoOptions[$m] = (empty($m)) ? 'No':'Yes';    
    }
    $s = $form->addElement('select', 'do_not_thank', ts('Do Not Thank'),$yesNoOptions);
    $s->setValue($doNotThank);
    
    foreach (range(0,12) as $m) {
        $monthOptions[$m] = (empty($m)) ? 'No reminder':date("F",strtotime(date("Y-$m-01")));    
    }
    $s = $form->addElement('select', 'reminder_month', ts('Reminder Month'),$monthOptions);
    $s->setValue($reminderMonth);
    
    $form->addElement('text', 'id_number', ts('ID Number'),array('value'=>$idNumber));
    
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

