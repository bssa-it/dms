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
      <div class="crm-label">{$form.organisation_id.label}</div>
      <div class="crm-content">{$form.organisation_id.html} <img src="/dms/img/search-15.png" id="imgSearch" /></div>
    </div>
    <div class="crm-summary-row" id="divOrganisationNameRow">
      <div class="crm-label">Congregation Name</div>
      <div class="crm-content" id="divOrganisationName">{$form.organisationName.value}</div>
    </div>
    {*$form|@print_r*}
    <div class="crm-summary-row">
      <div class="crm-label">{$form.category_id.label}</div>
      <div class="crm-content">{$form.category_id.html}</div>
    </div> 
  </div>
</div>
<!-- Find organisation id -->

{literal}
<script type="text/javascript">
    function getOrganisationName( ) {
        var orgId = cj("#divOrganisationId").val();
        cj("#divOrganisationNameRow").hide(500);
        
        if (orgId.length!=8) {
            var orgName = '';
        } else {
            var orgName = 'Not Found';
            CRM.api3('Organisation', 'get', {
              "sequential": 1,
              "return": "org_name",
              "org_id": orgId
            }).done(function(data) {
                if (data.values[0]!=undefined) orgName = data.values[0].org_name;
                cj("#divOrganisationName").empty().append(orgName);   
            });
        }
        
        if (orgName.length>0) cj("#divOrganisationNameRow").show(500);  
    }
    
    function getCategoryName() {
        var catId = cj("#divCategoryId").val();
        cj("#divCategoryNameRow").hide(500);
        
        if (catId.length!=4) {
            var catName = '';
        } else {
            var catName = 'Not Found';
            CRM.api3('Category', 'get', {
              "sequential": 1,
              "cat_id": catId
            }).done(function(data) {
                if (data.values[0]!=undefined) catName = data.values[0].cat_name;
                cj("#divCategoryName").empty().append(catName);   
            });
        }
        
        if (catName.length>0) cj("#divCategoryNameRow").show(500);
    }
    
    cj("#imgSearch").click(function(){
        fillOrganisationTable("prev");   
    });
</script>
{/literal}