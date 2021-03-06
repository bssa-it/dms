<?php

class CRM_Dmsextension_BAO_AcknowledgementPreferences extends CRM_Dmsextension_DAO_AcknowledgementPreferences {

  public static function create($params) {
    $instance = new CRM_Dmsextension_DAO_AcknowledgementPreferences();
    $instance->copyValues($params);
    $instance->save();
    $params['id'] = $instance->id;
  }

  public static function getValues(&$params) {
    $instance = new CRM_Dmsextension_DAO_AcknowledgementPreferences();
    $instance->copyvalues($params);
    $instance->find();
    
    $instances = array();
    $count = 0;
    while ($instance->fetch()) {
      $values = array();
      CRM_Core_DAO::storeValues($instance, $values);
      
      $values['frequency_description'] = dms::getFrequencyDescription($values['frequency']);
      
      $instances[$count] = $values;
      $count++;
    }

    return $instances;
  }
}