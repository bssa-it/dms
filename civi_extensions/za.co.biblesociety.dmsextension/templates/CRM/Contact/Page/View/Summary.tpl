<!-- DMS Search box -->
{literal}
<style type="text/css">
    #searchFrm {
        position: absolute;
        z-index: 1000;
        min-height: 400px;
        height: auto;
        overflow: hidden;
        width: 600px;
        border: 5px solid #000;
        border-radius: 5px;
        padding: 10px;
        background-color: #FFF;
        top: 400px;
        left: 600px;
    }
    #theadOrgNames {
        background-color: #EEE;
    }
    #tdPrev:hover,#tdNext:hover {
        cursor: pointer;
        text-shadow: 0px 0px 10px #0F0;
    }
    #tdPrev,#tdNext {
        vertical-align: middle;
    }
    #tblOrganisations tr:nth-child(even) {
        background-color: #EEE; 
    }
    #inpSearch {
        width: 150px;
        margin: 0px;
        margin-left: 5px;
    }
    #tblOrganisations {
        font-size: 8pt;
    }
    .divControlLeft {
        float:left;
    }
    .divControlRight {
        float:right;
    }
    .organisationRow {
        cursor:pointer;
    }
    .dmsContainer {
    background-color: #254B7C;
      padding: 3px;
      width: 98%;
      display: block;
      clear: both;
      margin-top: 5px;
      height: auto;
      overflow: hidden;
    }
    .dmsContainer h3 {
        background-color: #254B7C;
        color: #FFF;
    }
    .dms-block {
        float: left;
        width: 33%;
    }
    .dmsContainer h4 {
        margin: 0;
        background-color: #254B7C;
        color: #FFF;
    }
</style>
{/literal}

<div id="searchFrm" style="display:none">
    <h3>Find Organisations</h3>
    <div id="frmContent">
        <input type="hidden" id="prevOffset" value="0" />
        <input type="hidden" id="nextOffset" value="15" />
        <table align="center" width="100%" cellpadding="5" cellspacing="0" id="tblOrganisations">
            <thead id="theadOrgNames">
                <tr>
                    <td>Id</td>
                    <td>Congregation Name</td>
                    <td>Region</td>
                </tr>
            </thead>
            <tbody id="tbodyOrgNames">
                <tr><td colspan="3">No Organisations found</td></tr>
            </tbody>
            <tfoot>
                <tr>
                    <td id="tdPrev">&nbsp;</td>
                    <td id="tdVariables">&nbsp;</td>
                    <td id="tdNext">next</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div>
        <div class="divControlLeft"><input class="cancel crm-form-submit cancel" name="_organisationIdSearch_cancel" value="Cancel" type="button" id="_organisationIdSearch_cancel" /></div>
        <div class="divControlRight">
            <label for="inpSearch">Congregation Id</label><input type="text" id="inpSearch" value="" />
            <input class="validate crm-form-submit default" accesskey="S" name="_organisationIdSearch_submit" value="Search" type="submit" id="_organisationIdSearch_submit" />
        </div>
    </div>
</div>
<!-- end DMS Search box -->

