/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $("#btnAfrikaans").click(function(){
        window.location="operation.personalInfo.php?language=A";
    });
    $("#btnEnglish").click(function(){
        window.location="operation.personalInfo.php?language=E";
    });
    $("#vidLink,#backLink").bind("click",showHideVideo);
    $("#depLink").click(function(){
       window.location="myconfig.php";
    });
});
function showHideVideo() {
    $("#contentDiv").toggle();
    $("#videoDiv").toggle();
}