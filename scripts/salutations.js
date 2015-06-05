/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $("#btnSave").click(function(){
        if ($("#sal_department_id").val().length==0) {
            alert('Please select the department.');
            $("#sal_department_id").focus();
            return;
        }
       
        if ($("#sal_denomination_id").val().length==0) {
            alert('Please select the denomination.');
            $("#sal_denomination_id").focus();
            return;
        }
        
        if ($("#sal_category_id").val().length==0) {
            alert('Please select the category.');
            $("#sal_category_id").focus();
            return;
        }
        
        if ($("#sal_type").val().length==0) {
            alert('Please select the type.');
            $("#sal_type").focus();
            return;
        }
        
        if ($("#sal_language").val().length==0) {
            alert('Please select the language.');
            $("#sal_language").focus();
            return;
        }
        
        if ($("#sal_text").val().length==0) {
            alert('Please enter the text.');
            $("#sal_text").focus();
            return;
        }
        document.frmSalutations.action = "save.salutations.php";
        document.frmSalutations.submit();
    });
    $(".salut").click(function(){
        $("#divNewHeading").empty().append('Edit');
        $("#sal_id").val($(this).attr('salId'));
        $("#sal_department_id").val($(this).find("td:eq(0)").text());
        $("#sal_denomination_id").val($(this).find("td:eq(1)").text());
        $("#sal_category_id").val($(this).find("td:eq(2)").text());
        $("#sal_type").val($(this).find("td:eq(3)").text());
        $("#sal_language").val($(this).find("td:eq(4)").text());
        $("#sal_text").val($(this).find("td:eq(5)").text());
        $("#btnCancel").show();
        $("#btnDelete").show();
    });
    $("#btnCancel").click(function(){
        $("#divNewHeading").empty().append('Add');
        $("#sal_id").val('');
        $("#sal_department_id").val('');
        $("#sal_denomination_id").val('');
        $("#sal_category_id").val('');
        $("#sal_type").val('');
        $("#sal_language").val('');
        $("#sal_text").val('');
        $("#btnDelete").hide();
        $(this).hide();
    });
    $("#btnDelete").click(function(){
        $("#divNewHeading").empty().append('You about to delete this rule');
        var cfm = confirm('Are you sure you want to delete this rule?');
        if (cfm) {
            document.frmSalutations.action = "delete.salutations.php";
            document.frmSalutations.submit();
        }
    });
});