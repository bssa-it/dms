<?php

require_once 'CRM/Core/Page.php';

class CRM_Dmsextension_Page_Category extends CRM_Core_Page {
  function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Category'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    $this->assign('options',array('sort' => "cat_id"));
    
    $result = civicrm_api3('Category', 'get', array('sequential' => 1,array('options'=>array('sort'=>'cat_id'))));
    $this->assign("categories", $result['values']);
    parent::run();
    
  }
}