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

function loadTemplate() {
    var tplid = document.getElementById('tpl_id');
	var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
	
	if (tplid.value.length == 0) return;
	
	var strvar = '?tpl_id=' + tplid.value + '&datetim=' + tim;
	http.open("GET", "load.letter.php" + strvar);
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		CKEDITOR.instances['letterEditor'].setData(http.responseText);
	  }
	}
	http.send(null);
}

function changeTemplate() {
    loadTemplate();
    var tplid = document.getElementById('tpl_id');
    var tplname = document.getElementById("tpl_name");
    var tplAccess = document.getElementById("tpl_accessLevel");
    var tplMarginLeft = document.getElementById("tpl_marginLeft");
    var tplMarginTop = document.getElementById("tpl_marginTop");
    var tplMarginRight = document.getElementById("tpl_marginRight");
    var tplMarginBottom = document.getElementById("tpl_marginBottom");
    
    for (var i in tpl) {
        if (tpl[i][0]==tplid.value) {
            tplname.value = tpl[i][2];
            tplAccess.value = tpl[i][3];
            tplMarginLeft.value = tpl[i][4];
            tplMarginTop.value = tpl[i][5];
            tplMarginRight.value = tpl[i][6];
            tplMarginBottom.value = tpl[i][7];
            break; 
        }
    } 
}

function openPreview() {
    var tpl = CKEDITOR.instances['letterEditor'].getData();
    if (tpl.length==0) {
        alert("Please create your letter before you preview it.");
        return;
    }
    document.frmTemplate.target = '_blank';
    document.frmTemplate.action = 'preview.letter.php';
    document.frmTemplate.submit();
}

function saveTemplate() {
    var tmpl = CKEDITOR.instances['letterEditor'].getData();
    var tplid = document.getElementById('tpl_id');
    var tplname = document.getElementById('tpl_name');
    
    if (tmpl.length==0) {
        alert("Please create your letter before you save it.");
        return;
    }
    if (tplname.value.length==0) {
        alert("Please insert a name for this template");
        tplname.focus();
        return;
    }
    
    for (var i in tpl) {
        if (tpl[i][0]==tplid.value&&tpl[i][1]=='N') {
            var r=confirm("This template does not belong to you.  Would you like to save a copy instead?");
            if (r)
            {
              tplid.value = '';
              break;
            }
            else
            {
              return;
            }
        }
    }
    
    document.frmTemplate.target = '';
    document.frmTemplate.action = 'save.letter.php';
    document.frmTemplate.submit();
}

function mergeRecords() {
    
    var tmpl = CKEDITOR.instances['letterEditor'].getData();
    var tplid = document.getElementById('tpl_id');
    var tplname = document.getElementById('tpl_name');
    var tplChkbox = document.getElementById('saveTemplate');
    
    if (tmpl.length==0) {
        alert("Please create your letter before you merge it with your records.");
        return;
    }
    if (tplname.value.length==0&&tplChkbox.checked) {
        alert("Please insert a name for this template");
        tplname.focus();
        return;
    }
    for (var i in tpl) {
        if (tpl[i][0]==tplid.value&&tpl[i][1]=='N') {
            var r=confirm("This template does not belong to you.  Would you like to save a copy instead?");
            if (r)
            {
              tplid.value = '';
              break;
            }
            else
            {
              return;
            }
        }
    }
    
    document.frmTemplate.target = '';
    document.frmTemplate.action = 'evaluate.letter.php';
    document.frmTemplate.submit();
}