/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var loadingDiv = '<div class="waitingDiv"><img src="/dms/img/loading.gif" title="loading..." align="center" style="margin-right: 15px;margin-left: 15px;" /> loading...</div>';
$(document).ready(function() {
    loadEntryList();
    
    $(".navItem").click(function(){
        $(".navItem").removeClass('selectedNav');
        $(this).addClass('selectedNav');
        eval($(this).attr('fn')+'()');
    });
});

function loadEntryList() {
    
    $("#bodyDiv").empty().append(loadingDiv);
    $.get("get.receipt.entries.php",{receipt_id: $("#receipt_id").val()}).done(function(data) {
        $("#bodyDiv").empty().append(data);
    });
    
}

function loadNewEntry() {

    $("#bodyDiv").empty().append(loadingDiv);
    $.get("get.entry.form.php",{receipt_id: $("#receipt_id").val()}).done(function(data){
        $("#bodyDiv").empty().append(data);
    });
}