function downloadFile(fname) {
    //window.location='acklists/'+fname;
    window.open('download.txtfile.php?f=acklists/'+fname,'_blank');
    //$.post('download.txtfile.php',{f: 'acklists/'+fname})
}
function searchForFilename(strSearch) {
    var table = document.getElementById("searchTbl");
    for (var i = 0, row; row = table.rows[i]; i++) row.style.display = '';
    for (var i = 1, row; row = table.rows[i]; i++) {
       var strFound = row.cells.item(0).textContent.indexOf(strSearch);
       row.style.display = (strFound>-1) ? '':'none';
    }
}