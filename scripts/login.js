/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var filelist = [];
var fileIdx = 0;
var timer;
$(document).ready(function() {
    $("#contentDiv").css('height',$(window).height()*.75);
    $("#contentDiv").css('margin-top',$(window).height()*.1);
    $("#contentDiv").outerWidth($(window).width()*.8);
    $("#resultDiv").hide();
    $("#loadingDiv").hide();
    $("input").keypress(function(event){
        var code = (event.keyCode ? event.keyCode : event.which);
        if(code == 13) $("#btnLogin").click();
    });
    $("#currentAnnouncementDiv").empty().append('<img src="img/loading-4.gif" align="center" /> loading ...');
    $.get('get.announcements.php').done(function(data){
       filelist = $.parseJSON(data);
       if (filelist.files.length>0) {
           getNextFile(fileIdx);
           fileIdx++;
       }
       announcementLoop();
    });
    
    $("#btnLogin").click(function(){
        if ($("#username").val().length==0) {
            alert('Please insert your username');
            $("#username").focus();
            return;
        }
        if ($("#password").val().length==0) {
            alert('Please insert your password');
            $("#password").focus();
            return;
        }
        var u = $("#username").val();
        var p = $("#password").val();
        $.post('joomla.login.php',{username: u,password: p}).done(function(data){
            $("#loadingDiv").show();
            if (isNaN(data)) {
                $("#resultDiv").empty().append(data);
                $("#username").val('');
                $("#password").val('');
                $("#loadingDiv").hide();
                $("#resultDiv").show();
                $("#username").focus();
            } else {
                window.location='/';
            }
        });
    });
    $("#timeDiv").countdowntimer({
        currentTime: true,
        size: "lg"
    });
    $("#nextBtn").click(function(){
        if (fileIdx==filelist.files.length) fileIdx = 0;
        getNextFile(fileIdx);
        fileIdx++;
        announcementLoop();
    });
    $("#prevBtn").click(function(){
        fileIdx -= 2;
        if (fileIdx<0) fileIdx = 0;
        getNextFile(fileIdx);
        fileIdx++;
        announcementLoop();
    });
    
});
function getCurrentIdx() {
    var h = $("#announcementHeadingDiv").text();
    for (var i=0;i<filelist.files.length;i++) {
        var f = filelist.files[i].fname;
        if (f==h) return i;
    }
}

function getNextFile(idx) {
    var h = filelist.files[idx].fname;
    var f = filelist.files[idx].path;
    $.get(f).done(function(data){
        $("#announcementHeadingDiv").empty().append('<img src="img/announcement.png" width="24" align="center" style="margin-right: 5px;float: left;" />' + h);
        $("#currentAnnouncementDiv").empty().append(data);
    });
    if (filelist.files.length>0) {
        $("#nextBtn,#prevBtn").show();
        if (idx==0) {
            $("#prevBtn").hide();
        }
        var lastA = filelist.files.length-1;
        if (idx==lastA) {
            $("#nextBtn").hide();
        }
    } else {
        $("#nextBtn,#prevBtn").hide();
    }
}
function announcementLoop() {
    clearInterval(timer);
    if (filelist.files.length>0) {
        timer = setInterval(function(){
            refreshFilelist();
            if (fileIdx==filelist.files.length) fileIdx = 0;
            getNextFile(fileIdx); 
            fileIdx++;
        }, 30000);
    } else {
        $("#announcementHeadingDiv").empty().append(filelist.message);
        $("#currentAnnouncementDiv").empty();
    }
}
function refreshFilelist() {
    $.get('get.announcements.php').done(function(data){
       filelist = $.parseJSON(data);
    });
}