{literal}
<style type="text/css">
    #imgSearch:hover {
        cursor: pointer;
    }
</style>
{/literal}
{$form.oplock_ts.html}
<div class="crm-inline-edit-form">
  <div class="crm-inline-button">
    {include file="CRM/common/formButtons.tpl"}
  </div>

  <div class="crm-clear">
    <div class="crm-summary-row">
      <div class="crm-label">Must Acknowledge</div>
      <div class="crm-content">{$form.must_acknowledge.html}</div>
    </div>
    <div class="crm-summary-row">
      <div class="crm-label">Frequency</div>
      <div class="crm-content">{$form.frequency.html}</div>
    </div>
    <div class="crm-summary-row">
      <div class="crm-label">Preferred Method</div>
      <div class="crm-content">{$form.preferred_method.html}</div>
    </div>
  </div>
</div>