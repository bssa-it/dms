$(document).ready(function() {
    $("#primaryDepartment").change(function(){
        $("#currentEmailDpt").val($(this).val());
    });
    
    $("select[name='widgets[]']").change(function(){
        usedWidgetList = [];
        for (var i=1;i<=4;i++) if ($("#q"+i).val().length>0) usedWidgetList.push($("#q"+i).val());
        updateWidgetSelects();
    });
    $("#btnNext1").click(function(){
        if ($("#email").val().length==0) {
            alert("Please insert the email address");
            $("#email").focus();
            return;
        }
        if ($("#first_name").val().length==0) {
            alert("Please insert the First Name");
            $("#first_name").focus();
            return;
        }
        if ($("#last_name").val().length==0) {
            alert("Please insert the Last Name");
            $("#last_name").focus();
            return;
        }
        if ($("#userType").val().length==0) {
            alert("Please select the user type");
            $("#userType").focus();
            return;
        }
        $(".pg").hide();
        $("#page-2").show();
    });
    $("#btnNext2").click(function(){
        if (countChks()==0) {
            alert('Please select at least 1 department');
            return;
        }
        $(".pg").hide();
        $("#page-3").show();
    });
    $("#btnBack2").click(function(){
        $(".pg").hide();
        $("#page-1").show();
    });
    $("#btnBack3").click(function(){
        $(".pg").hide();
        $("#page-2").show();
    });
    $("#btnSave").click(function(){
        var widgetCnt = 0;
        for (var i=1;i<=4;i++) widgetCnt += $("#q"+i).val().length;
        if (widgetCnt==0) {
            alert('Please select at least 1 widget');
            return;
        }
        $("#frmUser").submit();
    });
    $("#q1").val('searchBuilder').trigger('change');
    $("#q2").val('acknowledgement').trigger('change');
});

function updateWidgetSelects() {
    $("select[name='widgets[]']").each(function(){
        for (var w in usedWidgetList) {
            if ($(this).val()!=usedWidgetList[w]) $(this).find("option[value='"+usedWidgetList[w]+"']").remove();
        }
    });
}


function addOption(sid,val,txt) {
    var option = document.createElement("option");
    option.text = txt;
    option.value = val;
    $("#"+sid).add(option);
}

function checkAllDepts() {
    var sa = document.getElementById('chkAll');
    var chks = document.getElementsByName('departments[]');
    for (c=0;c<chks.length;c++) {
        chks[c].checked = sa.checked;
    }
    addDepartmentToImpersonate();
}
function countChks() {
    var chks = document.getElementsByName('departments[]');
    var total = 0;
    for (c=0;c<chks.length;c++) {
        if (chks[c].checked) total++;
    }
    return total;
}
function addDepartmentToImpersonate() {
    var x = document.getElementById("primaryDepartment");
    var curDpt = document.getElementById('currentEmailDpt');
    var xLen = x.length;
    for (var i=xLen; i>-1; i--) x.remove(i); 
    var chks = document.getElementsByName('departments[]');
    for (c=0;c<chks.length;c++) {
        if (chks[c].checked) {
            var option = document.createElement("option");
            option.text = chks[c].alt;
            option.value = chks[c].value;
            x.add(option); 
        }
    }
    if (x.length==0) curDpt.value = '';
    x.value = curDpt.value;
    if (x.length>0&&x.value.length==0) {
        x.value = x[0].value;
        curDpt.value = x.value;   
    }
}