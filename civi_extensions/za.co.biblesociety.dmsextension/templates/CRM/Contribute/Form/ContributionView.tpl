{* DMS Custom View *}

{* Transaction Reporting Detail *}
{crmAPI var='result' entity='Transaction' action='get' sequential=1 contribution_id=$contribution_id}
{foreach from=$result.values item=transaction}
  {assign var="motivationId" value=$transaction.motivation_id}
  {assign var="categoryId" value=$transaction.category_id}
  {assign var="regionId" value=$transaction.region_id}
  {assign var="organisationId" value=$transaction.organisation_id}
{/foreach}

{* Category Details *}
{crmAPI var='cat' entity='Category' action='get' sequential=1 cat_id=$categoryId}
{foreach from=$cat.values item=category}
  {assign var="categoryName" value=$category.cat_name}
{/foreach}

{* Organisation Id details *}
{crmAPI var='org' entity='Organisation' action='get' sequential=1 org_id=$organisationId}
{foreach from=$org.values item=organisation}
  {assign var="organisationName" value=$organisation.org_name}
{/foreach}

{* Department Details *}
{assign var="departmentId" value=$organisationId|substr:0:1}
{crmAPI var='dp' entity='Department' action='get' sequential=1 dep_id=$departmentId}
{foreach from=$dp.values item=department}
  {assign var="departmentName" value=$department.dep_name}
{/foreach}

{* Motivation Id details *}
{crmAPI var='motive' entity='Motivation' action='get' sequential=1 motivation_id=$motivationId}
{foreach from=$motive.values item=motivation}
  {assign var="motivationName" value=$motivation.description}
{/foreach}

{* Region Details *}
{crmAPI var='reg' entity='Region' action='get' sequential=1 reg_id=$regionId}
{foreach from=$reg.values item=region}
  {assign var="regionName" value=$region.reg_name}
{/foreach}

