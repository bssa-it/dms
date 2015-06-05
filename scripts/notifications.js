$(".notificationStatusChkBox").click(function(){
    if ($(this).prop("checked")) {
        var activityId = $(this).val();
        $("#notificationsDiv").empty().append('<img src="/dms/img/loading-white.gif" /> saving...');
        $.post( 
           "/dms/contacts/save.contact.activityStatus.php",
           {a: activityId,quickClose:"y"}  
        ).done(function () { 
           getNotifications();
        });
    }
});
$('.divNotification').click(function(){
    if ($(this).attr('a')=='birthday') {
        $("#frmBirthday").submit();
    }
});