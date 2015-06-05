<?php

/**
 * @author Chezre Fredericks
 * @copyright 2014
 */

/**
* Function to get the parameters for the API explorer
* 
*/
function dmsextension_civicrm_getParameterFields($dao_name,&$params) {
  $d = new $dao_name();
  $fields = $d->fields();
  // replace uniqueNames by the normal names as the key
  if (empty($unique)) {
    foreach ($fields as $name => & $field) {
      //getting rid of unused attributes
      foreach ($unsetIfEmpty as $attr) {
        if (empty($field[$attr])) {
          unset($field[$attr]);
        }
      }
      if ($name == $field['name']) {
        continue;
      }
      if (array_key_exists($field['name'], $fields)) {
        $field['error'] = 'name conflict';
        // it should never happen, but better safe than sorry
        continue;
      }
      $fields[$field['name']] = $field;
      $fields[$field['name']]['uniqueName'] = $name;
      unset($fields[$name]);
    }
  }
  foreach ($fields as $k=>$v) $params[$k] = $v;  
}

?>