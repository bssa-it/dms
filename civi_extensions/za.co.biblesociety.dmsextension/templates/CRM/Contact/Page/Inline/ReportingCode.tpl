{* template for building Reporting Codes block *}
{crmAPI var='result' entity='ContactReportingCode' action='get' sequential=1 contact_id=$contactId}
{foreach from=$result.values item=contactreportingcode}
  {assign var="organisationId" value=$contactreportingcode.organisation_id}
  {assign var="categoryId" value=$contactreportingcode.category_id}
{/foreach}

{* Category Details *}
{crmAPI var='cat' entity='Category' action='get' sequential=1 cat_id=$categoryId}
{foreach from=$cat.values item=category}
  {assign var="categoryName" value=$category.cat_name}
{/foreach}

{* Department Details *}
{assign var="departmentId" value=$organisationId|substr:0:1}
{crmAPI var='dp' entity='Department' action='get' sequential=1 dep_id=$departmentId}
{foreach from=$dp.values item=department}
  {assign var="departmentName" value=$department.dep_name}
{/foreach}

{* Organisation Id details *}
{crmAPI var='org' entity='Organisation' action='get' sequential=1 org_id=$organisationId}
{foreach from=$org.values item=organisation}
  {assign var="organisationName" value=$organisation.org_name}
  {assign var="regionId" value=$organisation.org_region}
{/foreach}

{* Region Details *}
{crmAPI var='reg' entity='Region' action='get' sequential=1 reg_id=$regionId}
{foreach from=$reg.values item=region}
  {assign var="regionName" value=$region.reg_name}
{/foreach}

<div id="crm-reportingcodes-content" {if $permission EQ 'edit'} class="crm-inline-edit" data-edit-params='{ldelim}"cid": "{$contactId}", "class_name": "CRM_Contact_Form_Inline_ReportingCode"{rdelim}'{/if}>
  <div class="crm-clear crm-inline-block-content" {if $permission EQ 'edit'}title="{ts}Edit reporting codes{/ts}"{/if}>
    {if $permission EQ 'edit'}
      <div class="crm-edit-help">
        <span class="batch-edit"></span>{ts}Edit reporting codes{/ts}
      </div>
    {/if}
       <div class="crm-summary-row">
        <div class="crm-label">{ts}Department{/ts}</div>
        <div class="crm-content">{$departmentId} - {$departmentName}</div>
      </div>
      <div class="crm-summary-row">
        <div class="crm-label">{ts}Congregation{/ts}</div>
        <div class="crm-content">{$organisationId} - {$organisationName}</div>
      </div>
      <div class="crm-summary-row">
        <div class="crm-label">{ts}Region{/ts}</div>
        <div class="crm-content">{$regionId} - {$regionName}</div>
      </div>
      <div class="crm-summary-row">
        <div class="crm-label">{ts}Category{/ts}</div>
        <div class="crm-content">{$categoryId} - {$categoryName}</div>
      </div>
  </div>
</div>