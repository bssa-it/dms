var http = false;

if(navigator.appName == "Microsoft Internet Explorer") {
  http = new ActiveXObject("Microsoft.XMLHTTP");
} else {
  http = new XMLHttpRequest();
}

function showSubMenu(strMenu,strSubMenu) {
    var x = document.getElementById(strSubMenu);
    var y = document.getElementById(strMenu);
    var l = y.offsetLeft-15;
    var t = y.offsetTop+12;
    
    x.style.left = l+'px';
    x.style.top = t+'px';
    x.style.display = 'block';    
}

function positionNotificationsDiv() {
    var x = document.getElementById('notificationsDiv');
    var y = document.getElementById('notificationLinkDiv');
    var t = y.offsetTop+40;
    x.style.top = t+'px';
}


function hideSubMenu(strSubMenu) {
    var x = document.getElementById(strSubMenu);
    x.style.display = 'none';
}

function showHideImpersonatedUser() {
    var screenProtector = document.getElementById('screenProtectorDiv');
    screenProtector.style.display = (screenProtector.style.display=='') ? 'none':'';
    var settingsDiv = document.getElementById('impersonateUserDetailDiv');
    settingsDiv.style.display = (settingsDiv.style.display=='') ? 'none':'';    
}

function getMonthFromDate(d) {
    
    var month = new Array();
    month[0] = "January";
    month[1] = "February";
    month[2] = "March";
    month[3] = "April";
    month[4] = "May";
    month[5] = "June";
    month[6] = "July";
    month[7] = "August";
    month[8] = "September";
    month[9] = "October";
    month[10] = "November";
    month[11] = "December";

    return month[d.getMonth()];
}

function getYearsBetweenDates(fromDate,toDate) {
    var age = toDate.getFullYear() - fromDate.getFullYear();
    var m = toDate.getMonth() - fromDate.getMonth();
    if (m < 0 || (m === 0 && toDate.getDate() < fromDate.getDate())) {
        age--;
    }
    return age;
}

$(document).ready(function() {
    // First check for sql 
    if ($(".sql").length) {
        $("#menuRow").append('<div id="sql"><div id="sqlLabel">sql</div><div id="queries" style="display:none"></div></div>');
        $(".sql").each(function(){
            $(this).detach().appendTo('#queries');
        });
    }
    
    $("#sqlLabel").click(function(){
        $("#queries").toggle();
    });
    
    $("#notificationLinkDiv").click(function(){
        getNotifications();
        positionNotificationsDiv();
        $("#notificationsDiv").toggle();
    });
    
    $("#imgLogout").click(function(){
        window.location.href = '/dms/joomla.logout.php';
    });
    
    
    setFooterWidths();
    $("#srchRed, #srchBlue").click(function(){
        window.location.href = '/dms/contacts/find.contact.php';
    });
    
    $("#imgExport").click(function(){
        exportData();
    });
    
    $("#closeContactSearchDiv").bind('click',hideContactSearchDiv);
    
    $("#notificationLinkDiv").css({'background-color': notificationDivColor(),'border-color':notificationDivColor()});
    
    // config div
    $("#myConfigurationDiv").click(function(){
        window.location='/dms/myconfig.php';
    });
});


function notificationDivColor(){
    var uNotifications = $("#hasUserGotNotifications").val();
    return (uNotifications=='Y') ? '#CD3333':'#254B7C';
}

function getNotifications(){
    $("#notificationsDiv").empty().append('<img src="/dms/img/loading-white.gif" /> loading...');
    $.get("/dms/get.user.notifications.php",function(data){
        $("#notificationsDiv").empty().append(data);
        $("#xCloseNotifications").click(function(){
            $("#notificationsDiv").toggle();
        });
    });
}

function validateQuickSearch() {
    var q = $("#qck_search").val();
    if (q.length==0) {
        alert('Please insert something in the search box');
        $("#qck_search").focus();
        return false;
    }
    return true;
}

function setFooterWidths() {
    var fw = $("#footerRow").width();
    var ot = Math.floor(fw/3);
    $("#titleDiv").width(ot);
    var ps = ot - 20;
    $("#permanentSearchDiv").width(ps);
}

function htmlEscape(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}

function exportData() {
    var w = window.open('/dms/export.php','_blank');
    w.focus();
}

function hideContactSearchDiv() {
    $("#contactSearchResultdiv").hide();
}

function daysBetween(sDate,eDate) {
    var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
    var firstDate = new Date(sDate);
    var secondDate = new Date(eDate);
    var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
    return diffDays;
}

function checkForEmailDuplicates(eAddress,callback) {
    $.get('/dms/get.emailAddresses.php',{e: eAddress}).done(function(data){
        var result = true;
        var json = $.parseJSON(data);
        if (json.count>0) {
            var belongsTo = 'Are you sure you want to use: ' + eAddress + '? \n\nIt belongs to:\n';
            for (var i=0;i<json.count;i++) {
                belongsTo += json.values[i].display_name + '\n';
            }
            callback(confirm(belongsTo));
        } else {
            callback(true);
        }
        return result;
    }).fail(function(){
        callback(false);
    });
}