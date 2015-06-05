<?php

class CRM_Dmsextension_BAO_ContactReportingCode extends CRM_Dmsextension_DAO_ContactReportingCode {

  public static function create(&$params) {
    $instance = new CRM_Dmsextension_DAO_ContactReportingCode();
    $instance->copyValues($params);
    $instance->save();
    $params['id'] = $instance->id;
  }

  public static function getValues(&$params) {

    $instance = new CRM_Dmsextension_DAO_ContactReportingCode();
    $instance->copyvalues($params);
    $instance->find();
    
    $instances = array();
    $count = 0;
    while ($instance->fetch()) {
      $values = array();
      CRM_Core_DAO::storeValues($instance, $values);
      
      $oParams['org_id'] = $values['organisation_id'];
      $o = CRM_Dmsextension_BAO_Organisation::getValues($oParams);
      $values['org_name'] = (count($o)===1) ? $o[0]['org_name']:'';
      
      $cParams['cat_id'] = $values['category_id'];
      $c = CRM_Dmsextension_BAO_Category::getValues($oParams);
      $values['cat_name'] = (count($c)===1) ? $c[0]['cat_name']:'';
      
      $instances[$count] = $values;
      $count++;
    }

    return $instances;
  }
}