$(document).ready(function() {
    $("#tblData tr").click(function(){
        $('#dep_id').val($(this).find("td:nth-child(1)").text());
        $("#departmentId").empty().append($(this).find("td:nth-child(1)").text());
        $('#dep_name').val($(this).find("td:nth-child(2)").text());
        $('#dep_defaultRegion').val($(this).find("td:nth-child(3)").text());
        $('#dep_isNational').val($(this).find("td:nth-child(4)").text());
        $('#dep_budgetAllocation').val($(this).find("td:nth-child(5)").text());
        $('#dep_chartColor').val($(this).find("td:nth-child(6)").text());
        $("#departmentId").css("background-color",$(this).find("td:nth-child(6)").text());
        $('#dep_fromEmailName').val($(this).find("td:nth-child(7)").text());
        $('#dep_fromEmailAddress').val($(this).find("td:nth-child(8)").text());
        $('#dep_contact_id').val($(this).find("td:nth-child(9)").text());
        $("#screenProtectorDiv").show();
    });
    $("#btnClose").click(function(){
        $("#screenProtectorDiv").hide();
    });
    $("#btnEdit").click(function(){
        var a = document.getElementById('dep_id');
        var b = document.getElementById('dep_name');
        var c = document.getElementById('dep_defaultRegion');
        var d = document.getElementById('dep_isNational');
        var e = document.getElementById('dep_budgetAllocation');

        if (a.value.length==0) {
            alert('Please select a department');
            a.focus();
            return;
        }

        if (b.value.length==0) {
            alert('Please enter the Organising Secretary\'s name');
            b.focus();
            return;
        }
        
        var budAmount = e.value.replace('R','');
        while (budAmount.indexOf(' ')>-1) budAmount = budAmount.replace(' ','');
        if (isNaN(budAmount)) {
            alert("Please enter the allocated budget");
            e.focus();
            return false;
        }
        
        e.value = budAmount;
        document.frmSave.submit(); 
    });
});