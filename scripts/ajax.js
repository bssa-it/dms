var http = false;

if(navigator.appName == "Microsoft Internet Explorer") {
  http = new ActiveXObject("Microsoft.XMLHTTP");
} else {
  http = new XMLHttpRequest();
}

function getdnrnos() {

    var w = document.getElementById('srch_email');
	var x = document.getElementById('srch_cell_no');
	var y = document.getElementById('srch_dnr_name');
	var z = document.getElementById('srch_dnr_addr');
	var a = document.getElementById('srch_dnr_deleted');
	var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
	var r = document.getElementById('srch_result');
	var f = document.getElementById('srch_form');
	var e = document.form1.enginetype;
	
	if (w.value.length == 0 && x.value.length == 0 && y.value.length == 0 && z.value.length == 0) {
		alert('Please enter a search value');
		return;
	}
	
	f.style.display = 'none';
	r.style.display = '';
	
	var strvar = '?enginetype=' + e.value + '&srch_email=' + w.value + '&srch_cell_no=' + x.value + '&srch_dnr_name=' + y.value + '&srch_dnr_addr=' + z.value + '&srch_dnr_deleted=' + a.value + '&datetim=' + tim;
	http.open("GET", "engine.php" + strvar);
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
	  }
	}
	http.send(null);

}

function postmthatha() {

	var dt = new Date();
	var tim = dt.getHours() + ':' + dt.getMinutes() + ':' + dt.getSeconds();
	var c = document.getElementById('cat_id');
	var d = document.getElementById('date');
	var v = document.getElementById('value');
	var e = document.getElementById('enginetype');
	var r = document.getElementById('ajax_mthatha');
	
	if (c.value.length == 0 || c.value == '-- Select --') {
		alert('Please select a Category Code');
		c.focus();
		return;
	}
	if (d.value.length == 0) {
		alert('Please insert a date');
		d.focus();
		return;
	}
	if (v.value.length == 0) {
		alert('Please insert a value');
		v.focus();
		return;
	}
	
	var strvar = 'cat_id=' + c.value + '&date=' + d.value + '&value=' + v.value + '&enginetype=' + e.value + '&datetim=' + tim;
	
	http.open("POST", "engine.php",true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
		document.getElementById('btn_mthatha').disabled = true;
	  }
	}
	http.send(strvar);

}

function resetfrm(x) {
	
	switch (x) {
		case 'mthatha':
			document.form1.reset();
			document.getElementById('btn_mthatha').disabled = false;
			break;
	}

}

function getimportverify() {

	var n = document.getElementById('vregion');
	var v = document.getElementById('vvalue');
	var e = document.form3.enginetype;
	var r = document.getElementById('ajax_import');
	var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

	if (n.value.length == 0 || n.value == '-- Select --') {
		alert('Please select a Region');
		n.focus();
		return;
	}
	if (v.value.length == 0) {
		alert('Please insert a value');
		v.focus();
		return;
	}

	var strvar = '?vregion=' + n.value + '&vvalue=' + v.value + '&enginetype=' + e.value + '&datetim=' + tim;
	http.open("GET", "engine.php" + strvar);
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
	  }
	}
	http.send(null);
	
}

function exportcons(bydn) {

	var e = document.form2.enginetype;
    var b = (bydn) ? 'Y':'N';
	var r = document.getElementById('ajax_export');
	var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
	
	r.innerHTML = 'exporting... <img src="img/loading.gif">';

	var strvar = '?enginetype=' + e.value + '&bydn='+b+'&datetim=' + tim;
	http.open("GET", "engine.php" + strvar);
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
	  }
	}
	http.send(null);

}

function checkimporttar(x) {
	
	var r = document.getElementById('ajax_tar_' + x);
	var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
	var strvar = '?enginetype=tarfiles&region='+ x +'&datetim=' + tim;

	http.open("GET", "engine.php" + strvar);
	http.onreadystatechange=function() {
		if(http.readyState == 4) {
			r.innerHTML = http.responseText;
		}
	}
	http.send(null);
}

function getstats() {
	
	var r = document.getElementById('ajax_usage');
	var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
	var strvar = '?enginetype=stats&datetim=' + tim;
	
	http.open("GET", "engine.php" + strvar);
	http.onreadystatechange=function() {
		if(http.readyState == 4) {
			r.innerHTML = http.responseText;
			drwgrph();
		}
	}
	http.send(null);
	
}