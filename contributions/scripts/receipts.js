/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var openReceipts;
var loadingDiv = '<div class="waitingDiv"><img src="/dms/img/loading.gif" title="loading..." align="center" style="margin-right: 15px;margin-left: 15px;" /> loading...</div>';
$(document).ready(function() {
    $.get("get.receipts.php").done(function(data) {
        openReceipts = $.parseJSON(data);
        if (openReceipts.length===0) {
            $("#bodyDiv").empty().append('<div class="emptyReturn">No Open Receipts</div>');
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
    var receiptNo = rw.id;
    while (receiptNo.length<5) receiptNo = '0' + receiptNo;
    var total = parseFloat(rw.receipt_total).toFixed(2);
    var tr = '<tr bid="'+ rw.id +'" class="receiptRow">';
    tr += '<td>' + receiptNo + '</td>';
    tr += '<td>' + rw.office + '</td>';
    tr += '<td>' + rw.receipt_type + '</td>';
    tr += '<td align="right">R ' + total + '</td>';
    tr += '<td>' + rw.receipt_status + '</td>';
    tr += '<td>' + rw.receipt_title + '</td>';
    tr += '<td>' + rw.created_datetime + '</td>';
    tr += '<td>' + rw.display_name + '</td>';
    tr += '</tr>';
    return tr;
}
function loadBatchList() {
    
    $("#bodyDiv").empty().append(loadingDiv);
    $.get("/dms/get.user.details.php").done(function(data){
        var rwCnt = 0;
        var tbl = '<h3>Open Receipts</h3><table id="tblOpenReceipts" width="1100" cellpadding="5" cellspacing="0" class="tblDetails">';
        tbl += '<thead><tr><td>Receipt No</td><td>Office</td><td>Receipt Type</td><td align="right">Receipt Total</td>';
        tbl += '<td>Status</td><td>Title</td><td>Created</td><td>User</td></tr></thead>';
        dmsUser = $.parseJSON(data);
        for (var i=0;i<openReceipts.length;i++) {
            if (openReceipts[i].office_id == dmsUser.office_id) {
                tbl += addRow(openReceipts[i]);
                rwCnt++;
            }
        }
        tbl += '</table>';
        if (rwCnt>20) {
            $('#tblOpenReceipts').oneSimpleTablePagination({rowsPerPage: 20,brdrColor: "#7B7922"});
        }
        $("#bodyDiv").empty().append(tbl);
        bindRowClick();
    });
    
}

function loadNewBatch() {
    
    $("#bodyDiv").empty().append(loadingDiv);
    $.get("get.receipt.form.php").done(function(data){
        $("#bodyDiv").empty().append(data);
    });
}

function bindRowClick() {
    $(".receiptRow").click(function(){
       window.location="load.receipt.php?receipt_id="+$(this).attr('bid'); 
    });
}