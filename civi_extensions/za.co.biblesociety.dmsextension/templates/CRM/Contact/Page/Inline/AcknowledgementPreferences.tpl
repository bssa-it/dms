{* template for building Reporting Codes block *}
{crmAPI var='result' entity='AcknowledgementPreferences' action='get' sequential=1 contact_id=$contactId}
{foreach from=$result.values item=ap}
  {assign var="mustAcknowledge" value=$ap.must_acknowledge}
  {assign var="frequency" value=$ap.frequency_description}
  {assign var="preferredMethod" value=$ap.preferred_method}
  {assign var="lastAcknowledgementDate" value=$ap.last_acknowledgement_date}
  {assign var="unacknowledgedTotal" value=$ap.unacknowledged_total}
{/foreach}

{literal}
<style type="text/css">
    .dms-label {
        width: auto;
        background-color: #FAFAFA;
        color: #7A7A60;
        text-align: left;
        float: left;
        padding: 4px;
    }
</style>
{/literal}
<div id="crm-acknowledgement-content" {if $permission EQ 'edit'} class="crm-inline-edit" data-edit-params='{ldelim}"cid": "{$contactId}", "class_name": "CRM_Contact_Form_Inline_AcknowledgementPreferences"{rdelim}'{/if}>
  <div class="crm-clear crm-inline-block-content" {if $permission EQ 'edit'}title="{ts}Edit Acknowledgement Preferences{/ts}"{/if}>
      <h4>Acknowledgements</h4>
    {if $permission EQ 'edit'}
      <div class="crm-edit-help">
        <span class="batch-edit"></span>{ts}Edit Acknowledgement Preferences{/ts}
      </div>
    {/if}
       <div class="crm-summary-row">
        <div class="crm-label">{ts}Must Acknowledge{/ts}</div>
        <div class="crm-content">{$mustAcknowledge}</div>
      </div>
      <div class="crm-summary-row">
        <div class="crm-label">{ts}Frequency{/ts}</div>
        <div class="crm-content">{$frequency}</div>
      </div>
      <div class="crm-summary-row">
        <div class="crm-label">{ts}Preferred Method{/ts}</div>
        <div class="crm-content">{$preferredMethod}</div>
      </div>
      <div class="crm-summary-row">
        <div class="dms-label">{ts}Last Acknowledgement Date{/ts}</div>
        <div class="crm-content">{$lastAcknowledgementDate|date_format:"%D"}</div>
      </div>
      <div class="crm-summary-row">
        <div class="dms-label">{ts}Unacknowledged Total{/ts}</div>
        <div class="crm-content">R {$unacknowledgedTotal|string_format:"%.2f"}</div>
      </div>
  </div>
</div>