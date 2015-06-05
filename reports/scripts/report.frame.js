$(document).ready(function(){
    $("#btnRun").click(function(){
        var bName = baseName($("#reportId").val());
        $("#promptContainer").find("select,input").each(function(){
            var id = $(this).attr('id');
            var nid = id.replace('ip',bName);
            var v = $(this).val();
            $("#"+nid).val(v);
            xhr.abort();
            getFn($(".selectedNav").attr('fn'));
        });
    });
    $("#btnBack").click(function(){
       $("#reportHeader, #reportListDiv").show();
       $("#report").hide();
    });
    $("#btnPrompts").click(function(){
        $("#report").hide();
        $("#prompts").show();
    });
    $("#leftNavigationDiv").find("li").click(function(){
        xhr.abort();
        getFn($(this).attr('fn'));
        $("#leftNavigationDiv").find("li").removeClass('selectedNav');
        $(this).addClass('selectedNav');
    });
    
    var fn = $(".selectedNav").attr('fn');
    getFn(fn);
});
var xhr;
function getFn(fn) {
    $("#reportContent").empty().append('<img src="../img/loading.gif" align="center" /> loading...');
    var frmName = 'frm' + ucfirst(baseName($("#reportId").val()));
    xhr = $.post(fn,$('#'+ frmName).serialize()).done(function(data){
        $("#reportContent").empty().append(data);
    });
}