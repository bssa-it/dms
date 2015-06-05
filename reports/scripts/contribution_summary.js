$(document).ready(function(){
    
});
function bindCategoryChange() {
    $("#ip_category_id").change(function(){
        setLevel($(this).val());
        
        if ($(this).val()=='SUM') {
            $("#legacyInput").show();
        } else {
            $("#legacyInput").hide();
        }
        
        if ($(this).val()=='8001') {
            $("#ip_include_legacy").val('O');
        } else {
            $("#ip_include_legacy").val('Y');
        }
    });
}

function bindBreadcrumbsClick() {
    $("#reportBreadCrumbs").find("li").click(function(){
        $("#ip_category_id").val($(this).attr('val'));
        setLevel($(this).attr('val'));
        $("#btnRun").click();
    });
}

function bindLegacyChange() {
    $("#ip_include_legacy").change(function(){
        if ($(this).val()=='O') $("#ip_category_id").val('8001');
    });
}

function setLevel(strVal){
    var pattern = /[0-9]000/g;
    var lvl = 0;
    lvl = (strVal.match(pattern)||strVal=='0') ? 1:2;
    if (isNaN(strVal)) lvl = 0;
    $("#ip_level_id").val(lvl);
}