{if $action eq 2}
  {include file="CRM/Contact/Form/Contact.tpl"}
{else}

  {include file="CRM/common/wysiwyg.tpl" includeWysiwygEditor=true}

  <div class="crm-summary-contactname-block crm-inline-edit-container">
    <div class="crm-summary-block" id="contactname-block">
     {include file="CRM/Contact/Page/Inline/ContactName.tpl"}
    </div>
  </div>

  {if !$summaryPrint}
    <div class="crm-actions-ribbon">
      <ul id="actions">
        {assign var='urlParams' value="reset=1"}
        {if $searchKey}
          {assign var='urlParams' value=$urlParams|cat:"&key=$searchKey"}
        {/if}
        {if $context}
          {assign var='urlParams' value=$urlParams|cat:"&context=$context"}
        {/if}

        {* CRM-12735 - Conditionally include the Actions and Edit buttons if contact is NOT in trash.*}
        {if !$isDeleted}
          {if call_user_func(array('CRM_Core_Permission','check'), 'access CiviCRM')}
            <li class="crm-contact-activity crm-summary-block">
              {include file="CRM/Contact/Page/Inline/Actions.tpl"}
            </li>
          {/if}
          {* Include Edit button if contact has 'edit contacts' permission OR user is viewing their own contact AND has 'edit my contact' permission. CRM_Contact_Page_View::checkUserPermission handles this and assigns $permission true as needed. *}
          {if $permission EQ 'edit'}
            <li>
              {assign var='editParams' value=$urlParams|cat:"&action=update&cid=$contactId"}
              <a href="{crmURL p='civicrm/contact/add' q=$editParams}" class="edit button" title="{ts}Edit{/ts}">
              <span><div class="icon edit-icon"></div>{ts}Edit{/ts}</span>
              </a>
            </li>
          {/if}
        {/if}

        {* Check for permissions to provide Restore and Delete Permanently buttons for contacts that are in the trash. *}
        {if (call_user_func(array('CRM_Core_Permission','check'), 'access deleted contacts') and
          $is_deleted)}
          <li class="crm-contact-restore">
            <a href="{crmURL p='civicrm/contact/view/delete' q="reset=1&cid=$contactId&restore=1"}" class="delete button" title="{ts}Restore{/ts}">
              <span><div class="icon restore-icon"></div>{ts}Restore from Trash{/ts}</span>
            </a>
          </li>

          {if call_user_func(array('CRM_Core_Permission','check'), 'delete contacts')}
            <li class="crm-contact-permanently-delete">
              <a href="{crmURL p='civicrm/contact/view/delete' q="reset=1&delete=1&cid=$contactId&skip_undelete=1"}" class="delete button" title="{ts}Delete Permanently{/ts}">
                <span><div class="icon delete-icon"></div>{ts}Delete Permanently{/ts}</span>
              </a>
            </li>
          {/if}

        {elseif call_user_func(array('CRM_Core_Permission','check'), 'delete contacts')}
          {assign var='deleteParams' value="&reset=1&delete=1&cid=$contactId"}
          <li class="crm-delete-action crm-contact-delete">
            <a href="{crmURL p='civicrm/contact/view/delete' q=$deleteParams}" class="delete button" title="{ts}Delete{/ts}">
              <span><div class="icon delete-icon"></div>{ts}Delete Contact{/ts}</span>
            </a>
          </li>
        {/if}

        {* Previous and Next contact navigation when accessing contact summary from search results. *}
        {if $nextPrevError}
          <li class="crm-next-action">
            {help id="id-next-prev-buttons"}&nbsp;
          </li>
        {else}
          {if $nextContactID}
            {assign var='viewParams' value=$urlParams|cat:"&cid=$nextContactID"}
            <li class="crm-next-action">
              <a href="{crmURL p='civicrm/contact/view' q=$viewParams}" class="view button" title="{$nextContactName}">
                <span title="{$nextContactName}"><div class="icon next-icon"></div>{ts}Next{/ts}</span>
              </a>
            </li>
          {/if}
          {if $prevContactID}
            {assign var='viewParams' value=$urlParams|cat:"&cid=$prevContactID"}
            <li class="crm-previous-action">
              <a href="{crmURL p='civicrm/contact/view' q=$viewParams}" class="view button" title="{$prevContactName}">
                <span title="{$prevContactName}"><div class="icon previous-icon"></div>{ts}Previous{/ts}</span>
              </a>
            </li>
          {/if}
        {/if}

        {if !empty($groupOrganizationUrl)}
          <li class="crm-contact-associated-groups">
            <a href="{$groupOrganizationUrl}" class="associated-groups button" title="{ts}Associated Multi-Org Group{/ts}">
              <span><div class="icon associated-groups-icon"></div>{ts}Associated Multi-Org Group{/ts}</span>
            </a>
          </li>
        {/if}
      </ul>
      <div class="clear"></div>
    </div><!-- .crm-actions-ribbon -->
  {/if}

  <div class="crm-block crm-content-block crm-contact-page crm-inline-edit-container">
    <div id="mainTabContainer">
      <ul class="crm-contact-tabs-list">
        <li id="tab_summary" class="crm-tab-button ui-corner-all">
          <a href="#contact-summary" title="{ts}Summary{/ts}">
            <span> </span> {ts}Summary{/ts}
            <em></em>
          </a>
        </li>
        {foreach from=$allTabs key=tabName item=tabValue}
          <li id="tab_{$tabValue.id}" class="crm-tab-button ui-corner-all crm-count-{$tabValue.count}{if isset($tabValue.class)} {$tabValue.class}{/if}">
            <a href="{$tabValue.url}" title="{$tabValue.title}">
              {$tabValue.title}
              {if empty($tabValue.hideCount)}<em>{$tabValue.count}</em>{/if}
            </a>
          </li>
        {/foreach}
      </ul>

      <div id="contact-summary" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
        {if (isset($hookContentPlacement) and ($hookContentPlacement neq 3)) or empty($hookContentPlacement)}

          {if !empty($hookContent) and isset($hookContentPlacement) and $hookContentPlacement eq 2}
            {include file="CRM/Contact/Page/View/SummaryHook.tpl"}
          {/if}

          <div class="contactTopBar contact_panel">
            <div class="contactCardLeft">
              {crmRegion name="contact-basic-info-left"}
                <div class="crm-summary-contactinfo-block">
                  <div class="crm-summary-block" id="contactinfo-block">
                    {include file="CRM/Contact/Page/Inline/ContactInfo.tpl"}
                  </div>
                </div>
              {/crmRegion}
            </div> <!-- end of left side -->
            <div class="contactCardRight">
              {crmRegion name="contact-basic-info-right"}
              {if !empty($imageURL)}
                <div id="crm-contact-thumbnail">
                  {include file="CRM/Contact/Page/ContactImage.tpl"}
                </div>
              {/if}
                <div class="{if !empty($imageURL)} float-left{/if}">
                  <div class="crm-clear crm-summary-block">
                    <div class="crm-summary-row">
                      <div class="crm-label" id="tagLink">
                        <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=$contactId&selectedChild=tag"}"
                           title="{ts}Edit Tags{/ts}">{ts}Tags{/ts}</a>
                      </div>
                      <div class="crm-content" id="tags">{$contactTag}</div>
                    </div>
                    <div class="crm-summary-row">
                      <div class="crm-label">{ts}Contact Type{/ts}</div>
                      <div class="crm-content crm-contact_type_label">
                        {if isset($contact_type_label)}{$contact_type_label}{/if}
                      </div>
                    </div>
                    <div class="crm-summary-row">
                      <div class="crm-label">
                        {ts}CiviCRM ID{/ts}{if !empty($userRecordUrl)} / {ts}User ID{/ts}{/if}
                      </div>
                      <div class="crm-content">
                        <span class="crm-contact-contact_id">{$contactId}</span>
                        {if !empty($userRecordUrl)}
                          <span class="crm-contact-user_record_id">
                            &nbsp;/&nbsp;<a title="View user record" class="user-record-link"
                                            href="{$userRecordUrl}">{$userRecordId}</a>
                          </span>
                        {/if}
                      </div>
                    </div>
                    <div class="crm-summary-row">
                      <div class="crm-label">{ts}External ID{/ts}</div>
                      <div class="crm-content crm-contact_external_identifier_label">
                        {if isset($external_identifier)}{$external_identifier}{/if}
                      </div>
                    </div>
                  </div>
                </div>
              {/crmRegion}
            </div>
            <!-- end of right side -->
        </div>
        <div class="contact_details">
          <div class="contact_panel">
            <div class="contactCardLeft">
              {crmRegion name="contact-details-left"}
                <div >
                  {if $showEmail}
                    <div class="crm-summary-email-block crm-summary-block" id="email-block">
                      {include file="CRM/Contact/Page/Inline/Email.tpl"}
                    </div>
                  {/if}
                  {if $showWebsite}
                    <div class="crm-summary-website-block crm-summary-block" id="website-block">
                      {include file="CRM/Contact/Page/Inline/Website.tpl"}
                    </div>
                  {/if}
                </div>
              {/crmRegion}
            </div><!-- #contactCardLeft -->

            <div class="contactCardRight">
              {crmRegion name="contact-details-right"}
                <div>
                  {if $showPhone}
                    <div class="crm-summary-phone-block crm-summary-block" id="phone-block">
                      {include file="CRM/Contact/Page/Inline/Phone.tpl"}
                    </div>
                  {/if}
                  {if $showIM}
                    <div class="crm-summary-im-block crm-summary-block" id="im-block">
                      {include file="CRM/Contact/Page/Inline/IM.tpl"}
                    </div>
                  {/if}
                  {if $showOpenID}
                    <div class="crm-summary-openid-block crm-summary-block" id="openid-block">
                      {include file="CRM/Contact/Page/Inline/OpenID.tpl"}
                    </div>
                  {/if}
                </div>
              {/crmRegion}
            </div><!-- #contactCardRight -->

            <div class="clear"></div>
          </div><!-- #contact_panel -->
          {if $showAddress}
            <div class="contact_panel">
              {assign var='locationIndex' value=1}
              {if $address}
                {foreach from=$address item=add key=locationIndex}
                  <div class="{if $locationIndex is odd}contactCardLeft{else}contactCardRight{/if} crm-address_{$locationIndex} crm-address-block crm-summary-block">
                    {include file="CRM/Contact/Page/Inline/Address.tpl"}
                  </div>
                {/foreach}
                {assign var='locationIndex' value=$locationIndex+1}
              {/if}
              {* add new link *}
              {if $permission EQ 'edit'}
                {assign var='add' value=0}
                <div class="{if $locationIndex is odd}contactCardLeft{else}contactCardRight{/if} crm-address-block crm-summary-block">
                  {include file="CRM/Contact/Page/Inline/Address.tpl"}
                </div>
              {/if}

              </div> <!-- end of contact panel -->
            {/if}
            <div class="contact_panel">
              {if $showCommunicationPreferences}
                <div class="contactCardLeft">
                  <div class="crm-summary-comm-pref-block">
                    <div class="crm-summary-block" id="communication-pref-block" >
                      {include file="CRM/Contact/Page/Inline/CommunicationPreferences.tpl"}
                    </div>
                  </div>
                </div> <!-- contactCardLeft -->
              {/if}
                            
              {if $contact_type eq 'Individual' AND $showDemographics}
                <div class="contactCardRight">
                  <div class="crm-summary-demographic-block">
                    <div class="crm-summary-block" id="demographic-block">
                      {include file="CRM/Contact/Page/Inline/Demographics.tpl"}
                    </div>
                  </div>
                </div> <!-- contactCardRight -->
              {/if}
              
               <!-- DMS SECTION -->
              
               <div class="clear"></div>
              <div class="dmsContainer">
                <h3>DMS</h3>
                <div class="crm-summary-block dms-block">
                    {include file="CRM/Contact/Page/Inline/ReportingCode.tpl"}
                </div>
                <div class="crm-summary-block dms-block">
                    {include file="CRM/Contact/Page/Inline/AcknowledgementPreferences.tpl"}
                </div>
                <div class="crm-summary-block dms-block">
                    {include file="CRM/Contact/Page/Inline/OtherData.tpl"}
                </div>
                
              </div>
              <!-- END DMS SECTION -->
              
              <div class="clear"></div>
                <div class="separator"></div>
              </div> <!-- contact panel -->
            </div><!--contact_details-->

            {if $showCustomData}
              <div id="customFields">
                <div class="contact_panel">
                  <div class="contactCardLeft">
                    {include file="CRM/Contact/Page/View/CustomDataView.tpl" side='1'}
                  </div><!--contactCardLeft-->
                  <div class="contactCardRight">
                    {include file="CRM/Contact/Page/View/CustomDataView.tpl" side='0'}
                  </div>

                  <div class="clear"></div>
                </div>
              </div>
            {/if}
            
            {if !empty($hookContent) and isset($hookContentPlacement) and $hookContentPlacement eq 1}
              {include file="CRM/Contact/Page/View/SummaryHook.tpl"}
            {/if}
          {else}
             {include file="CRM/Contact/Page/View/SummaryHook.tpl"}
          {/if}
        </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div><!-- /.crm-content-block -->
{/if}

