{* Get the other data *}
{crmAPI var='result' entity='ContactOtherData' action='get' sequential=1 contact_id=$contactId}
{foreach from=$result.values item=contactotherdata}
  {assign var="do_not_thank" value=$contactotherdata.do_not_thank}
  {assign var="reminder_month" value=$contactotherdata.reminder_month}
  {assign var="id_number" value=$contactotherdata.id_number}
{/foreach}

{assign var='strMonth' value="%02d"|sprintf:$reminder_month}
{assign var='strMonth' value="2000`$strMonth`01000001"}

{* Other Contact Data Block *}
<div id="crm-otherdata-content" {if $permission EQ 'edit'} class="crm-inline-edit" data-edit-params='{ldelim}"cid": "{$contactId}", "class_name": "CRM_Contact_Form_Inline_OtherData"{rdelim}'{/if}>
  <div class="crm-clear crm-inline-block-content" {if $permission EQ 'edit'}title="{ts}Edit Other Data{/ts}"{/if}>
      <h4>Other Data</h4>
    {if $permission EQ 'edit'}
      <div class="crm-edit-help">
        <span class="batch-edit"></span>{ts}Edit Other Data{/ts}
      </div>
    {/if}
    <div class="crm-summary-row">
        <div class="crm-label">{ts}Reminder Month{/ts}</div>
        <div class="crm-content">{if $reminder_month EQ 0}No Reminder{else}{$strMonth|date_format:"%B"}{/if}</div>
    </div>
    <div class="crm-summary-row">
        <div class="crm-label">{ts}ID Number{/ts}</div>
        <div class="crm-content">{$id_number}</div>
    </div>
  </div>
</div>