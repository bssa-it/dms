<?php

/**
 * ContactReportingCode.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_contact_reporting_code_Get_spec(&$params) {
  dmsextension_civicrm_getParameterFields("CRM_Dmsextension_DAO_ContactReportingCode",$params);
}

/**
 * ContactReportingCode.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contact_reporting_code_Get($params) {
  $result = CRM_Dmsextension_BAO_ContactReportingCode::getValues($params);
  return civicrm_api3_create_success($result, $params, 'ContactReportingCode', 'get');
}

