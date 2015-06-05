$(document).ready(function() {
    $(".report").click(function() {
       $("#pageHeadingDiv").empty().append(ucfirst($(this).text() + ' report'));
       $("#report").empty().append('<img src="/dms/img/loading.gif" align="center" /> loading...');
       $("#reportHeader, #reportListDiv").hide();
       var f = $(this).attr('fn');
       $("input[name='filename']").val(f);
       var p = $(this).attr('frm');
       var b = $(this).attr('validate');
       var frmId = $(this).attr('frmid');
       if (p.length>0) {
           $("#prompts").show();
           $("#"+p).show();    
           $("#btnSubmit").click(function(){
               eval(b+'()');
           });
       } else {
           eval(b+'()');
       }
    });
});

function submitForm(frmId) {
    $.post('create.report.php', $("#"+frmId).serialize()).done(function(data){
        $("#report").show();
        $("#prompts").hide();
        $("#report").empty().append(data);
    });
}

function ucfirst(str) {
  str += '';
  var tkns = str.split(' ');
  var returnStr = '';
  for (var i=0;i<tkns.length;i++) {
      returnStr += tkns[i].charAt(0).toUpperCase() + tkns[i].substr(1) + ' ';
  }
  return returnStr.trim();
}

function baseName(str)
{
    var base = str.substr(str.lastIndexOf('/') + 1);
    if(base.lastIndexOf(".") != -1)       
        base = base.substring(0, base.lastIndexOf("."));
    return base;
}