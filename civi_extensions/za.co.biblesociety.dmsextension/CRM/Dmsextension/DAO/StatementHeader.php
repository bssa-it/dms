<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';

class CRM_Dmsextension_DAO_StatementHeader extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_dms_statement_header';
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
   * Date and time the statement was imported
   *
   * @var datetime
   */
  public $imported_date;	
/**
   * The user that imported the statement
   *
   * @var int
   */
  public $imported_usr_id;	
/**
   * The filename including path of the statement
   *
   * @var varchar
   */
  public $import_filename;	
/**
   * Bank Account to which the deposit was made
   *
   * @var int
   */
  public $bank_account_id;	
/**
   * Office where batch was processed
   *
   * @var int
   */
  public $office_id;	
/**
   * Statement Date
   *
   * @var date
   */
  public $statement_date;	
  
  
  
  function __construct()
  {
    $this->__table = 'civicrm_dms_statement_header';
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
        
              'imported_date' => array(
              'name' => 'imported_date',
              'title' => ts('imported date'),
              'type' => CRM_Utils_Type::T_DATE,
              
            ) ,	

              'imported_usr_id' => array(
              'name' => 'imported_usr_id',
              'title' => ts('imported usr id'),
              'type' => CRM_Utils_Type::T_INT,
              
            ) ,	

              'import_filename' => array(
              'name' => 'import_filename',
              'title' => ts('import filename'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 255,
            ) ,	

              'bank_account_id' => array(
              'name' => 'bank_account_id',
              'title' => ts('bank account id'),
              'type' => CRM_Utils_Type::T_INT,
              
            ) ,	

              'office_id' => array(
              'name' => 'office_id',
              'title' => ts('office id'),
              'type' => CRM_Utils_Type::T_INT,
              
            ) ,	

              'statement_date' => array(
              'name' => 'statement_date',
              'title' => ts('statement date'),
              'type' => CRM_Utils_Type::T_DATE,
              
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
        'imported_date' => 'imported_date',	
'imported_usr_id' => 'imported_usr_id',	
'import_filename' => 'import_filename',	
'bank_account_id' => 'bank_account_id',	
'office_id' => 'office_id',	
'statement_date' => 'statement_date',	

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
            self::$_import['dms_statement_header'] = & $fields[$name];
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
            self::$_export['dms_statement_header'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}