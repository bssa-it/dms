<?php

class CRM_Dmsextension_BAO_Statement extends CRM_Dmsextension_DAO_Statement {

  public static function create($params) {
    $instance = new CRM_Dmsextension_DAO_Statement();
    $instance->copyValues($params);
    $instance->save();
    $params['id'] = $instance->id;
  }

  public static function getValues(&$params) {
    $instance = new CRM_Dmsextension_DAO_Statement();
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