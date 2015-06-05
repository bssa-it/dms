<?php

class CRM_Dmsextension_BAO_Organisation extends CRM_Dmsextension_DAO_Organisation {

  public static function create($params) {
    $instance = new CRM_Dmsextension_DAO_Organisation();
    $instance->name = $params['org_id'];
    $instance->find(TRUE);

    $instance->copyValues($params);
    $instance->save();
  }

  public static function getValues($params) {
    
    $instance = new CRM_Dmsextension_DAO_Organisation();
    $instance->copyvalues($params);
    $instance->find();
    
    $instances = array();
    $count = 0;
    while ($instance->fetch()) {
      $values = array();
      CRM_Core_DAO::storeValues($instance, $values);
      
      $instances[$count] = $values;
      $count++;
    }

    return $instances;
    
    
  }
}