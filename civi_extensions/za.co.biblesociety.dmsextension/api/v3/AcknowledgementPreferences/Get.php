<?php

/**
 * Category.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_acknowledgement_preferences_Get_spec(&$params) {
  dmsextension_civicrm_getParameterFields("CRM_Dmsextension_DAO_AcknowledgementPreferences",$params);
}

/**
 * Category.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 * {@getfields category_Get}
 */
function civicrm_api3_acknowledgement_preferences_Get($params) {
    $result = CRM_Dmsextension_BAO_AcknowledgementPreferences::getValues($params);
    return civicrm_api3_create_success($result, $params, 'AcknowledgementPreferences', 'get');
}