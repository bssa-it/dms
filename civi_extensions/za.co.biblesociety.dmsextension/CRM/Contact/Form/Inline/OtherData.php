<?php

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2014
 * $Id$
 *
 */

/**
 * form helper class for demographics section
 */
class CRM_Contact_Form_Inline_OtherData extends CRM_Contact_Form_Inline {

  public $_contactOtherDataId = '';
  /**
   * build the form elements
   *
   * @return void
   * @access public
   */
  public function buildQuickForm() {
    
    parent::buildQuickForm();
    
    $this->applyFilter('__ALL__', 'trim');
    
    CRM_Contact_Form_Edit_OtherData::buildQuickForm($this);

  }

  /**
   * process the form
   *
   * @return void
   * @access public
   */
  public function postProcess() {
    $params = $this->exportValues();
    $params['contact_id'] = $this->_contactId;
    $params['id']  = $this->_contactOtherDataId;
    $result = CRM_Dmsextension_BAO_ContactOtherData::create($params);
    
    $this->log();
    $this->ajaxResponse['contactOtherDataId'] = $this->_contactOtherDataId; 
    $this->response();
  }
}
