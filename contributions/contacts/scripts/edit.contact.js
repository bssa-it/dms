var departments = [];
var denominations = [];
var congregations = [];
var resultContent = [];
var contactTypes = [];

$(document).ready(function(){    
    $.get("/dms/get.contactTypes.php").done(function(data) { contactTypes = $.parseJSON(data) });
    $.get("/dms/get.departments.php").done(function(data){ departments = $.parseJSON(data); });
    fillDenominations();
    
    $("#dp").change(function () {
        fillDenominations();
        initEdit();
        setCongregation(); 
        $("#regionList").hide();
        $("#regionList").empty();
    });
    $("#dn").change(function () {
        setCongregation();
        $("#regionList").hide();
        $("#regionList").empty();
    });
    $("#cn").keyup(function () {
        $("#regionList").hide();
        $("#regionList").empty();
        if ($(this).val().length==0) {
            $("#organisation_id").val('');
            setCongregation();
        }
        for (var i=0;i<congregations.length;i++) {
            var orgId = congregations[i].org_id;
            var selOrgId = $("#orgIdPrefix").text()+''+$(this).val();
            if ($(this).val().length>0&&orgId.indexOf(selOrgId)==0) {
                var conId = congregations[i].org_id.substring(3);
                $("#regionList").show();
                $("#regionList").append('<div class="insertLink"><a class="aInsert" orgid="'+orgId+'" conreg="'+congregations[i].org_region+'" conid="'+conId+'" coname="' + congregations[i].org_name + '">' + conId + ' - ' + congregations[i].org_name + '</a></div>');
            } 
            if (orgId==selOrgId) {
                $("#lblCn").empty().append('<label for="cn">'+congregations[i].org_name+' ('+congregations[i].org_region+')<label>');
                $("#regionList").hide();
                $("#regionList").empty();
                $("#organisation_id").val(selOrgId);
                break;
            }
        }
        $(".aInsert").click(function(){
            $("#cn").val($(this).attr('conid'));
            $("#regionList").hide();
            $("#regionList").empty();
            $("#lblCn").empty().append($(this).attr('coname') + ' (' + $(this).attr('conreg') + ')');
            $("#organisation_id").val($(this).attr('orgid'));
        });
    });
    
    
    $("#btnSaveReportDetails").click(function () {
       var cfm = confirm("Are you sure you want to Save the changes you made.");
        if (cfm) {
            var $form = $( '#frmReportDetails' );
            $( "#frmDiv" ).empty().append('<img src="/dms/img/loading.gif" style="vertical-align: middle" /> saving...');
            var strUrl = $form.attr( "action" );
            $.post( 
                strUrl,
                $form.serialize()  
            ).done(function (data) {
               resultContent = $.parseJSON(data);
               reloadSummary();
               $("#reportDetails").click();
            });
        } 
    });
    $("#contact_sub_type").change(function(){
        $("#first_name,#last_name,#organization_name,#household_name").val('');
        for (var i=0;i<contactTypes.length;i++) {
            if (contactTypes[i].name==$(this).val()) {
                $("#contact_type").val(contactTypes[i].parent);
                break;
            }
        }
        $(".Organization, .Individual, .Household").hide();
        $("."+$("#contact_type").val()).show();
    });
    $("#btnSaveContact").click(function() {
        validate();
    });
    
    $("#btnContinueAnyway").click(function(){
        $("#duplicateCheck").val('N');
        validate();
    });
    $("#btnCancelSave").click(function(){
        $("#screenProtectorDiv").hide();
        $("#duplicateCheck").val('Y');
    });
    $("#first_name, #last_name").change(function(){
        $("#duplicateCheck").val('Y');
    });
    $("#btnAddPhone").click(function(){
       var tmpl = $("#tplPhone").html(); 
       $("#personalDetailsDiv").append("<div class=\"inputWrapperDiv\">"+tmpl+"</div>");
    });
    $("#btnAddEmail").click(function(){
       var tmpl = $("#tplEmail").html(); 
       $("#personalDetailsDiv").append("<div class=\"inputWrapperDiv\">"+tmpl+"</div>");
    });
    $("#btnAddAddress").click(function(){
       var tmpl = $("#tplAddress").html(); 
       $("#personalDetailsDiv").append("<div class=\"inputWrapperDiv  addressDiv\">"+tmpl+"</div>");
       addSuburbSearcher();
    });
});

