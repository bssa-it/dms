/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var openBatches;
var loadingDiv = '<div class="waitingDiv"><img src="/dms/img/loading.gif" title="loading..." align="center" style="margin-right: 15px;margin-left: 15px;" /> loading...</div>';
$(document).ready(function() {
    $.get("get.batches.php").done(function(data) {
        openBatches = $.parseJSON(data);
        if (openBatches.length===0) {
            $("#bodyDiv").empty().append('<div class="emptyReturn">No Open Batches</div>');
        } else {
            loadBatchList();
        }
    });
    $(".navItem").click(function(){
        $(".navItem").removeClass('selectedNav');
        $(this).addClass('selectedNav');
        eval($(this).attr('fn')+'()');
    });
});

function addRow(rw) {
    var batchNo = rw.id;
    while (batchNo.length<5) batchNo = '0' + batchNo;
    var total = parseFloat(rw.batch_total).toFixed(2);
    var tr = '<tr bid="'+ rw.id +'" class="batchRow">';
    tr += '<td>' + batchNo + '</td>';
    tr += '<td>' + rw.office + '</td>';
    tr += '<td>' + rw.batch_type + '</td>';
    tr += '<td align="right">R ' + total + '</td>';
    tr += '<td>' + rw.batch_status + '</td>';
    tr += '<td>' + rw.batch_title + '</td>';
    tr += '<td>' + rw.created_datetime + '</td>';
    tr += '<td>' + rw.display_name + '</td>';
    tr += '</tr>';
    return tr;
}
function loadBatchList() {
    
    $("#bodyDiv").empty().append(loadingDiv);
    $.get("/dms/get.user.details.php").done(function(data){
        var rwCnt = 0;
        var tbl = '<h3>Open Batches</h3><table id="tblOpenBatches" width="1100" cellpadding="5" cellspacing="0" class="tblDetails">';
        tbl += '<thead><tr><td>Batch No</td><td>Office</td><td>Batch Type</td><td align="right">Batch Total</td>';
        tbl += '<td>Status</td><td>Title</td><td>Created</td><td>User</td></tr></thead>';
        dmsUser = $.parseJSON(data);
        for (var i=0;i<openBatches.length;i++) {
            if (openBatches[i].office_id == dmsUser.office_id) {
                tbl += addRow(openBatches[i]);
                rwCnt++;
            }
        }
        tbl += '</table>';
        if (rwCnt>20) {
            $('#tblOpenBatches').oneSimpleTablePagination({rowsPerPage: 20,brdrColor: "#7B7922"});
        }
        $("#bodyDiv").empty().append(tbl);
        bindRowClick();
    });
    
}

function loadNewBatch() {
    
    $("#bodyDiv").empty().append(loadingDiv);
    $.get("get.batch.form.php").done(function(data){
        $("#bodyDiv").empty().append(data);
    });
}

function bindRowClick() {
    $(".batchRow").click(function(){
       window.location="load.batch.php?batch_id="+$(this).attr('bid'); 
    });
}