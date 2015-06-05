<?php

class CRM_Dmsextension_BAO_Department extends CRM_Dmsextension_DAO_Department {

  public static function create($params) {
    $instance = new CRM_Dmsextension_DAO_Department();
    $instance->name = $params['dep_id'];
    $instance->find(TRUE);

    $instance->copyValues($params);
    $instance->save();
  }

  public static function getValues(&$params, &$values) {
    $instances           = array();
    $instance            = new CRM_Dmsextension_DAO_Department();
    $instance->find();
    
    $count = 1;
    while ($instance->fetch()) {
      $values['department'][$count] = array();
      CRM_Core_DAO::storeValues($instance, $values['department'][$count]);

      $instances[$count] = $values['department'][$count];
      $count++;
    }

    return $instances;
  }
}