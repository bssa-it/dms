<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';

class CRM_Dmsextension_DAO_Office extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_dms_office';
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   * @static
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   * @static
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   * @static
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   * @static
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   * @static
   */
  static $_log = true;
  
  /**
   * Unique key
   *
   * @var int unsigned
   */
  public $id;
  /**
   * Contact Id for the business manager
   *
   * @var int
   */
  public $business_manager_contact_id;	
/**
   * Name of office
   *
   * @var varchar
   */
  public $name;	
/**
   * Address in English
   *
   * @var varchar
   */
  public $address_eng;	
/**
   * Address in Afrikaans
   *
   * @var varchar
   */
  public $address_afr;	
/**
   * Telephone number for signatures
   *
   * @var varchar
   */
  public $telephone;	
/**
   * Fax
   *
   * @var varchar
   */
  public $fax;	
  
  
  
  function __construct()
  {
    $this->__table = 'civicrm_dms_office';
    parent::__construct();
  }
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
       'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('ID') ,
          'required' => true,
        ) ,
        
              'business_manager_contact_id' => array(
              'name' => 'business_manager_contact_id',
              'title' => ts('business manager contact id'),
              'type' => CRM_Utils_Type::T_INT,
              
            ) ,	

              'name' => array(
              'name' => 'name',
              'title' => ts('name'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 150,
            ) ,	

              'address_eng' => array(
              'name' => 'address_eng',
              'title' => ts('address eng'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 255,
            ) ,	

              'address_afr' => array(
              'name' => 'address_afr',
              'title' => ts('address afr'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 255,
            ) ,	

              'telephone' => array(
              'name' => 'telephone',
              'title' => ts('telephone'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 20,
            ) ,	

              'fax' => array(
              'name' => 'fax',
              'title' => ts('fax'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 20,
            ) ,	

        
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'business_manager_contact_id' => 'business_manager_contact_id',	
'name' => 'name',	
'address_eng' => 'address_eng',	
'address_afr' => 'address_afr',	
'telephone' => 'telephone',	
'fax' => 'fax',	

      );
    }
    return self::$_fieldKeys;
  }
  /**
   * returns the names of this table
   *
   * @access public
   * @static
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * returns if this table needs to be logged
   *
   * @access public
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * returns the list of fields that can be imported
   *
   * @access public
   * return array
   * @static
   */
  static function &import($prefix = false)
  {
    if (!(self::$_import)) {
      self::$_import = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('import', $field)) {
          if ($prefix) {
            self::$_import['dms_office'] = & $fields[$name];
          } else {
            self::$_import[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_import;
  }
  /**
   * returns the list of fields that can be exported
   *
   * @access public
   * return array
   * @static
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['dms_office'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}