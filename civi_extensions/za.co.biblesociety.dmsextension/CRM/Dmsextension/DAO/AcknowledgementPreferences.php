<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 * DO NOT EDIT.  Generated by GenCode.php
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';

class CRM_Dmsextension_DAO_AcknowledgementPreferences extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   * @static
   */
  static $_tableName = 'civicrm_dms_acknowledgement_preferences';
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
   * contact id to whom the preference belongs
   *
   * @var int
   */
  public $contact_id;	
/**
   * flag to identify if contact would like to be acknowledged
   *
   * @var varchar
   */
  public $must_acknowledge;	
/**
   * how often must contact be acknowledged
   *
   * @var smallint
   */
  public $frequency;	
/**
   * which communication method must be used for acknowledgements
   *
   * @var varchar
   */
  public $preferred_method;	
/**
   * the last time the contact was acknowledged
   *
   * @var datetime
   */
  public $last_acknowledgement_date;	
/**
   * the last contribution id acknowledged
   *
   * @var int
   */
  public $last_acknowledgement_contribution_id;	
/**
   * sum of contributions since last acknowledgement
   *
   * @var decimal
   */
  public $unacknowledged_total;	
/**
   * Last contribution date of contact
   *
   * @var datetime
   */
  public $last_contribution_date;	
  
  
  
  function __construct()
  {
    $this->__table = 'civicrm_dms_acknowledgement_preferences';
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
        
              'contact_id' => array(
              'name' => 'contact_id',
              'title' => ts('contact id'),
              'type' => CRM_Utils_Type::T_INT,
              
            ) ,	

              'must_acknowledge' => array(
              'name' => 'must_acknowledge',
              'title' => ts('must acknowledge'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 1,
            ) ,	

              'frequency' => array(
              'name' => 'frequency',
              'title' => ts('frequency'),
              'type' => CRM_Utils_Type::T_INT,
              
            ) ,	

              'preferred_method' => array(
              'name' => 'preferred_method',
              'title' => ts('preferred method'),
              'type' => CRM_Utils_Type::T_STRING,
              'maxlength' => 50,
            ) ,	

              'last_acknowledgement_date' => array(
              'name' => 'last_acknowledgement_date',
              'title' => ts('last acknowledgement date'),
              'type' => CRM_Utils_Type::T_DATE,
              
            ) ,	

              'last_acknowledgement_contribution_id' => array(
              'name' => 'last_acknowledgement_contribution_id',
              'title' => ts('last acknowledgement contribution id'),
              'type' => CRM_Utils_Type::T_INT,
              
            ) ,	

              'unacknowledged_total' => array(
              'name' => 'unacknowledged_total',
              'title' => ts('unacknowledged total'),
              'type' => CRM_Utils_Type::T_MONEY,
              
            ) ,	

              'last_contribution_date' => array(
              'name' => 'last_contribution_date',
              'title' => ts('last contribution date'),
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
        'contact_id' => 'contact_id',	
'must_acknowledge' => 'must_acknowledge',	
'frequency' => 'frequency',	
'preferred_method' => 'preferred_method',	
'last_acknowledgement_date' => 'last_acknowledgement_date',	
'last_acknowledgement_contribution_id' => 'last_acknowledgement_contribution_id',	
'unacknowledged_total' => 'unacknowledged_total',	
'last_contribution_date' => 'last_contribution_date',	

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
            self::$_import['dms_acknowledgement_preferences'] = & $fields[$name];
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
            self::$_export['dms_acknowledgement_preferences'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}