<?php

/**
 * ContactReportingCode.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_acknowledgement_preferences_Create_spec(&$params) {
    
    dmsextension_civicrm_getParameterFields("CRM_Dmsextension_DAO_AcknowledgementPreferences",$params);
  
}

/**
 * ContactReportingCode.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_acknowledgement_preferences_Create($params) {
    
    $instance = new CRM_Dmsextension_BAO_AcknowledgementPreferences();
    $instance->create($params);
    $records = _civicrm_api3_basic_get('CRM_Dmsextension_BAO_AcknowledgementPreferences', $params);
    $result = $records['values'];
    return civicrm_api3_create_success($result, $params, 'AcknowledgementPreferences', 'Create');
    
}

