/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $("select[name='widgets[]']").change(function(){
        usedWidgetList = [];
        for (var i=1;i<=4;i++) if ($("#q"+i).val().length>0) usedWidgetList.push($("#q"+i).val());
        updateWidgetSelects();
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
    $("tr").click(function(){
        var uid = $(this).attr('uid');
        if (typeof uid !== typeof undefined && uid !== false) {
            $("select[name='widgets[]']").each(function(){
                $(this).empty();
                $(this).append("<option value=''>-- select --</option>");
                for (var i in widgetList) {
                    $(this).append('<option value="'+widgetList[i]+'">'+widgetList[i]+"</option>");
                }
            });
            $("#usr_id").val(uid);
            $("#userNameDiv").empty().append($(this).find('td:nth-child(3)').text());
            $("#userType").val($(this).find('td:nth-child(1)').text());
            $("#officeId").val($(this).find('td:nth-child(6)').text());
            var u = [];
            for (var i in users) if (users[i].u==uid) u = users[i];
            if (u.q1.toString().length>1) $("#q1").val(u.q1).trigger('change');
            if (u.q2.toString().length>1) $("#q2").val(u.q2).trigger('change');
            if (u.q3.toString().length>1) $("#q3").val(u.q3).trigger('change');
            if (u.q4.toString().length>1) $("#q4").val(u.q4).trigger('change');
            
            var bxs = ["view","save"];
            for (var key in u.permissions) {
                if (u.permissions.hasOwnProperty(key)) for (var b in bxs) $("input[name='"+key+"_"+bxs[b]+"']").prop('checked',u.permissions[key][bxs[b]]);
            }
        }
    });
});

function updateWidgetSelects() {
    $("select[name='widgets[]']").each(function(){
        for (var w in usedWidgetList) {
            if ($(this).val()!=usedWidgetList[w]) $(this).find("option[value='"+usedWidgetList[w]+"']").remove();
        }
    });
}