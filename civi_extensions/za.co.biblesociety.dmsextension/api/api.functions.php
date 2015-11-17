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

class dms {
    public static function getConfig() {
        $cfg = simplexml_load_file(dirname(__FILE__).'/../config.xml');
        return $cfg;
    }
    public static function getOptionValues($groupId) {
        $parms['option_group_id'] = (int)$groupId;
        $parms['version'] = 3;
        $results = civicrm_api('OptionValue','Get',$parms);
        if ($results['is_error']>0) {
            return array();
        } else {
            return $results['values'];
        }
    }
    public static function getFrequencyValues() {
        $frequencyValues = array();
        $cfg = simplexml_load_file(dirname(__FILE__).'/../config.xml');
        $frequencies = $cfg->preferences->frequencies->frequency;
        if (!empty($frequencies)) {
            foreach ($frequencies as $f) {
                $freqVal['default'] = (string)$f['default'];
                $freqVal['value'] = (string)$f['value'];
                $freqVal['label'] = (string)$f['desc'];
                $frequencyValues[] = $freqVal;
            }
        }
        return $frequencyValues;
    }
    
    public static function getFrequencyDescription($val) {
        $cfg = simplexml_load_file(dirname(__FILE__).'/../config.xml');
        $frequencies = $cfg->preferences->frequencies->frequency;
        if (!empty($frequencies)) {
            foreach ($frequencies as $f) {
                if ((string)$f['value'] == (string)$val) return (string)$f['desc'];
            }
        }
        return 'Unknown Frequency';
    }
    
    public static function addChildrenItems($parentItemId) {
        $children = array();
        $cfg = simplexml_load_file(dirname(__FILE__).'/../xml/Menu/dmsextension.xml');        
        $nextId = $parentItemId + 1;
        foreach ($cfg->children() as $i) {
            $children[$nextId] = array (
                'attributes' => array (
                  'label' => ts((string)$i->title,array('domain' => 'donation.biblesociety.co.za')),
                  'name' => (string)$i->title,
                  'url' => (string)$i->path,
                  'permission' => 'access CiviCRM',
                  'operator' => null,
                  'separator' => null,
                  'parentID' => $parentItemId,
                  'navID' => $nextId,
                  'active' => 1
                )   
            );
            $nextId++;
        }
        return $children;
    }
}