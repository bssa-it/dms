var http = false;

if(navigator.appName == "Microsoft Internet Explorer") {
  http = new ActiveXObject("Microsoft.XMLHTTP");
} else {
  http = new XMLHttpRequest();
}

function initializeMe(x,s) {

    var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    var r = document.getElementById('bodyDiv');
	
    var strvar = '?d='+x+'&s='+s+'&datetim=' + tim;
	http.open("GET", "load.contact.summary.php" + strvar);
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
        var s=r.getElementsByTagName('script');
        for(var i=0;i<s.length;++i)window.eval(s[i].innerHTML);
	  }
	}
	http.send(null);

}

function setFocusOnMe(el,x,s) {
    
    var strClickedOn = el.innerHTML.trim().toLowerCase().replace('transactions','contributions');
    var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    var r = document.getElementById('bodyDiv');
    r.innerHTML = '<div class="waitingDiv"><img src="/dms/img/loading.gif" title="loading..." align="middle" style="margin-right: 15px;" /> loading...</div>';
	
    var strvar = '?d='+x+'&s='+s+'&datetim=' + tim;
	http.open("GET", "load.contact." + strClickedOn + ".php" + strvar);
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
        var s=r.getElementsByTagName('script');
        for(var i=0;i<s.length;++i)window.eval(s[i].innerHTML);
	  }
	}
	http.send(null);
    
    var navList = document.getElementById('navList');
    var items = navList.getElementsByTagName('li');
    for (var i=0;i<items.length;i++) {
        items[i].className = '';
    }
    el.className = 'selectedNav';
}

function changeMembership() {
    var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    var r = document.getElementById('bodyDiv');
    var m = document.getElementById('membership').value;
    
    if (m.length==0) {
        btnAddMembershipClick();
        return;
    }
    
    var d = document.getElementById('dnr_no').value;
    var strvar = '?m='+m+'&d='+d+'&datetim=' + tim;
	http.open("GET", "load.contact.memberships.php" + strvar);
    r.innerHTML = '<div class="waitingDiv"><img src="/dms/img/loading.gif" title="loading..." align="middle" style="margin-right: 15px;" /> loading...</div>';
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
        var s=r.getElementsByTagName('script');
        for(var i=0;i<s.length;++i)window.eval(s[i].innerHTML);
	  }
	}
	http.send(null);
}

function hideEditForm() {
    emptyResults();
    var editFormDiv = document.getElementById('editDonorDiv');
    editFormDiv.style.display = 'none';
    var screenProtector = document.getElementById('screenProtectorDiv');
    screenProtector.style.display = 'none';
    $("#contactSearchResultdiv").hide();
    
}

function emptyResults() {
    resultContent = [];
    $("#messageDiv").empty().hide();
    $("#frmDiv").height(525);
}

function showEditForm(frm) {
    $("#"+frm).click();
    console.log("#"+frm);
}

function getEditForm(frm) {
    $('#frmDiv').empty().append('<img src="/dms/img/loading.gif" style="vertical-align: middle" /> loading...');
    var d = $('#dnr_no').val();
    $.get('edit.contact.'+frm+'.php',{d: d}).done(function(data){
        $('#frmDiv').empty().append(data);
        getEditDetail();
    });
}

function btnAddMembershipClick() {
    showEditForm('Memberships');
}

function getEditDetail() {
    var cid = $("#cid").val();
    if (cid.length>0) {
        $.get( "contact.last.edit.php",{ contact_id: cid }, function( data ) {
            $( "#userDetailDiv" ).empty().append( data );
        });
    }
}

function reloadSummary() {
    var dnr = $("#dnr_no").val();
    $.get( "load.contact.summary.php", { d: dnr })
        .done(function( data ) { 
        $( "#bodyDiv" ).empty().append( data );
    });
}

var resultContent = [];
$(document).ready(function() {
    
    $("#divNextRecord,#divPrevRecord").click(function(){
        var dnr = $(this).attr('d');
        window.location='load.contact.php?d='+dnr;
    });
    
    if (resultContent.hasOwnProperty('message')) {
        checkForResult();
    }
    
    $("#btnShowResults").click(function(){
        $("#frmContentDiv").hide();
        showResults();
        $("#divSaveResults").show();
        $("#btnShowDetail").show();
        $(this).hide();
    });
    
    $("#btnShowDetail").click(function(){
        $("#frmContentDiv").show();
        $("#btnShowResults").show();
        $("#divSaveResults").hide();
        $(this).hide();
    });
    
    $(".navItem").click(function() {
        $(".navItem").each(function() {
            $(this).removeClass('selectedNav');
        });
        $(this).addClass('selectedNav');
        getEditForm($(this).attr('id'));
        $("#notificationsDiv").hide();
        $("#contactSearchResultdiv").hide();
        $('#editDonorDiv').show();
        $('#screenProtectorDiv').show();
        checkForResult();
    });
});

function showResults() {
    $("#divResult").empty();
    $("#divResult").append('<div id="postHeadingDiv">Posted Variables</div>');
    $("#divResult").append('<div id="postDiv"></div>');
    if (resultContent.hasOwnProperty('post')) {
        $.each(resultContent.post,function(index,value){
            $("#postDiv").append('<div class="key">Key: '+index+'</div><div class="val">Value: '+value+'</div>');
        });        
    } else {
        $("#postDiv").append('<div class="key">No posted variables returned</div>');
    }
    
    $("#divResult").append('<div id="resultHeadingDiv">Results</div>');
    $("#divResult").append('<div id="resultDiv"></div>');
    if (resultContent.hasOwnProperty('result')) {
        $.each(resultContent.result,function(index,value){
            $.each(value,function(k,v){
                
                if (k=='request') {
                    $("#resultDiv").append('<div class="resHeading">Request</div>');
                    $.each(v,function(r,rv){
                        $("#resultDiv").append('<div class="key">Key: '+r+'</div><div class="val">Value: '+rv+'</div>');    
                    });
                    
                }
                if (k=='result') {
                    $("#resultDiv").append('<div class="resHeading">Result</div>');
                    $.each(v,function(r,rv){
                        if (rv instanceof Object) {
                            $("#resultDiv").append('<div class="resHeading">Values</div>');
                            $.each(rv, function(o,ov){
                                $("#resultDiv").append('<div class="resHeading">'+o+'</div>');
                                $.each(ov,function(kValue,vValue) {
                                    $("#resultDiv").append('<div class="key">Key: '+kValue+'</div><div class="val">Value: '+vValue+'</div>');
                                });
                            });
                        } else {
                            $("#resultDiv").append('<div class="key">Key: '+r+'</div><div class="val">Value: '+rv+'</div>');
                        }
                    });
                }
            });
        });
    } else {
        $("#resultDiv").append('<div class="key">No results returned</div>');
    }
    

}

function checkForResult() {
    $("#btnShowResults").show();
    $("#btnShowDetail").hide();
    if (resultContent.hasOwnProperty('message')) {
        var clsLink = '<div id="clsMessageDiv">x</div>';
        $("#messageDiv").empty().append(resultContent.message+clsLink).fadeIn(500);
        var newHeight = $("#messageDiv").outerHeight() + $("#messageDiv").css('margin-bottom');
        $("#frmDiv").animate({height: newHeight}, 100);
        $("#clsMessageDiv").click(function() {
            $("#messageDiv").hide();
            $("#frmDiv").height(525);
            resultContent = [];
        });
    }
}