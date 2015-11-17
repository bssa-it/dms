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
$(".nextDateDiv").click(function(){
    window.location='/dms/contacts/load.contact.php?d='+$(this).attr('d')+'&s=civicrm&a=' + $(this).attr('a');
});