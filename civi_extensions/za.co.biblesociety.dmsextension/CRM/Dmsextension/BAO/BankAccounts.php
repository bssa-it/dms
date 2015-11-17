<?php

class CRM_Dmsextension_BAO_BankAccounts extends CRM_Dmsextension_DAO_BankAccounts {

  public static function create($params) {
    $instance = new CRM_Dmsextension_DAO_BankAccounts();
    $instance->copyValues($params);
    $instance->save();
    $params['id'] = $instance->id;
  }

  public static function getValues(&$params) {
    $instance = new CRM_Dmsextension_DAO_BankAccounts();
    $instance->copyvalues($params);
    $instance->find();
    
    $instances = array();
    $count = 0;
    while ($instance->fetch()) {
      $values = array();
      CRM_Core_DAO::storeValues($instance, $values);
      
      $mParams['id'] = $values['bank_id'];
      $m = CRM_Dmsextension_BAO_Banks::getValues($mParams);
      $values['bank_name'] = (count($m)===1) ? $m[0]['name']:'';
      
      $instances[$count] = $values;
      $count++;
    }

    return $instances;
  }
}