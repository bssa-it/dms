function selectAll(){
    var sa = document.getElementById('chkSelectAll');
    var table = document.getElementById("filteredDataTbl");
    for (var i = 1, row; row = table.rows[i]; i++) {
        row.cells.item(0).firstChild.checked=sa.checked;
    }
    var c = document.getElementById('countSummaryDiv');
    c.innerHTML = (sa.checked) ? table.rows.length-1:'0';
}

function countSelected(){
    var table = document.getElementById("filteredDataTbl");
    var cnt = 0;
    for (var i = 1, row; row = table.rows[i]; i++) {
        cnt += (row.cells.item(0).firstChild.checked) ? 1:0;
    }
    var c = document.getElementById('countSummaryDiv');
    c.innerHTML = cnt;
    return cnt;
}

function selectFromFilter(){
   
    /* GET CHECKBOXES */
    var chkboxDepartment = document.getElementsByName("chkDepartments");
    var txtDateReceived = document.getElementById("datepicker");
    var etxtDateReceived = document.getElementById("edatepicker");
    var chkboxLanguage = document.getElementsByName("chkLanguages");
    var chkboxCategory = document.getElementsByName("chkCategories");
    var chkboxDocType = document.getElementsByName("chkDocTypes");
    var chkboxMotivation = document.getElementsByName("chkMotivations");
    var chkboxRegion = document.getElementsByName("chkRegions");
    var radFirstTimers = document.getElementsByName("firstTimers");
    var radPreferences = document.getElementsByName("preference");
    
    var table = document.getElementById("filteredDataTbl");
    for (var i = 1, row; row = table.rows[i]; i++) {
        row.cells.item(0).firstChild.checked = false;
        var con = false;
        var tblFirstTrxn = row.cells.item(11).textContent;
        for (var d=0;d<radFirstTimers.length;d++) {
            if (radFirstTimers[d].checked) {
                switch (radFirstTimers[d].value) {
                    case 'includeFirstTimers':
                        break;
                    case 'excludeFirstTimers':
                        if (tblFirstTrxn=='Y') 
                        {
                            row.cells.item(0).firstChild.checked = false;
                            con = true;
                        }
                        break;
                    case 'exclusivelyFirstTimers':
                        if (tblFirstTrxn=='Y') row.cells.item(0).firstChild.checked = true;
                        con = true;
                        break;
                }
            }    
        }
        if (con) continue;
        
        var tblPreference = row.cells.item(13).textContent;
        var preferenceMatch = false;
        var checkPreference = false
        for (var p=0;p<radPreferences.length;p++) {
            if (radPreferences[p].checked) {
                var pref = radPreferences[p].value;
                if (pref.length>0) {
                    checkPreference = true;
                    if (pref == tblPreference) preferenceMatch = true;
                } 
            }
        }
        
       var tblDepartment = row.cells.item(1).textContent;
       var departmentMatch = false;
       var checkDepartment = false;
       for (var d=0;d<chkboxDepartment.length;d++) {
            if (chkboxDepartment[d].checked) {
                checkDepartment = true;
                if (tblDepartment.substr(0,1)==chkboxDepartment[d].value) {
                    departmentMatch = true;
                    break;   
                }   
            } 
       }
       
       var checkDate = (txtDateReceived.value.length>0);
       var tblDate = row.cells.item(5).textContent;
       var dateMatch = (tblDate==txtDateReceived.value);
       
       var checkeDate = (etxtDateReceived.value.length>0);
       if (checkeDate) {
            var sDate = Date.parse(txtDateReceived.value.substr(3,2)+'-'+txtDateReceived.value.substr(0,2)+'-'+txtDateReceived.value.substr(6,4));
            var eDate = Date.parse(etxtDateReceived.value.substr(3,2)+'-'+etxtDateReceived.value.substr(0,2)+'-'+etxtDateReceived.value.substr(6,4));
            var tDate = Date.parse(tblDate.substr(3,2)+'-'+tblDate.substr(0,2)+'-'+tblDate.substr(6,4));
            dateMatch = (tDate>=sDate&&tDate<=eDate);
       }
       
       var tblLanguage = row.cells.item(3).textContent;
       var languageMatch = false;
       var checkLanguage = false;
       for (var d=0;d<chkboxLanguage.length;d++) {
            if (chkboxLanguage[d].checked) {
                checkLanguage = true;
                if (tblLanguage==chkboxLanguage[d].value) {
                    languageMatch = true;
                    break;   
                }   
            } 
       }
       
       var tblCategory = row.cells.item(4).textContent;
       var categoryMatch = false;
       var checkCategory = false;
       for (var d=0;d<chkboxCategory.length;d++) {
            if (chkboxCategory[d].checked) {
                checkCategory = true;
                if (tblCategory==chkboxCategory[d].value) {
                    categoryMatch = true;
                    break;   
                }   
            } 
       }
       
       var tblDocType = row.cells.item(7).textContent;
       var docTypeMatch = false;
       var checkDocType = false;
       for (var d=0;d<chkboxDocType.length;d++) {
            if (chkboxDocType[d].checked) {
                checkDocType = true;
                if (tblDocType==chkboxDocType[d].value) {
                    docTypeMatch = true;
                    break;   
                }   
            } 
       }
       
       
       var tblMotivation = row.cells.item(14).textContent;
       var motivationMatch = false;
       var checkMotivation = false;
       for (var d=0;d<chkboxMotivation.length;d++) {
            if (chkboxMotivation[d].checked) {
                checkMotivation = true;
                if (tblMotivation==chkboxMotivation[d].value) {
                    motivationMatch = true;
                    break;   
                }    
            } 
       }
       
       var tblRegion = row.cells.item(10).textContent;;
       var regionMatch = false;
       var checkRegion = false;
       for (var d=0;d<chkboxRegion.length;d++) {
            if (chkboxRegion[d].checked) {
                checkRegion = true;
                if (tblRegion==chkboxRegion[d].value) {
                    regionMatch = true;
                    break;   
                }   
            } 
       }
       
       if (!checkDepartment&&!checkDate&&!checkLanguage&&!checkCategory&&!checkDocType&&!checkRegion&&!checkPreference&&!checkMotivation) {
            row.cells.item(0).firstChild.checked = false;
       } else {
            row.cells.item(0).firstChild.checked = (
               //alert(
                (departmentMatch||!checkDepartment)
                &&(dateMatch||!checkDate)
                &&(languageMatch||!checkLanguage)
                &&(categoryMatch||!checkCategory)
                &&(docTypeMatch||!checkDocType)
                &&(regionMatch||!checkRegion)
                &&(preferenceMatch||!checkPreference)
                &&(motivationMatch||!checkMotivation)
               );     
       }
       
    }
    countSelected();
}

