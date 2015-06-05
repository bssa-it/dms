CKEDITOR.plugins.add('strinsert',
{
  requires : ['richcombo'],
  init : function( editor )
  {
    // add the menu to the editor
    editor.ui.addRichCombo('strinsert',
    {
      label:     'DMS Merge Fields',
      title:     'Donor Management System Fields',
      voiceLabel: 'DMS Field',
      className:   'cke_format',
      multiSelect:false,
      panel:
      {
        css: [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ],
        voiceLabel: editor.lang.panelVoiceLabel
      },
      init: function()
      {
        for (var z in mergeGroups) 
        {
            this.startGroup( mergeGroups[z][1] );
            var strings = window[mergeGroups[z][0]];
            for (var i in strings)
            {
              this.add(strings[i][0], strings[i][1], strings[i][2]);
            }
        }
      },

      onClick: function( value )
      {
        editor.focus();
        editor.fire( 'saveSnapshot' );
        editor.insertHtml(value);
        editor.fire( 'saveSnapshot' );
      }
    });   
  }
});

var http = false;
if(navigator.appName == "Microsoft Internet Explorer") {
  http = new ActiveXObject("Microsoft.XMLHTTP");
} else {
  http = new XMLHttpRequest();
}

var http2 = false;
if(navigator.appName == "Microsoft Internet Explorer") {
  http2 = new ActiveXObject("Microsoft.XMLHTTP");
} else {
  http2 = new XMLHttpRequest();
}

function saveLetter() {
    if (validate()) {
        updateXml();
        alert('Changes have been saved.');   
    }
}

function showEmail(el) {
    document.getElementById('emailDiv').style.display = (el.value=='Email') ? 'inline':'none';
    document.getElementById('printDiv').style.display = (el.value=='Postal Mail') ? 'inline':'none';
    showUpdateBtn();
}

function preview(prevAll) {
    document.getElementById('previewAll').value = prevAll;
    document.frmLetter.action = 'preview.letter.php';
    document.frmLetter.target = "_blank";
    document.frmLetter.submit();
}

function openNext() {
    var haveCurrent = false;
    var nextFileName = '';
    for (var i=0;i<fileList.length;i++) { 
        if (haveCurrent) {
            nextFileName = fileList[i].toString();
            break;
        }
        haveCurrent = (fileList[i].toString().indexOf(document.getElementById('filename').value.toString())>0);
    }
    
    var isValidated = validate();
    if (isValidated) {
        updateXml();
        populateForm(nextFileName);
        updateLetterNo(i+1);
    } 
}

function updateLetterNo(x){
    document.getElementById('letterCnt').innerHTML = x.toString();
}

function populateForm(fileName) {
    
    http.onreadystatechange=function() {
	  if(http.readyState == 4 && http.status == 200) {
		var xmlData = http.responseXML;
        var letter = xmlData.getElementsByTagName("letter");
        var htmlFile = letter[0].getElementsByTagName('filename')[0].firstChild.nodeValue;
        var printLeftMargin = letter[0].getElementsByTagName('tpl_marginLeft')[0].firstChild.nodeValue;
        var printTopMargin = letter[0].getElementsByTagName('tpl_marginTop')[0].firstChild.nodeValue;
        var printRightMargin = letter[0].getElementsByTagName('tpl_marginRight')[0].firstChild.nodeValue;
        var printBottomMargin = letter[0].getElementsByTagName('tpl_marginBottom')[0].firstChild.nodeValue;
        var exportMethod = letter[0].getElementsByTagName('method')[0].firstChild.nodeValue;
        var strReady = letter[0].getElementsByTagName('ready')[0].firstChild.nodeValue;
        var strToName = (letter[0].getElementsByTagName('emailname')[0].firstChild==null) ? '' : letter[0].getElementsByTagName('emailname')[0].firstChild.nodeValue;
        var strSubject = (letter[0].getElementsByTagName('subject')[0].firstChild==null) ? '' :letter[0].getElementsByTagName('subject')[0].firstChild.nodeValue;
        var strEmail = (letter[0].getElementsByTagName('email')[0].firstChild==null) ? '' : letter[0].getElementsByTagName('email')[0].firstChild.nodeValue;
        var strImpersonate = (letter[0].getElementsByTagName('impersonate')[0].firstChild==null) ? '' :letter[0].getElementsByTagName('impersonate')[0].firstChild.nodeValue;
        var strDepartment = (letter[0].getElementsByTagName('department')[0].firstChild==null) ? '' :letter[0].getElementsByTagName('department')[0].firstChild.nodeValue;
        var strContact = (letter[0].getElementsByTagName('contact_id')[0].firstChild==null) ? '' :letter[0].getElementsByTagName('contact_id')[0].firstChild.nodeValue;
        var strContribution = (letter[0].getElementsByTagName('contribution_id')[0].firstChild==null) ? '' :letter[0].getElementsByTagName('contribution_id')[0].firstChild.nodeValue;
        var strRegion = (letter[0].getElementsByTagName('region')[0].firstChild==null) ? '' :letter[0].getElementsByTagName('region')[0].firstChild.nodeValue;
        
        //load form details 
        document.getElementById('tpl_marginLeft').value = printLeftMargin;
        document.getElementById('tpl_marginTop').value = printTopMargin;
        document.getElementById('tpl_marginRight').value = printRightMargin;
        document.getElementById('tpl_marginBottom').value = printBottomMargin;
        document.getElementById('method').value = exportMethod;
        document.getElementById('ready').checked = (strReady=='Y');
        document.getElementById('emailname').value = strToName;
        document.getElementById('subject').value = strSubject;
        document.getElementById('impDepartment').checked = (strImpersonate=='Y');
        document.getElementById('impUser').checked = (strImpersonate=='N');
        document.getElementById('filename').value = htmlFile.substring(htmlFile.indexOf('/')+1,htmlFile.indexOf('.htm'));
        document.getElementById('contact_id').value = strContact;
        document.getElementById('contribution_id').value = strContribution;
        document.getElementById('region').value = strRegion;
        resetEmailSelect(strEmail);
        showEmail(document.getElementById('method'));
        
        //load file details
        document.getElementById('xmlFilename').value = fileName;
        document.getElementById('htmlFilename').value = htmlFile;
        
        //show next previous buttons
        document.getElementById('prevBtn').style.display = (fileList[0].toString()!=fileName) ? 'block':'none';
        var recordCount = fileList.length-1;
        document.getElementById('nextBtn').style.display = (fileList[recordCount].toString()!=fileName) ? 'block':'none';
        
        //show update button
        document.getElementById('updateBtn').style.display = (document.getElementById('ready').checked) ? 'block':'none';
        
        //export image
        showUpdateBtn();
        
        //get letter from HTML
        getLetterHtml();
        
        for (var i=0;i<departmentList.length;i++) {
            if (departmentList[i][0].trim()==strDepartment.trim()) {
                document.getElementById('lblDepartment').innerHTML = departmentList[i][1];
                break;  
            } 
        }
        
        
      }
    }
    var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    http.open("GET",fileName+'?t='+tim,true);
	http.send();
    
}

