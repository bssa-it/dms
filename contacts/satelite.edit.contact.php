<?php

/**
 * @description
 * This script loads the form for editing/creating a new contact.
 * 
 * @author      Chezre Fredericks
 * @date_created 17/06/2015
 * @Changes
 * 
 */

#   BOOTSTRAP
include("../inc/globals.php");

$curScript = basename(__FILE__, '.php');
$pageHeading = $title = 'Create Contact';


$contactCfg = simplexml_load_file("inc/config.xml");
$contactDefaults = $contactCfg->defaults;

#   SANITIZE POSTED VARIABLES
if(!empty($_POST)) $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
if(!empty($_GET)) $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);


if (!empty($_GET['contact_id'])||!empty($_POST['contact_id'])) {
    $pageHeading = $title = 'Edit Contact';
}

$contactTypeOpts = '';
$contactTypes = $GLOBALS['functions']->getCiviContactTypes();
if (!empty($contactTypes)) {
    foreach ($contactTypes as $t) $contactTypeOpts .= '<option value="'.$t['label'].'">'.$t['label'].'</option>';
}

$titleOptGroupId = (string)$GLOBALS['xmlConfig']->civiOptionGroups->titles;
$titleOptsValues = $GLOBALS['functions']->getCiviOptionValues($titleOptGroupId);
$titleOpts = '';
if (!empty($titleOptsValues)) {
    foreach ($titleOptsValues as $t) {
        $titleOpts .= '<option value="'.$t['value'].'">'.$t['label'].'</option>';
    }
}

$departmentOpts = $denominationOpts = '';
$myDepartment = (empty($_SESSION['dms_user']['config']['impersonate'])) ? '0':$_SESSION['dms_user']['config']['impersonate'];
$department = new civicrm_dms_department_extension($myDepartment);
$depColor = (empty($department->dep_chart_color)) ? '#254B7C' : $department->dep_chart_color;

$allDepartments = $GLOBALS['functions']->GetDepartmentList();
$departmentOpts = '';
if (!empty($allDepartments)) {
    foreach ($allDepartments as $d) {
        $selected = ($myDepartment==$d['dep_id']) ? ' SELECTED':'';
        $departmentOpts .= '<option value="' . $d['dep_id'] . '"'.$selected.'>' . $d['dep_id'] .' - '.$d['dep_name'].'</option>';   
    }
}
$denominations = $department->getDistinctDenominations();
$denominationOpts = $organisationName = '';
$denomination = '00';
if (!empty($denominations)) {
    foreach ($denominations as $dn) {
        $selected = ($dn['den_id']==$denomination) ? ' SELECTED':'';
        $denominationOpts .= "\n" .'<option value="'.$dn['den_id'].'"'.$selected.'>'.$dn['den_id'].' - '.$dn['den_name'].'</option>';
    }
}

$organisation = new civicrm_dms_organisation_extension($myDepartment . '0000000');
$organisationName = (empty($organisation->org_name)) ? 'Unknown' : $organisation->org_name . ' (' .$organisation->org_region . ')';

