function getSettings(x) {
    $.get("load.widget.settings.php",{i:x}).done(function(data){
        $("#rightBox").empty().append(data);
    });
    
}
function setAllColorsToDefault() {
    $('.qtr').each(function(){
        $(this).css('color',$(this).css('background-color'));
        $(this).css('border-color',$(this).css('background-color'));
    });
    $(".qtr").bind('click',clickWidgetQtr);
    
    $('.nwWidget').removeClass('hvr');
    $(".nwWidget").unbind('click');
}

function loadWidgetSelect(){
    var wid = $(this).attr('wid');
    $("#rightBox").empty().append($("#widgetSelect").html());
    $("#rightBox").find('select[name="newWidget"]').attr('id','newWidget');
    for (var w in userWidgets) {
        if (widgetList.indexOf(userWidgets[w])>-1) $("#newWidget").find("option[value='"+userWidgets[w]+"']").remove();
    }
    $("#rightBox").find('input[name="qtrNo"]').attr('id','qtrNo');
    $("#qtrNo").val($(this).attr('qno'));
    $("#rightBox").find('div[class="btn"]').attr('id',"btnAddNew");
    $("#btnAddNew").bind('click',submitNewWidget);
}

function submitNewWidget(){
    if ($("#newWidget").val().length==0) {
        alert('Please select the widget you would like to add');
        $("#newWidget").focus();
        return;
    }
    $.post('add.widget.php',{w: $("#newWidget").val(),p: $("#qtrNo").val()}).done(function(data){
        var jsonData = $.parseJSON(data);
        var curId = $("#q"+$("#qtrNo").val()).attr('wid');
        $("div[wid='"+curId+"']").each(function(){
            $(this).attr('wid',jsonData.newWidgetId);
        });
        userWidgets = [];
        userWidgets.push(jsonData.q1);
        userWidgets.push(jsonData.q2);
        userWidgets.push(jsonData.q3);
        userWidgets.push(jsonData.q4);
        $(".qtr").bind('click',clickWidgetQtr);
        $("#q"+$("#qtrNo").val()).click();
    });
}

function clickWidgetQtr(){
    setAllColorsToDefault();
    $(this).css('borderColor','#254B7C');
    $(this).css('color','#254B7C');
    $(this).unbind('click');
    
    $(this).find('.nwWidget').addClass('hvr');
    $(this).find(".nwWidget").bind('click',loadWidgetSelect);
    
    $('#rightBox').empty().append('<img src="img/loading.gif" />');
    var wid = $(this).attr('wid');
    getSettings(wid);
}
$(document).ready(function(){
    $(".qtr").bind('click',clickWidgetQtr);
    setAllColorsToDefault();
    $.get("load.user.settings.php").done(function(data){
       $("#rightBox").empty().append(data); 
    });
});