function fillDenominations() {
    $.get("/dms/get.denominations.php",{dp: $("#dp").val()}).done(function(data){ 
        denominations = $.parseJSON(data); 
        resetDenominationSelect();
        fillCongregations();
    });
}
function resetDenominationSelect() {
    $("#dn").empty();
    for (var i=0;i<denominations.length;i++) {
        $("#dn").append($('<option>', {
            value: denominations[i].den_id,
            text: denominations[i].den_id + ' - ' +denominations[i].den_name
        }));   
    }
    $("#orgIdPrefix").text($("#dp").val()+''+$("#dn").val());
}

function fillCongregations() {
    $.get("/dms/get.congregations.php",{dp: $("#dp").val(),dn: $("#dn").val()}).done(function(data){ 
        congregations = $.parseJSON(data); 
        $("#orgIdPrefix").text($("#dp").val()+''+$("#dn").val());
    });
}

function initEdit() {
    $("#organisationIdDiv").hide();
    $("#currDep").hide();
    for (var i=0;i<departments.length;i++) {
        if ($("#dp").val()==departments[i].dep_id) {
            $("#newDep").css('background-color',departments[i].dep_chart_color);
            $("#newDep").css('border-color',departments[i].dep_chart_color);
            $("#depSelectDiv").css({marginLeft:'0px'});
            break;       
        }
    }
    $("#newDep").text($("#dp").val());
    $("#newDep").show();
}

function setCongregation() {
    $("#regionDiv").text('?');
    $("#cn").val('');
    $("#lblCn").text('????');
    $("#cn").focus();
    fillCongregations();
}

function validate() {
    switch($("#contact_type").val()) {
        case "Individual":
            if ($("#first_name").val().length==0&&$("#last_name").val().length==0) {
                alert("Please enter the contact's name.");
                $("#first_name,#last_name").each(function(){
                    if($(this).val().length==0) {
                       $(this).focus();
                       return false;
                    }
                });
                return;
            }
            break;
        case "Organization":
            if ($("#organization_name").val().length==0) {
                alert("Please enter the Organization's name.");
                $("#organization_name").focus();
                return;
            }
            break;
        case "Household":
            if ($("#household_name").val().length==0) {
                alert("Please enter the Household's name.");
                $("#household_name").focus();
                return;
            }
            break;
    }
    if ($("#organisation_id").val().length==0) {
        alert('Please complete the congregation code.');
        $("#cn").focus();
        return;
    }
    if ($("#category_id").val().length==0) {
        alert('Please select the category code.');
        $("#category_id").focus();
        return;
    }
   
    var postVars = {srch_database: 'civicrm',format: 'json'};
    if ($("#first_name").val().length>0) postVars.srch_firstName = $("#first_name").val();
    if ($("#last_name").val().length>0) postVars.srch_lastName = $("#last_name").val();
    if ($("#organization_name").val().length>0) postVars.srch_organizationName = $("#organization_name").val();
    if ($("#household_name").val().length>0) postVars.srch_householdName = $("#household_name").val();
    
    $("#duplicatesHdr").empty().append('Checking for duplicates');
    if ($("#duplicateCheck").val()=='Y') {
        $("#duplicatesDetail").empty().append('checking...');
        $("#screenProtectorDiv").show();
        $.post("find.contact.php",postVars).done(function(data){
            var json = $.parseJSON(data);
            if (json==null) {
                saveContact();
                return;
            }
            if (json.length>0) {
                $("#duplicatesDetail").empty();
                var plural = (json.length==1) ? '':'s';
                $("#duplicatesHdr").empty().append(json.length.toString()+" Possible Duplicate"+plural+" Found");
                for (var i=0;i<json.length;i++) {
                    $("#duplicatesDetail").append('<div class="duplicateRow">'+json[i].display_name+' ('+json[i].contact_id+'/'+json[i].dnr_no+')<span class="spanEdit" dnr="'+json[i].dnr_no+'">edit</span></div>');
                }
                $(".spanEdit").click(function(){
                    window.location = 'load.contact.php?d='+$(this).attr('dnr')+'&edit=personalDetails';
                });
                /*$("#duplicatesDiv").css('margin-top',function(){
                    var mt = $(window).height() - $("#duplicatesDiv").outerHeight();
                    return mt/4;
                });*/
            } else {
                saveContact();
            }
        });
    } else {
        saveContact();
    }
}

function saveContact() {   
    $("#screenProtectorDiv,#btnSaveContact,#frmNewContact").hide();
    $("#divSaving").show();
    $.post("save.contact.php",$("#frmNewContact").serialize()).done(function(data){
        var json = $.parseJSON(data);
        var dnrNo = json.result[0].result.civicrmApiObject.external_identifier;
        window.location = 'load.contact.php?d='+dnrNo+'&edit=personalDetails';
    });
}