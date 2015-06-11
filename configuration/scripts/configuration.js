function saveConnections() {
    var a = document.getElementById('dms_host');
    var b = document.getElementById('dms_username');
    var c = document.getElementById('dms_password');
    var d = document.getElementById('dms_database');
    var e = document.getElementById('jml_host');
    var f = document.getElementById('jml_username');
    var g = document.getElementById('jml_password');
    var h = document.getElementById('jml_database');
    var n = document.getElementById('civ_host');
    var o = document.getElementById('civ_username');
    var p = document.getElementById('civ_password');
    var q = document.getElementById('civ_database');
    var i = document.getElementById('apiCrmConfig');
    var j = document.getElementById('apiCrmCoreConfig');
        
    if (a.value.length==0) {
        alert('Please enter a Host for the DMS connection');
        a.focus();
        return;
    }
    
    if (b.value.length==0) {
        alert('Please enter the username for the DMS connection');
        b.focus();
        return;
    }
    
    if (c.value.length==0) {
        alert('Please enter the password for the DMS connection');
        c.focus();
        return;
    }
    
    if (d.value.length==0) {
        alert('Please enter the database for the DMS connection');
        d.focus();
        return;
    }
    
    if (n.value.length==0) {
        alert('Please enter a Host for the CiviCRM connection');
        n.focus();
        return;
    }
    
    if (o.value.length==0) {
        alert('Please enter the username for the CiviCRM connection');
        o.focus();
        return;
    }
    
    if (p.value.length==0) {
        alert('Please enter the password for the CiviCRM connection');
        p.focus();
        return;
    }
    
    if (q.value.length==0) {
        alert('Please enter the database for the CiviCRM connection');
        q.focus();
        return;
    }
    
    if (e.value.length==0) {
        alert('Please enter a Host for the Joomla connection');
        e.focus();
        return;
    }
    
    if (f.value.length==0) {
        alert('Please enter the username for the Joomla connection');
        f.focus();
        return;
    }
    
    if (g.value.length==0) {
        alert('Please enter the password for the Joomla connection');
        g.focus();
        return;
    }
    
    if (h.value.length==0) {
        alert('Please enter the database for the Joomla connection');
        h.focus();
        return;
    }
    
    if (i.value.length==0) {
        alert('Please insert the CiviCRM API configuration file.');
        g.focus();
        return;
    }
    
    if (j.value.length==0) {
        alert('Please insert the CiviCRM API Core configuration file.');
        h.focus();
        return;
    }
    
    document.frmConnections.submit(); 
}

function saveAuth() {
    var i = document.getElementById('aut_select');
    var j = document.getElementById('aut_insert');
    var k = document.getElementById('aut_update');
    var l = document.getElementById('aut_delete');
    var m = document.getElementById('aut_admin');
    
    if (i!=null&&i.value.length==0) {
        alert('Please select the Joomla group SELECT permission');
        i.focus();
        return;
    }
    
    if (j!=null&&j.value.length==0) {
        alert('Please select the Joomla group INSERT permission');
        j.focus();
        return;
    }
    
    if (k!=null&&k.value.length==0) {
        alert('Please select the Joomla group UPDATE permission');
        k.focus();
        return;
    }
    
    if (l!=null&&l.value.length==0) {
        alert('Please select the Joomla group DELETE permission');
        l.focus();
        return;
    }
    
    if (m!=null&&m.value.length==0) {
        alert('Please select the Joomla group ADMIN permission');
        m.focus();
        return;
    }
    
    document.frmAuthorisation.submit();
}

function addImportfile() {
    var a = document.getElementById('newImportFile');
    
    if (a.value.length==0) {
        alert('Please SCO table name.');
        a.focus();
        return;
    }
    
    document.frmImportfiles.submit();
}

function saveBudget() {
    document.frmBudget.submit();
}

function saveBam() {
    var a = document.getElementById('bamMemTypeId');
    
    if (a.value.length==0) {
        alert('Please enter the BAM Membership Type');
        a.focus();
        return;
    }
    
    document.frmBam.submit();  
}

function insertClass() {
    
    $("#saveInsertClassResult").hide();
    var a = document.getElementById('className');
    var b = document.getElementById('classFilename');
    
    if (a.value.length==0) {
        alert('Please insert the name of the class');
        a.focus();
        return;
    }
    
    if (b.value.length==0) {
        alert('Please insert the filename of the class');
        b.focus();
        return;
    }
    
    $.post("save.class.configuration.php",$("#frmClass").serialize()).done(function(data){
        var json = $.parseJSON(data);
        $.get("get.classes.php").done(function(data){
            $("#classesListDiv").empty().append(data); 
            $("#saveInsertClassResult").empty().append(json.result);
            $("#saveInsertClassResult").show();
            $("#className").val('');
            $("#classFilename").val('');
            $("#isExtension").attr('checked', false);
        });
    });
}