function openTemplateManager() {
    document.acknowledgementDataset.action = 'letter.php';
    document.acknowledgementDataset.submit();
}

function acknowledgeContributions() {
    var isChecked = document.getElementById('chkInsertAcknowledment').checked;
    if (isChecked) {
        document.acknowledgementDataset.action = 'acknowledge.contributions.php';
        document.acknowledgementDataset.submit();   
        return;
    } else {
        alert('Please check the box to acknowledge these contributions');
    }
}

function showNextDiv() {
    
    var recordsSelectedCount = countSelected();
    if (recordsSelectedCount==0) {
        alert('Please select at least one record to export');
        return;
    }
    
    var d = document.getElementById('nextDivBackground');
    d.style.display = (d.style.display=='none') ? '':'none';
    
}

$(document).ready(function() {

    $("#btnCreate").click(function(){
        
        var fileName = $('#filename').val();
        if (fileName.length==0){
            alert('Please provide a filename for your extract');
            return;
        }
        if (fileName.substr(fileName.length-4)!='.txt') {
            alert('Your filename must end with .txt');
            return;
        }
        $("#nextDiv").hide();
        $("#progressDivBackground").show();
        $.post("update.acknowledgements.php", $( "#acknowledgementDataset" ).serialize(),function(){
            window.location.href = 'export.acknowledgements.php';
        });
    });
});