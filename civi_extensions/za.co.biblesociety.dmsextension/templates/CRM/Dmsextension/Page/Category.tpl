{* template for Category *}
{*include file="CRM/common/pager.tpl" location="top"}

{strip}
<table class="selector row-highlight">
{counter start=0 skip=1 print=false}
{foreach from=$categories item=c}
    <tr>
        <td>{$c.cat_id}</td>
        <td>{$c.cat_name}</td>
    </tr>
{/foreach}
</table>
{/strip}
{include file="CRM/common/pager.tpl" location="bottom"*}

{$categories|@print_r}