{* CRM-10560 *}
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $('.crm-inline-edit-container').crmFormContactLock({
    ignoreLabel: "{/literal}{ts escape='js'}Ignore{/ts}{literal}",
    saveAnywayLabel: "{/literal}{ts escape='js'}Save Anyway{/ts}{literal}",
    reloadLabel: "{/literal}{ts escape='js'}Reload Page{/ts}{literal}"
  });
});
    
    /* DMS fn */
    var intLimit = 15;
    cj("#_organisationIdSearch_cancel").click(function(){
        initializeVars();
        cj("#searchFrm").hide();
    });
    cj("#_organisationIdSearch_submit").click(function(){
        initializeVars();
        fillOrganisationTable("prev");
        showPrevLabel();
    });
    cj("#tdNext").click(function(){
       fillOrganisationTable("next"); 
       
       var prevOffset = cj("#prevOffset").val();
       var newPrevOffset = parseInt(prevOffset)+intLimit;
       cj("#prevOffset").val(newPrevOffset);
       
       var nextOffset = cj("#nextOffset").val();
       var newNextOffset = parseInt(nextOffset)+intLimit;
       cj("#nextOffset").val(newNextOffset);
       
       showPrevLabel();        
    });
    cj("#tdPrev").click(function(){

       var prevOffset = cj("#prevOffset").val();
       var newPrevOffset = (prevOffset==0) ? 0:parseInt(prevOffset)-intLimit;
       cj("#prevOffset").val(newPrevOffset);
       
       var nextOffset = cj("#nextOffset").val();
       var newNextOffset = (nextOffset<=intLimit) ? intLimit:parseInt(nextOffset)-intLimit;
       cj("#nextOffset").val(newNextOffset);
        
        fillOrganisationTable("prev");
        showPrevLabel();
    });
    function bindRowClick(){
        cj("#tbodyOrgNames tr").click(function(){
            cj("#_organisationIdSearch_cancel").click();
            cj("#divOrganisationId").val(cj(this).attr('value'));
            getOrganisationName(); 
        });
    }
    function showPrevLabel(){
        var prevOffset = cj("#prevOffset").val();
        var lblPrev = (prevOffset==0) ? '&nbsp;':"previous";
        cj("#tdPrev").empty().append(lblPrev);
    }
    function initializeVars(){
        cj("#prevOffset").val('0');
        cj("#nextOffset").val(intLimit);
    }
    function fillOrganisationTable(direction){
        var prevOffset = cj("#prevOffset").val();
        var nextOffset = cj("#nextOffset").val();
        var offset = (direction=='prev') ? prevOffset:nextOffset;
        var orgId = cj("#inpSearch").val();
        var parms = {
          "sequential": 1,
          "options": {"offset":offset,"sort":"org_id","limit":intLimit}
        };
        if (orgId.length>0) parms["org_id"] = {"LIKE":orgId+'%'};
        
        CRM.api3('Organisation', 'get', parms).done(function(data) {
          if (data.values.length>0) {
              cj("#tbodyOrgNames").empty();
              for (var i=0;i<data.values.length;i++) {
                var newRow = '<tr value="'+data.values[i].org_id+'" class="organisationRow"><td>'+data.values[i].org_id+'</td><td>'+data.values[i].org_name+'</td><td>'+data.values[i].org_region+'</td></tr>';
                cj("#tbodyOrgNames").append(newRow);
              }
              var lblNext = (data.values.length<intLimit) ? '&nbsp;':"next";
              cj("#tdNext").empty().append(lblNext);
              bindRowClick();
          }
        });
        cj("#searchFrm").show();
    }
    /* End DMS fn */
</script>
{/literal}