/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    getGroups();
});

function getGroups() {
    $("#groupsDiv").empty().append('<img src="img/loading.gif" align="center" class="margin-right: 10px;" /> loading ...');
    $.get('get.group.totals.php').done(function(data) {
        $("#groupsDiv").empty().append(data);
        $('.groupSummaryDiv').click(function(){
            var gid = $(this).attr('gid');
            window.location = 'contacts/find.contact.php?group='+gid;
        });
    }); 
}