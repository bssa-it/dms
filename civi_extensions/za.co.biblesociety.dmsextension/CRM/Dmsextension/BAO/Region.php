<?php

class CRM_Dmsextension_BAO_Region extends CRM_Dmsextension_DAO_Region {

  public static function create($params) {
    $instance = new CRM_Dmsextension_DAO_Region();
    $instance->name = $params['reg_id'];
    $instance->find(TRUE);

    $instance->copyValues($params);
    $instance->save();
  }

  public static function getValues($params) {
    $values   = array();

    $instance = new CRM_Dmsextension_DAO_Region();
    $instance->copyvalues($params);

    if ($instance->find()) {
      CRM_Core_DAO::storeValues($instance, $values);
    }
    return $values;
  }
}