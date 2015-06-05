{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{include file="CRM/common/pager.tpl" location="top"}

{strip}
<table class="selector row-highlight">
  <thead class="sticky">
  <tr>
    {if !$single and $context eq 'Search' }
        <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th>
    {/if}
    {assign var="softCreditColumns" value=0}
    {foreach from=$columnHeaders item=header}
        <th scope="col">
        {if $header.sort}
          {assign var='key' value=$header.sort}
          {$sort->_response.$key.link}
        {else}
          {$header.name}
	  {/if}
        </th>
	{if $header.name eq "Soft Credit For"}
	  {assign var='softCreditColumns' value=1}
	{/if}
    {/foreach}
  </tr>
  </thead> 

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr id="rowid{$row.contribution_id}" class="{cycle values="odd-row,even-row"}{if $row.cancel_date} cancelled{/if} crm-contribution_{$row.contribution_id}">
    {if !$single }
        {if $context eq 'Search' }
          {assign var=cbName value=$row.checkbox}
          <td>{$form.$cbName.html}</td>
   {/if}
    <td>{$row.contact_type}</td>
      <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    {/if}
    <td>{$row.receive_date}</td>
    <td>{$row.trxn_id}</td>
    <td>{$row.payment_instrument}</td>
    <td>{$row.total_amount|crmMoney:$row.currency}</td>
    <td>{$row.motivation_id}</td>
    <td>{$row.category_id}</td>
    <td>{$row.organisation_id}</td>
    <td>{$row.region_id}</td>
    <td>{$row.thankyou_date}</td>
    <td class="crm-contribution-status">
        {$row.contribution_status}<br />
        {if $row.cancel_date}
        {$row.cancel_date|crmDate}
        {/if}
    </td>
    <td>{$row.action|replace:'xx':$row.contribution_id}</td>
  </tr>
  {/foreach}

</table>
{/strip}

{include file="CRM/common/pager.tpl" location="bottom"}
