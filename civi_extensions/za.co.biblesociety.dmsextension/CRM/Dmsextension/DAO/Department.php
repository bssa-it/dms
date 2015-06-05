<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';

class CRM_Dmsextension_DAO_Department extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_dms_department';
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
   * Department Id
   *
   * @var string
   */
  public $dep_id;
  /**
   * Department name
   *
   * @var string 
   */
  public $dep_name;
  /**
   * Home Region
   *
   * @var tinyint
   */
  public $dep_home_region;
  /**
   * Is national Department?
   *
   * @var string
   */
  public $dep_is_national;
  /**
   * Budget Allocation
   *
   * @var float
   */
  public $dep_budget_allocation;
  /**
   * Chart Color
   *
   * @var string
   */
  public $dep_chart_color;
  /**
   * Contact Id
   *
   * @var int
   */
  public $dep_contact_id;
  /**
   * class constructor
   *
   * @access public
   * @return civicrm_dms_region
   */
  function __construct()
  {
    $this->__table = 'civicrm_dms_department';
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
        'dep_id' => array(
          'name' => 'dep_id',
          'title' => ts('Department Id'),
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 2,
        ) ,
        'dep_name' => array(
          'name' => 'dep_name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Department Name'),
          'maxlength' => 250,
          'size' => CRM_Utils_Type::HUGE,
        ) ,
        'dep_home_region' => array(
          'name' => 'dep_home_region',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Home Region Id'),
        ) ,
        'dep_is_national' => array(
          'name' => 'dep_is_national',
          'title' => ts('Is National Department'),
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 1,
        ) ,
        'dep_budget_allocation' => array(
          'name' => 'dep_budget_allocation',
          'type' => CRM_Utils_Type::T_MONEY,
          'title' => ts('Budget Allocation'),
        ) ,
        'dep_chart_color' => array(
          'name' => 'dep_chart_color',
          'title' => ts('Chart Colour'),
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 10,
        ) ,
        'dep_contact_id' => array(
          'name' => 'dep_contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Contact Id'),
          'FKClassName' => 'CRM_Contact_DAO_Contact',
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
        'dep_id' => 'dep_id',
        'dep_name' => 'dep_name',
        'dep_home_region' => 'dep_home_region',
        'dep_is_national' => 'dep_is_national',
        'dep_budget_allocation' => 'dep_budget_allocation',
        'dep_chart_color' => 'dep_chart_color',
        'dep_contact_id' => 'dep_contact_id',
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
            self::$_import['dms_department'] = & $fields[$name];
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
            self::$_export['dms_department'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}