function showUpdateBtn() {
    document.getElementById('updateBtn').src = (document.getElementById('method').value=='Email') ? 'img/email.png':'img/print-pdf.png';
    document.getElementById('updateBtn').style.display = (document.getElementById('ready').checked) ? 'block':'none';
}

function getLetterHtml() {
    var fileName = document.getElementById('htmlFilename').value;
    http.onreadystatechange=function() {
        if(http.readyState == 4 && http.status == 200) {
            CKEDITOR.instances['letterEditor'].setData(http.responseText);
        }
    }
    var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    http.open("GET",fileName+'?t='+tim,true);
	http.send();
}

function openPrevious() {
    var haveCurrent = false;
    var prevFileName = '';
    for (var i=0;i<fileList.length;i++) {
        haveCurrent = (fileList[i].toString().indexOf(document.getElementById('filename').value.toString())>0);
        if (haveCurrent) break;
        prevFileName = fileList[i].toString();
    }
    
    var isValidated = validate();
    if (isValidated) {
        updateXml();
        populateForm(prevFileName);
        updateLetterNo(i);   
    }
}

function checkEmailAddress() {
    if (document.getElementById('email').value.trim().length==0) {
        alert('Sorry, there is no email address.'); 
    }
}

function validate() {
    var strMethod = document.getElementById('method').value;
    switch (strMethod) {
        case 'Postal Mail':
            var marginLeft = document.getElementById('tpl_marginLeft');
            if (marginLeft.value.trim().length==0||isNaN(marginLeft.value)||marginLeft.value<0) {
                alert('Your left margin is not in the correct format.');
                marginLeft.focus();
                return false;
            }
            var marginTop = document.getElementById('tpl_marginTop');
            if (marginTop.value.trim().length==0||isNaN(marginTop.value)||marginTop.value<0) {
                alert('Your top margin is not in the correct format.');
                marginTop.focus();
                return false;
            }
            var marginRight = document.getElementById('tpl_marginRight');
            if (marginRight.value.trim().length==0||isNaN(marginRight.value)||marginRight.value<0) {
                alert('Your right margin is not in the correct format.');
                marginRight.focus();
                return false;
            }
            var marginBottom = document.getElementById('tpl_marginBottom');
            if (marginBottom.value.trim().length==0||isNaN(marginBottom.value)||marginBottom.value<0) {
                alert('Your bottom margin is not in the correct format.');
                marginBottom.focus();
                return false;
            }
            break;
        case 'Email':
            var email = document.getElementById('email');
            if (email.value.trim().length==0||email.value.indexOf('@')<0||email.value.indexOf('.')<0) {
                alert('Your email address is not in the correct format.');
                email.focus();
                return false;
            }
    }
    
    var letter = document.getElementById('letterEditor');
    if (letter.value.trim().length==0) {
        alert('Your letter is empty.');
        marginBottom.focus();
        return false;
    }
    
    return true;
}

