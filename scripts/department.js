$(document).ready(function() {
    $("#tblData tr").click(function(){
        $("#did").val($(this).attr('did'));
        $('#dep_id').val($(this).find("td:nth-child(1)").text());
        $("#departmentId").empty().append($(this).find("td:nth-child(1)").text());
        $('#dep_name').val($(this).find("td:nth-child(2)").text());
        $('#dep_office_id').val($(this).find("td:nth-child(3)").text());
        $('#dep_is_national').val($(this).find("td:nth-child(4)").text());
        $('#dep_budget_allocation').val($(this).find("td:nth-child(5)").text());
        $('#dep_chart_color').val($(this).find("td:nth-child(6)").text());
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
        var c = document.getElementById('dep_office_id');
        var d = document.getElementById('dep_is_national');
        var e = document.getElementById('dep_budget_allocation');

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