<div class="crm-block crm-content-block crm-contribution-view-form-block">
<div class="action-link">
  <div class="crm-submit-buttons">
    {if call_user_func(array('CRM_Core_Permission','check'), 'edit contributions')}
      {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context"}
      {if ( $context eq 'fulltext' || $context eq 'search' ) && $searchKey}
        {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&key=$searchKey"}
      {/if}
      <a class="button" href="{crmURL p='civicrm/contact/view/contribution' q=$urlParams}" accesskey="e"><span>
          <div class="icon edit-icon"></div>{ts}Edit{/ts}</span>
      </a>
    {/if}
    {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviContribute')}
      {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context"}
      {if ( $context eq 'fulltext' || $context eq 'search' ) && $searchKey}
        {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&key=$searchKey"}
      {/if}
      <a class="button" href="{crmURL p='civicrm/contact/view/contribution' q=$urlParams}"><span>
          <div class="icon delete-icon"></div>{ts}Delete{/ts}</span>
      </a>
    {/if}
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>
</div>
<table class="crm-info-panel">
    
  {if $organisationId}
    <tr>
      <td class="label">{ts}Department{/ts}</td>
      <td class="bold">{$departmentId} - {$departmentName}</td>
    </tr>
  {/if}
    
  <tr>
    <td class="label">{ts}From{/ts}</td>
    <td class="bold">{$displayName}</td>
  </tr>
  <tr>
    <td class="label">{ts}Financial Type{/ts}</td>
    <td>{$financial_type}{if $is_test} {ts}(test){/ts} {/if}</td>
  </tr>
  {if $lineItem}
    <tr>
      <td class="label">{ts}Contribution Amount{/ts}</td>
      <td>{include file="CRM/Price/Page/LineItem.tpl" context="Contribution"}
        {if $contribution_recur_id}
          <strong>{ts}Recurring Contribution{/ts}</strong>
          <br/>
          {ts}Installments{/ts}: {if $recur_installments}{$recur_installments}{else}{ts}(ongoing){/ts}{/if}, {ts}Interval{/ts}: {$recur_frequency_interval} {$recur_frequency_unit}(s)
        {/if}
      </td>
    </tr>
  {else}
    <tr>
      <td class="label">{ts}Total Amount{/ts}</td>
      <td><strong>{$total_amount|crmMoney:$currency}</strong>&nbsp;
        {if $contribution_recur_id}
          <strong>{ts}Recurring Contribution{/ts}</strong>
          <br/>
          {ts}Installments{/ts}: {if $recur_installments}{$recur_installments}{else}{ts}(ongoing){/ts}{/if}, {ts}Interval{/ts}: {$recur_frequency_interval} {$recur_frequency_unit}(s)
        {/if}
      </td>
    </tr>
  {/if}

  {if $fee_amount}
    <tr>
      <td class="label">{ts}Fee Amount{/ts}</td>
      <td>{$fee_amount|crmMoney:$currency}</td>
    </tr>
  {/if}
  {if $net_amount}
    <tr>
      <td class="label">{ts}Net Amount{/ts}</td>
      <td>{$net_amount|crmMoney:$currency}</td>
    </tr>
  {/if}

  <tr>
    <td class="label">{ts}Received{/ts}</td>
    <td>{if $receive_date}{$receive_date|crmDate}{else}({ts}not available{/ts}){/if}</td>
  </tr>
  {if $to_financial_account }
    <tr>
      <td class="label">{ts}Received Into{/ts}</td>
      <td>{$to_financial_account}</td>
    </tr>
  {/if}
  <tr>
    <td class="label">{ts}Contribution Status{/ts}</td>
    <td {if $contribution_status_id eq 3} class="font-red bold"{/if}>{$contribution_status}
      {if $contribution_status_id eq 2} {if $is_pay_later}: {ts}Pay Later{/ts} {else} : {ts}Incomplete Transaction{/ts} {/if}{/if}</td>
  </tr>

  {if $cancel_date}
    <tr>
      <td class="label">{ts}Cancelled / Refunded Date{/ts}</td>
      <td>{$cancel_date|crmDate}</td>
    </tr>
    {if $cancel_reason}
      <tr>
        <td class="label">{ts}Cancellation / Refund Reason{/ts}</td>
        <td>{$cancel_reason}</td>
      </tr>
    {/if}
  {/if}
  <tr>
    <td class="label">{ts}Paid By{/ts}</td>
    <td>{$payment_instrument}</td>
  </tr>

  {if $payment_instrument eq 'Check'|ts}
    <tr>
      <td class="label">{ts}Check Number{/ts}</td>
      <td>{$check_number}</td>
    </tr>
  {/if}
  <tr>
    <td class="label">{ts}Source{/ts}</td>
    <td>{$source}</td>
  </tr>

  {if $campaign}
    <tr>
      <td class="label">{ts}Campaign{/ts}</td>
      <td>{$campaign}</td>
    </tr>
  {/if}

  {if $contribution_page_title}
    <tr>
      <td class="label">{ts}Online Contribution Page{/ts}</td>
      <td>{$contribution_page_title}</td>
    </tr>
  {/if}
  {if $receipt_date}
    <tr>
      <td class="label">{ts}Receipt Sent{/ts}</td>
      <td>{$receipt_date|crmDate}</td>
    </tr>
  {/if}
  {foreach from=$note item="rec"}
    {if $rec }
      <tr>
        <td class="label">{ts}Note{/ts}</td>
        <td>{$rec}</td>
      </tr>
    {/if}
  {/foreach}

  {if $trxn_id}
    <tr>
      <td class="label">{ts}Transaction ID{/ts}</td>
      <td>{$trxn_id}</td>
    </tr>
  {/if}

  {if $invoice_id}
    <tr>
      <td class="label">{ts}Invoice ID{/ts}</td>
      <td>{$invoice_id}&nbsp;</td>
    </tr>
  {/if}

  {if $thankyou_date}
    <tr>
      <td class="label">{ts}Thank-you Sent{/ts}</td>
      <td>{$thankyou_date|crmDate}</td>
    </tr>
  {/if}
  
  {if $organisationId}
    <tr>
      <td class="label">{ts}Congregation Id{/ts}</td>
      <td>{$organisationId} - {$organisationName}</td>
    </tr>
  {/if}
  
  {if $regionId}
    <tr>
      <td class="label">{ts}Region{/ts}</td>
      <td>{$regionId} - {$regionName}</td>
    </tr>
  {/if}
  
  {if $categoryId}
    <tr>
      <td class="label">{ts}Category{/ts}</td>
      <td>{$categoryId} - {$categoryName}</td>
    </tr>
  {/if}
  
  {if $motivationId}
    <tr>
      <td class="label">{ts}Motivation Code{/ts}</td>
      <td>{$motivationId} - {$motivationName}</td>
    </tr>
  {/if}
  
</table>

{if count($softContributions)} {* We show soft credit name with PCP section if contribution is linked to a PCP. *}
  <div class="crm-accordion-wrapper crm-soft-credit-pane">
    <div class="crm-accordion-header">
      {ts}Soft Credit{/ts}
    </div>
    <div class="crm-accordion-body">
      <table class="crm-info-panel crm-soft-credit-listing">
        {foreach from=$softContributions item="softCont"}
          <tr>
            <td>
              <a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$softCont.contact_id`"}"
                 title="{ts}View contact record{/ts}">{$softCont.contact_name}
              </a>
            </td>
            <td>{$softCont.amount|crmMoney:$currency}
              {if $softCont.soft_credit_type_label}
                ({$softCont.soft_credit_type_label})
              {/if}
            </td>
          </tr>
        {/foreach}
      </table>
    </div>
  </div>
{/if}

{if $premium}
  <div class="crm-accordion-wrapper ">
    <div class="crm-accordion-header">
      {ts}Premium Information{/ts}
    </div>
    <div class="crm-accordion-body">
      <table class="crm-info-panel">
        <td class="label">{ts}Premium{/ts}</td>
        <td>{$premium}</td>
        <td class="label">{ts}Option{/ts}</td>
        <td>{$option}</td>
        <td class="label">{ts}Fulfilled{/ts}</td>
        <td>{$fulfilled|truncate:10:''|crmDate}</td>
      </table>
    </div>
  </div>
{/if}

{if $pcp_id}
  <div id='PCPView' class="crm-accordion-wrapper ">
    <div class="crm-accordion-header">
      {ts}Personal Campaign Page Contribution Information{/ts}
    </div>
    <div class="crm-accordion-body">
      <table class="crm-info-panel">
        <tr>
          <td class="label">{ts}Personal Campaign Page{/ts}</td>
          <td><a href="{crmURL p="civicrm/pcp/info" q="reset=1&id=`$pcp_id`"}">{$pcp_title}</a><br/>
            <span class="description">{ts}Contribution was made through this personal campaign page.{/ts}</span>
          </td>
        </tr>
        <tr>
          <td class="label">{ts}Soft Credit To{/ts}</td>
          <td><a href="{crmURL p="civicrm/contact/view" q="reset=1&cid=`$pcp_soft_credit_to_id`"}" id="view_contact"
                 title="{ts}View contact record{/ts}">{$pcp_soft_credit_to_name}</a></td>
        </tr>
        <tr>
          <td class="label">{ts}In Public Honor Roll?{/ts}</td>
          <td>{if $pcp_display_in_roll}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
        </tr>
        {if $pcp_roll_nickname}
          <tr>
            <td class="label">{ts}Honor Roll Name{/ts}</td>
            <td>{$pcp_roll_nickname}</td>
          </tr>
        {/if}
        {if $pcp_personal_note}
          <tr>
            <td class="label">{ts}Personal Note{/ts}</td>
            <td>{$pcp_personal_note}</td>
          </tr>
        {/if}
      </table>
    </div>
  </div>
{/if}

{include file="CRM/Custom/Page/CustomDataView.tpl"}

{if $billing_address}
  <fieldset>
    <legend>{ts}Billing Address{/ts}</legend>
    <div class="form-item">
      {$billing_address|nl2br}
    </div>
  </fieldset>
{/if}

<div class="crm-submit-buttons">
  {if call_user_func(array('CRM_Core_Permission','check'), 'edit contributions')}
    {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context"}
    {if ( $context eq 'fulltext' || $context eq 'search' ) && $searchKey}
      {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=update&context=$context&key=$searchKey"}
    {/if}
    <a class="button" href="{crmURL p='civicrm/contact/view/contribution' q=$urlParams}" accesskey="e"><span><div
          class="icon edit-icon"></div>{ts}Edit{/ts}</span></a>
  {/if}
  {if call_user_func(array('CRM_Core_Permission','check'), 'delete in CiviContribute')}
    {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context"}
    {if ( $context eq 'fulltext' || $context eq 'search' ) && $searchKey}
      {assign var='urlParams' value="reset=1&id=$id&cid=$contact_id&action=delete&context=$context&key=$searchKey"}
    {/if}
    <a class="button" href="{crmURL p='civicrm/contact/view/contribution' q=$urlParams}"><span><div
          class="icon delete-icon"></div>{ts}Delete{/ts}</span></a>
  {/if}
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
</div>