<?php

class CRM_Dmsextension_BAO_StatementEntry extends CRM_Dmsextension_DAO_StatementEntry {

  public static function create($params) {
    $instance = new CRM_Dmsextension_DAO_StatementEntry();
    $instance->copyValues($params);
    $instance->save();
    $params['id'] = $instance->id;
  }

  public static function getValues(&$params) {
    $instance = new CRM_Dmsextension_DAO_StatementEntry();
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