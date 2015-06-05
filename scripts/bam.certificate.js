/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $("#btnNext").click(function(){
        var la = $("#lang").val();
        var add = (la=='mix') ? 2:4;
        var li = parseInt($("#currentLimit").val()) + add;
        window.location = 'bam.certificate.php?lang='+la+'&limit='+li;
    }); 
    $("#lang").change(function() {
        var la = $("#lang").val();
        window.location = 'bam.certificate.php?lang='+la+'&limit=0';
    });
    if (parseInt($("#currentLimit").val())>0) {
        $("#btnPrev").show();
    }
    $("#btnPrev").click(function(){
        var la = $("#lang").val();
        var add = (la=='mix') ? 2:4;
        var li = parseInt($("#currentLimit").val()) - add;
        if (li<0) li = 0;
        window.location = 'bam.certificate.php?lang='+la+'&limit='+li;
    }); 
    $("#btnContinue").click(function(){
        $("#screenProtectorDiv").show();
        $.when(saveCert(1),saveCert(2),saveCert(3),saveCert(4)).done(function(){
            $("#progressDiv").empty();
            for (var i=1;i<=4;i++) setWaiting(i);
            $.when(checkCert(1),checkCert(2),checkCert(3),checkCert(4)).done(function(){
                $("#progressDiv").append('<div><div id="btnPrint" class="btn">Print</div></div>');
                $("#btnPrint").click(function(){
                    $("#screenProtectorDiv").hide();
                    window.print();
                    var la = $("#lang").val();
                    window.location = 'bam.certificate.php?lang='+la+'&limit=0';
                });
            });
        });
    });
    $(".nameDiv").click(function(){
        if ($(this).has("input").length>0) return;
        var t = $(this).text();
        $(this).empty().append('<input type="text" class="editName" value="'+t+'" orig="'+t+'" />');
        $(".editName").keyup(function(e){
            if(e.keyCode == 27){
                var o = $(this).attr('orig');
                $(this).parent().empty().append(o);
            }
            if(e.keyCode == 13){
                var v = $(this).val();
                $(this).parent().empty().append(v);
            }
        });
    });
    
});

function setWaiting(certId) {
    if ($("#name-"+certId).length) {
        var m = $("#name-"+certId).attr('mid');
        $("#progressDiv").append('<div id="m'+m+'" class="wtg">Waiting...</div>');
    } else {
        $("#progressDiv").append('<div class="wtg">Empty...</div>');
    }
}

function saveCert(certId) {
    if ($("#name-" + certId).length) {
        var n = $("#name-" + certId).text();
        var m = $("#name-" + certId).attr('mid');
        var l = $("#name-" + certId).attr('lan');
        var r = $("#ref-" + certId).text();
        return $.post('save.bamCertificate.php',{lang: l,name: n,ref: r,mid: m});
    }
    return 'empty';
}
function checkCert(certId) {
    if ($("#name-" + certId).length) {
        var m = $("#name-" + certId).attr('mid');
        $.ajax({
            url:'bam/'+ m +'-bam-certificate.pdf',
            type:'HEAD',
            error: function()
            {
                $("#m"+m).empty().append(m+'-bam-certificate.pdf.... failed <span class="spnRetry" certid="'+certId+'">retry</span>');
                $(".spnRetry").click(function(){
                    var c = $(this).attr('certid');
                    $.when(saveCert(c)).done(function(){
                        var m = $("#name-"+c).attr('mid');
                        $("#m"+m).empty().append('Waiting...');
                    });
                });
            },
            success: function()
            {
                $("#m"+m).empty().append(m+'-bam-certificate.pdf.... successfully created');
            }
        });
    }
}