<?php

class CRM_Dmsextension_BAO_Transaction extends CRM_Dmsextension_DAO_Transaction {

  public static function create(&$params) {
    $instance = new CRM_Dmsextension_DAO_Transaction();
    $instance->copyValues($params);
    $instance->save();
    $params['id'] = $instance->id;
  }

  public static function getValues(&$params) {

    $instance = new CRM_Dmsextension_DAO_Transaction();
    $instance->copyvalues($params);
    $instance->find();
    
    $instances = array();
    $count = 0;
    while ($instance->fetch()) {
      $values = array();
      CRM_Core_DAO::storeValues($instance, $values);
      
      $mParams['motivation_id'] = $values['motivation_id'];
      $m = CRM_Dmsextension_BAO_Motivation::getValues($mParams);
      $values['motivation_description'] = (count($m)===1) ? $m[0]['description']:'';
      
      $instances[$count] = $values;
      $count++;
    }

    return $instances;
  }
}