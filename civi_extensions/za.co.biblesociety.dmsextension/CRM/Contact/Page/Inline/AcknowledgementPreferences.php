<?php

class CRM_Contact_Page_Inline_AcknowledgementPreferences extends CRM_Core_Page {

  /**
   * Run the page.
   *
   * This method is called after the page is created.
   *
   * @return void
   * @access public
   *
   */
  function run() {
    
    $contactId = CRM_Utils_Request::retrieve('cid', 'Positive', CRM_Core_DAO::$_nullObject, TRUE, NULL, $_REQUEST);
    $params   = array('contact_id' => $contactId);
    //$reportingCodes = CRM_Core_BAO_ReportingCode::getValues($params, CRM_Core_DAO::$_nullArray);
    
    $this->assign('contactId', $contactId);

    // check logged in user permission
    CRM_Contact_Page_View::checkUserPermission($this, $contactId);

    // finally call parent
    parent::run();
  }
}