$categories = $GLOBALS['functions']->GetCategoryList();
$categoryOpts = '';
foreach ($categories as $c) {
    $selected = ((int)$contactDefaults->reporting_code->category_id==(int)$c['cat_id']) ? ' SELECTED':'';
    $categoryOpts .= '<option value="'.$c['cat_id'].'"'.$selected.'>'.str_pad($c['cat_id'],4,'0',STR_PAD_LEFT).' - '.$c['cat_name'].'</option>';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="/dms/css/global.css" />
        
        <script type="text/javascript" language="javascript" src="/dms/scripts/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" language="javascript" src="/dms/scripts/jquery-ui-1.10.4.custom.js"></script>
        <script type="text/javascript" language="javascript" src="/dms/scripts/global.js"></script>
        <script type="text/javascript" language="javascript">
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
                    $("#regionList, #lblCn").hide();
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
                        $("#lblCn").show();
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
                
                $("#btnCancel").click(function(){
                   window.close(); 
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
    $("#divSaving").show();
    $.post("save.contact.php",$("#frmNewContact").serialize()).done(function(data){
        var json = $.parseJSON(data);
        var contactId = json.result[0].result.civicrmApiObject.contact_id;
        var civiContact = json.result[0].result.civicrmApiObject;
        $("#divSaving").empty().append('Contact Created: '+contactId);
        alert('Contact Created: '+contactId);
        if ($("#isContribution").val()=='Y') {
            insertContributionAllocation(civiContact);
        } else {
            window.close();
        }
    });
}

function insertContributionAllocation(contact) {
    
    var allocAmount = window.opener.document.getElementById("alloc_total_amount").value;
    var receiptId = window.opener.receiptId;
    var motivationCode = window.opener.document.getElementById("alloc_motivation_id").value;
    var motivationText = $(window.opener.document.getElementById("alloc_motivation_id")).find('option:selected').text();
    var receiptDate = window.opener.document.getElementById("received_datetime").value;
    allocRecord = {
        id: '',
        receipt_id: receiptId,
        received_datetime: receiptDate,
        received_by: '',
        receipt_no: '',
        receipt_amount: allocAmount,
        receipt_type: '',
        line_no: '',
        contact_id: contact.contact_id,
        motivation_id: motivationCode,
        entry_id: '',
        dnr_no: contact.external_identifier,
        display_name: contact.display_name,
        motivation: motivationText
    };
    window.opener.allocations.allocations.push(allocRecord);
    var newRow = window.opener.createTblRow(allocRecord);
    $(window.opener.document).find("#tbodyAllocations").append(newRow);
    
       
    window.opener.bindDeleteToRows();
    window.opener.calculateUnallocated();          
    window.opener.clearAllocationFields();
    
    window.close();
}
        </script>
        
        
        <style type="text/css">
            #headingRow {
                font-size: 28pt;
                font-weight: bold;
                min-width: 480px;
                width: auto !important;
                min-height: 45px;
                height: auto !important;
                padding-left: 10px;
            }
            #contentDiv {
                padding: 10px;
                height: auto;
                width: auto;
                overflow: hidden;
            }
            .frmWrapperDiv {
                width: auto;
                height: auto;
                max-height: 450px;
                overflow: hidden;
                padding-bottom: 5px;
            }
            .hdg {
                font-size: 16pt;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .inputWrapperDiv {
                padding: 5px;
                padding-top: 0px;
                width: 620px;
                height: auto;
                overflow: hidden;
            }
            .inputDiv {
                float: left;
                width: 350px;

            }
            .labelDiv {
                float: left;
                width: 250px;
            }
            .inputDiv select,.inputDiv input,#cn {
                border: 3px solid #254B7C;
                padding: 2px;
            }
            #btnSaveContact {
                clear: left;
            }
            #departmentDiv {
                overflow:hidden;
                min-width: 400px;
                width: auto;
                float: left;
                height: auto;
                margin-bottom: 10px;
            }
            #depSelectDiv {
                min-height: 100px;
                height: auto !important;
                overflow: hidden;
                min-width: 475px;
                float: left;
            }
            #newDep,#currDep {
                margin-right: 10px;
                padding-top: 5px;
                font-size: 35px;
                float: right;
            }
            #orgIdPrefix {
                font-size: 16pt;
                font-weight: bold;
                padding:  3px;
            }
            #cn {
                width:75px;
            }
            #cnInputDiv {
                float: left;
                clear: left;
                width: auto;
                height: auto;
                overflow: hidden;

            }
            #lblCn {
                float:left;
                width: auto;
                height: auto;
                overflow: hidden;
                padding: 5px;
                padding-left: 10px;
                padding-right: 10px;
                border: 2px solid rgba(37,75,124,.2);
                background-color: rgba(37,75,124,.2);
                    margin-left: 5px;
            }
            #regionList {
                float:right;
                width: 200px;
                height: auto;
                max-height: 145px;
                padding: 5px;
                background-color: #F0F0F0;
                font-size: 10pt;
                border: 3px solid #254B7C;
                overflow: auto;
            }
            #categoryDiv {
                width: 540px;
            }
            #btnCancel {
                float:right;
            }
            #categoryInputDiv {
                width: 470px;
            }
            #category_id {
                width: 460px!important;
            }
            .aInsert {
                font-weight: bold;
                color: #254B7C;
                cursor:pointer;
            }
            .insertLink {
                overflow: hidden;
                width: 190px;
                padding: 5px;
            }
            .insertLink:hover {
                background-color: rgba(37,75,124,0.3);
            }
            #divSaving {
                float: left;
                padding: 5px;
                width: auto;
                height: auto;
                overflow: hidden;
            }
            #headingRow {
                background-color: #254B7C !important;
                border: none !important;
                color: #FFF !important;
            }
        </style>
    </head>
    <body>
        <div id="posDiv"></div>
        <div id="headingRow">
            <?php echo $pageHeading; ?>
            <div class="circle80" id="newDep" style="display:none">?</div>
            <div class="circle80" id="currDep" style="background-color: <?php echo $depColor; ?>;border-color: <?php echo $depColor; ?>;"><?php echo $department->dep_id; ?></div>
        </div>
        <div id="contentDiv">
            <?php if (!empty($_GET['qck'])||!empty($_POST['qck'])) { 
                
                $fName = (!empty($_GET['first_name'])) ? $_GET['first_name']:'';
                if (!empty($_POST['first_name'])) $fName = $_POST['first_name'];
                
                $lName = (!empty($_GET['last_name'])) ? $_GET['last_name']:'';
                if (!empty($_POST['last_name'])) $fName = $_POST['last_name'];
                
                $fullname = $fName . ' ' . $lName;
                
            ?>
            <form id="frmNewContact" name="frmNewContact" action="save.contact.php" method="POST">
                <input type="hidden" id="isContribution" value="Y" />
                <div class="frmWrapperDiv" >
                    <div class="hdg" frm="personalDetailsDiv">Personal Details</div>
                    <div class="frmDiv" id="personalDetailsDiv">
                        
                        <div class="inputWrapperDiv">
                            <input type="hidden" name="contact_type" id="contact_type" value="Individual" />
                            <div class="labelDiv"><label for="contact_sub_type">Contact Type</label></div>
                            <div class="inputDiv">
                                <select name="contact_sub_type" id="contact_sub_type">
                                    <?php echo $contactTypeOpts; ?>
                                </select>
                            </div>
                        </div>

                        <div class="inputWrapperDiv Individual">
                            <div class="labelDiv"><label for="first_name">First Name</label></div>
                            <div class="inputDiv"><input type="text" name="first_name" id="first_name" placeholder="" value="<?php echo $fName; ?>" /></div>
                        </div>

                        <div class="inputWrapperDiv Individual">
                            <div class="labelDiv"><label for="last_name">Last Name</label></div>
                            <div class="inputDiv"><input type="text" name="last_name" id="last_name" placeholder="" value="<?php echo $lName; ?>" /></div>
                        </div>
                        
                        <div class="inputWrapperDiv Household" style="display:none">
                            <div class="labelDiv"><label for="household_name">Family Name</label></div>
                            <div class="inputDiv"><input type="text" name="household_name" id="household_name" placeholder="" value="<?php echo $fullname; ?>" /></div>
                        </div>
                        
                        <div class="inputWrapperDiv Organization" style="display:none">
                            <div class="labelDiv"><label for="organization_name">Organisation Name</label></div>
                            <div class="inputDiv"><input type="text" name="organization_name" id="organization_name" placeholder="" value="<?php echo $fullname; ?>" /></div>
                        </div>
                    </div>
                </div>
                <div class="frmWrapperDiv" >
                    <div class="hdg" frm="reportDetailsDiv">Department</div>
                    <div class="frmDiv" id="reportDetailsDiv">
                            <input type="hidden" id="organisation_id" name="organisation_id" value="<?php echo $myDepartment; ?>0000000" />
                            <div id="departmentDiv">
                                <div id="depSelectDiv">
                                    <div class="inputWrapperDiv">
                                        <div class="labelDiv"><label for="dp"></label></div>
                                        <div class="inputDiv">
                                            <select id="dp">
                                                <?php echo $departmentOpts; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="inputWrapperDiv">
                                        <div class="labelDiv"><label for="dn"></label></div>
                                        <div class="inputDiv">
                                            <select id="dn">
                                                <?php echo $denominationOpts; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div id="cnInputDiv">
                                       <span id="orgIdPrefix"><?php echo $myDepartment; ?>00</span>
                                       <input type="text" id="cn" maxlength="5" value="00000" placeholder="" />
                                    </div>
                                    <div id="lblCn"><label for="cn"><?php echo $organisationName; ?></label></div>    
                                    
                                </div>
                                
                            </div>
                            
                            <div id='regionList' style='display: none'>&nbsp;</div>
                            
                            <div class="inputWrapperDiv" id="categoryDiv">
                                <div class="hdg" frm="reportDetailsDiv"><label for="category_id">Category</label></div>
                                <div class="inputDiv" id="categoryInputDiv">
                                    <select id="category_id" name="category_id">
                                        <option value="">-- select --</option>
                                        <?php echo $categoryOpts; ?>
                                    </select>
                                </div>
                            </div>

                    </div>
                </div>
                </form>
                <div class="btn" id="btnSaveContact">Create</div>
                <div class="btn" id="btnCancel">Cancel</div>
                <div id="divSaving" style="display:none">Creating new contact...</div>
            <?php } ?>
        </div>
    </body>
</html>