function updateXml() {
    var dt = new Date();
	var tim = dt.getHours() + ':' + dt.getMinutes() + ':' + dt.getSeconds();
    var a = document.getElementById('tpl_marginLeft').value;
    var b = document.getElementById('tpl_marginTop').value;
    var c = document.getElementById('tpl_marginRight').value;
    var d = document.getElementById('tpl_marginBottom').value;
    var e = document.getElementById('email').value;
    var f = document.getElementById('method').value;
    var g = document.getElementById('xmlFilename').value;
    var h = document.getElementById('htmlFilename').value;
    var i = (document.getElementById('ready').checked) ? 'Y':'N';
    var j = CKEDITOR.instances['letterEditor'].getData();
    CKEDITOR.instances['letterEditor'].setData(j);
    var k = document.getElementById('emailname').value;
    var l = document.getElementById('subject').value;
    var m = (document.getElementById('impDepartment').checked) ? 'Y':'N';
    var n = document.getElementById('department').value;
    var contact = document.getElementById('contact_id').value;
    var contribution = document.getElementById('contribution_id').value;
    var r = document.getElementById('region').value;
	
   	var strvar = 'tpl_marginLeft=' + a + '&tpl_marginTop=' + b + '&tpl_marginRight=' + c + '&tpl_marginBottom=' + d;
    strvar += '&email=' + e + '&method=' + f + '&xmlFilename=' + g + '&htmlFilename=' + h + '&ready=' + i
    strvar += '&letter=' + encodeURIComponent(j) + '&emailname=' + k + '&subject=' + encodeURIComponent(l);
    strvar += '&impersonate=' + m + '&department=' + n + '&contact_id=' + contact + '&contribution_id=' + contribution + '&region=' + r + '&datetim=' + tim;
    
    /*http.onreadystatechange=function() {
	   if(http.readyState == 4 && http.status == 200) {
		alert(http.responseText);
	  }
	}*/
    http.open("POST", "update.letter.php",false);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.send(strvar);

}

function sendForm() {
    if (!document.getElementById('ready').checked) {
        var cnfm = confirm('Are you sure you want to update and export this letter? \nClick Ok to continue.');
        if (!cnfm) return;
    }
    if (validate()) 
    {
        updateXml();
        document.frmLetter.action = "finish.letter.php";
        document.frmLetter.target = "_self";
        document.frmLetter.submit();
    }
}

function updateAll() {
    var conf = confirm('Are you sure you want to print/email all your unevaluated letters?\nClick Ok to continue.');
    if (conf) {
        document.getElementById('progressDivBackground').style.display = 'block';
        var isValidated = validate();
        if (isValidated) {
            updateXml();
            completeExport();
            var i = setInterval(function(){
               getStatusDetails();
            },750);
        } else {
            document.getElementById('updateAll').value = 'N';
            document.getElementById('progressDivBackground').style.display = 'none';
        }
    } else {
        document.getElementById('updateAll').value = 'N';
        document.getElementById('progressDivBackground').style.display = 'none';
    } 
}

function getStatusDetails() {
    var statusBox = document.getElementById('statusDiv');
    var startingTotal = document.getElementById('startingTotal').innerHTML;
    var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    http.onreadystatechange=function() {
        if(http.readyState == 4 && http.status == 200) {
            if (http.responseText=='completed') window.location = 'result.letter.php?d='+tim;
            statusBox.innerHTML = http.responseText;
        }
    }
    http.open("GET",'count.unevaluated.php?t='+startingTotal+'&d='+tim,true);
	http.send();
}
function completeExport() {
    var d = new Date();
    var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    http.open("GET",'finish.letter.php?d='+tim,true);
    http.send();
}

function setImpersonate() {
    document.getElementById('impDepartment').checked = true;
    document.getElementById('impUser').checked = false;
}

function resetEmailSelect(email) {
    $("#email").find('option').remove();
    var contactId = $("#contact_id").val();
    $.get('get.contact.emailAddresses.php', {contact_id: contactId}).done(function(jdata){
        var eAdds = $.parseJSON(jdata);
        $.each(eAdds,function(i,v){
            var selected = (eAdds[i].emailAddress == email) ? ' SELECTED':'';
            $("#email").append('<option value="'+eAdds[i].emailAddress+'"'+selected+'>'+eAdds[i].emailAddress+'</option)');
        });
    });
}

$(document).ready(function() {
   $("#btnDelLetter").click(function(){
      var x = $("#xmlFilename").val();
      var h = $("#htmlFilename").val();
      var cfm = confirm('Are you sure you want to delete this letter?');
      if (cfm) {
          $.post("delete.letter.php",{xmlFilename:x,htmlFilename:h},function(data){
              window.location.href = 'evaluate.letter.php';
          });
      }
   }); 
});