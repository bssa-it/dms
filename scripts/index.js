function showAppSettings(x) {
    var screenProtector = document.getElementById('screenProtectorDiv');
    screenProtector.style.display = '';
    var settingsDiv = document.getElementById('widgetSettingsDiv');
    settingsDiv.style.display = '';
    var d = new Date();
	var tim = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
    var r = document.getElementById('widgetSettingsForm');
    r.innerHTML = '<img src="img/loading.gif" />';
	
    var strvar = '?i='+x+'&datetim=' + tim;
	http.open("GET", "load.widget.settings.php" + strvar);
	http.onreadystatechange=function() {
	  if(http.readyState == 4) {
		r.innerHTML = http.responseText;
        
        var s=r.getElementsByTagName('script');
        for(var i=0;i<s.length;++i)window.eval(s[i].innerHTML);
	  }
	}
	http.send(null);    
}

function hideAppSettings() {
    var screenProtector = document.getElementById('screenProtectorDiv');
    screenProtector.style.display = 'none';
    var settingsDiv = document.getElementById('widgetSettingsDiv');
    settingsDiv.style.display = 'none';
    var r = document.getElementById('widgetSettingsForm');
    r.innerHTML = '&nbsp;';
}