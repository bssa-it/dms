function validate() {
    
    var dnrNo = document.getElementById('srch_donorNumber');
    var dnrDeleted = document.getElementById('srch_donorDeleted');
    var civId = document.getElementById('srch_civiId'); 
    var dnrName = document.getElementById('srch_donorName');
    var dnrFirstName = document.getElementById('srch_firstName');
    var dnrLastName = document.getElementById('srch_lastName');
    var dnrTitle = document.getElementById('srch_title');
    var dnrContactNo = document.getElementById('srch_contactNo');
    var dnrEmail = document.getElementById('srch_email');
    var dnrAddress1 = document.getElementById('srch_AddressLine1');
    var dnrAddress2 = document.getElementById('srch_AddressLine2');
    var dnrAddress3 = document.getElementById('srch_AddressLine3');
    var dnrCity = document.getElementById('srch_City');
    var dnrPostalCode = document.getElementById('srch_PostalCode');
    var dnrRegion = document.getElementById('srch_region');
    var dnrCellNo = document.getElementById('srch_cellNo');
    var dnrTelNo = document.getElementById('srch_telNo');
    var dnrFaxNo = document.getElementById('srch_faxNo');
    var dnrRemarks = document.getElementById('srch_remarks');
    var bamRefNo = document.getElementById('srch_bamRefNo');
    
    var allFieldsLength = 0;
    if (dnrNo!=null) allFieldsLength += dnrNo.value.length;
    if (civId!=null) allFieldsLength += civId.value.length;
    if (dnrDeleted!=null) allFieldsLength += dnrDeleted.value.length;
    if (dnrFirstName!=null) allFieldsLength += dnrFirstName.value.length;
    if (dnrLastName!=null) allFieldsLength += dnrLastName.value.length;
    if (dnrTitle!=null) allFieldsLength += dnrTitle.value.length;
    if (dnrContactNo!=null) allFieldsLength += dnrContactNo.value.length;
    if (dnrEmail!=null) allFieldsLength += dnrEmail.value.length;
    if (dnrAddress1!=null) allFieldsLength += dnrAddress1.value.length;
    if (dnrAddress2!=null) allFieldsLength += dnrAddress2.value.length;
    if (dnrAddress3!=null) allFieldsLength += dnrAddress3.value.length;
    if (dnrCity!=null) allFieldsLength += dnrCity.value.length;
    if (dnrPostalCode!=null) allFieldsLength += dnrPostalCode.value.length;
    if (dnrRegion!=null) allFieldsLength += dnrRegion.value.length;
    if (dnrCellNo!=null) allFieldsLength += dnrCellNo.value.length;
    if (dnrTelNo!=null) allFieldsLength += dnrTelNo.value.length;
    if (dnrFaxNo!=null) allFieldsLength += dnrFaxNo.value.length;
    if (dnrRemarks!=null) allFieldsLength += dnrRemarks.value.length;
    if (bamRefNo!=null) allFieldsLength += bamRefNo.value.length;
    
    if (allFieldsLength==0) {
        alert('Please insert your search criteria');
        dnrNo.focus();
        return;
    }
    
    $("#searchResultsDiv").empty().append('<img src="../img/loading.gif" /> loading ....');
    $.post("find.contact.php",$("#frmSearch").serialize()).done(function(data){
        $("#searchResultsDiv").empty().append(data);
    }).fail(function(data){
        alert(data);
    });
}
function enterDonorNumber(e){
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code == 13) { 
        validate();
    }
}
function checkBamOnly() {
    var bamRefNoLength = document.getElementById('srch_bamRefNo').value.trim().length;
    document.getElementById('srch_bamOnly').checked = (bamRefNoLength>0);
    document.getElementById('srch_bamOnly').disabled = (bamRefNoLength>0);
}

$(document).ready(function(){
    $(".searchGroupingHeadingDiv").click(function(){
       $(this).parent().find(".tblFields").toggle();
       $(this).find('.expandIndicator').each(function(){
           var current = $(this).attr('src');
           if (current.indexOf('contracted')>-1) {
               $(this).attr('src','../img/expanded.png');
           } else {
               $(this).attr('src','../img/contracted.png');
           }
       });
    });
    $("#btnSearch").click(function(){
        validate(); 
    });

});