function showDiv(divId,el) {
    var elements = document.getElementsByClassName('tabSelected');
    for (var x in elements) elements[x].className = 'tab';
    el.className = 'tabSelected';
    for (var i in arrTabDivs) document.getElementById(arrTabDivs[i]).style.display = 'none';
    document.getElementById(divId).style.display = '';
    
}

function saveSql() {
    document.frmSql.submit();
}

$(document).ready(function() {
    $("#btnSowerSettings").click(function(){
        if ($("#sowerAddressTypeId").val().length==0) {
            alert('Please insert the id for the sower address type');
            $("#sowerAddressTypeId").focus();
            return;
        }
        $("#frmSowerSettings").submit();
    });
    $("#btnCiviOptGroups").click(function(){
        var $a = $('#phoneTypesOptGroup');
        if ($a.val().length==0) {
            alert('Please enter the Phone Types Option Group');
            $a.focus();
            return;
        }
        var $b = $('#languageOptGroup');
        if ($b.val().length==0) {
            alert('Please enter the Languages Option Group');
            $b.focus();
            return;
        }
        var $c = $('#titlesOptGroup');
        if ($c.val().length==0) {
            alert('Please enter the Titles Option Group');
            $c.focus();
            return;
        }
        var $d = $('#genderOptGroup');
        if ($d.val().length==0) {
            alert('Please enter the Gender Option Group');
            $d.focus();
            return;
        }
        var $e = $('#communicationOptGroup');
        if ($e.val().length==0) {
            alert('Please enter the Communication Methods Group');
            $e.focus();
            return;
        }
        $("#frmCiviOptGroups").submit();  
    });
    
    $("#btnEmail").click(function(){
        var a = document.getElementById('emailHost');
        var b = document.getElementById('emailAddress');
        var c = document.getElementById('emailFromName');
        var d = document.getElementById('emailPassword');
        var e = document.getElementById('emailDomain');

        if (a.value.length==0) {
            alert('Please enter the email server host');
            a.focus();
            return;
        }

        if (b.value.length==0) {
            alert('Please enter the from address');
            b.focus();
            return;
        }

        if (c.value.length==0) {
            alert('Please enter the from name');
            c.focus();
            return;
        }

        if (d.value.length==0) {
            alert('Please enter the zimbra account password');
            d.focus();
            return;
        }

        if (e.value.length==0) {
            alert('Please enter the email domain');
            e.focus();
            return;
        }

        document.frmEmail.submit();
    });
    $("#btnAcknowledgement").click(function(){
        var a = document.getElementById('acknowledgementSubject');
        var b = document.getElementById('marginLeft');
        var c = document.getElementById('marginTop');
        var d = document.getElementById('marginRight');
        var e = document.getElementById('marginBottom');
        var f = document.getElementById('afrDefaultSalutation');
        var g = document.getElementById('engDefaultSalutation');

        if (a.value.length==0) {
            alert('Please enter the default email subject');
            a.focus();
            return;
        }

        if (b.value.length==0) {
            alert('Please enter the left margin');
            b.focus();
            return;
        }

        if (c.value.length==0) {
            alert('Please enter the top margin');
            c.focus();
            return;
        }

        if (d.value.length==0) {
            alert('Please enter the right margin');
            d.focus();
            return;
        }

        if (e.value.length==0) {
            alert('Please enter the bottom margin');
            e.focus();
            return;
        }

        if (f.value.length==0) {
            alert('Please enter the afrikaans salutation');
            f.focus();
            return;
        }
        
        if (g.value.length==0) {
            alert('Please enter the english salutation');
            g.focus();
            return;
        }
        document.frmAcknowledgement.submit();
    });
    $("#btnVersions").click(function(){
        if ($("#civicrmVersion").val().length==0) {
            alert('Please insert the CiviCRM version number');
            $("#civicrmVersion").focus();
            return;
        }
        if ($("#dmsVersion").val().length==0) {
            alert('Please insert the DMS version number');
            $("#dmsVersion").focus();
            return;
        }
        $("#frmVersions").submit();
    });
    $.get("get.classes.php").done(function(data){
       $("#classesListDiv").empty().append(data); 
    });
    $("#btnInsertClass").bind("click",insertClass);
});