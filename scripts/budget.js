function validateFilter() {
    var r = document.getElementById("bud_region");
    var d = document.getElementById("bud_department");
    var s = document.getElementById("sel_category");
    var c = document.getElementById("bud_category");
    var t = document.getElementById("bud_amount");
    
        
    if (d.value.length==0) {
        alert("Please select a department");
        d.focus();
        return false;
    }
        
    if (r.value.length==0) {
        alert("Please select a region");
        r.focus();
        return false;
    }
    
    if (s.value.length==0) {
        alert("Please select a category");
        s.focus();
        return false;
    }
    var ct = s.value.split("-");
    if (ct[1]!='*'&&ct[1].indexOf(d.value)==-1) {
        alert('Category not valid for this department');
        s.focus();
        return false;
    }
    
    c.value =  ct[0];
    
    var strAmount = t.value;
    while(strAmount.indexOf(' ')>-1) strAmount = strAmount.replace(' ','');
    if (strAmount.length==0 || strAmount <= 0 || isNaN(strAmount)) {
        alert("Please enter your budgeted amount.");
        t.focus();
        return false;
    }
    
    t.value = strAmount;
    var btn = document.getElementById("btnInsertEdit");
    document.frmFilter.action = "budget."+btn.innerHTML.toString().toLowerCase()+".php";
    document.frmFilter.submit();
    
}

function doSearch() {
    var r = document.getElementById("bud_region");
    var d = document.getElementById("bud_department");
    var c = document.getElementById("sel_category");
    
    var selected_r = r.options[r.selectedIndex].text;
    var selected_d = d.options[d.selectedIndex].text;
    var selected_c = c.options[c.selectedIndex].text;
    
    var btn = document.getElementById("btnInsertEdit");
    var a = document.getElementById("bud_amount");
    var i = document.getElementById("bud_id");
    
    if (c.value.length==0 || d.value.length==0 || r.value.length==0) {
        btn.innerHTML = 'Insert';
        a.value = '';
        i.value = '';
    }
    
    var targetTable = document.getElementById('tblData');
    var targetTableColCount;
    var keepChecking = true;
    
    //Loop through table rows
    for (var rowIndex = 0; rowIndex < targetTable.rows.length; rowIndex++) {
        var rowData = '';

        //Get column count from header row
        if (rowIndex == 0) {
           targetTableColCount = targetTable.rows.item(rowIndex).cells.length;
           continue; //do not execute further code for header row.
        }
                
        //Process data rows. (rowIndex >= 1)
        for (var colIndex = 0; colIndex < targetTableColCount; colIndex++) {
            rowData += targetTable.rows.item(rowIndex).cells.item(colIndex).textContent;
        }
        
        targetTable.rows.item(rowIndex).style.display = 'table-row';
    
        if (r.value.length> 0 && rowData.indexOf(selected_r) == -1)
            targetTable.rows.item(rowIndex).style.display = 'none';
               
        if (d.value.length> 0 && rowData.indexOf(selected_d) == -1) 
            targetTable.rows.item(rowIndex).style.display = 'none';
        
        if (c.value.length> 0 && rowData.indexOf(selected_c) == -1)
            targetTable.rows.item(rowIndex).style.display = 'none';
             
        if (c.value.length>0 && d.value.length>0 && r.value.length>0 
                && targetTable.rows.item(rowIndex).style.display=='table-row') 
        {
            btn.innerHTML = 'Edit';
            a.value = targetTable.rows.item(rowIndex).cells.item(4).textContent;
            i.value = targetTable.rows.item(rowIndex).cells.item(0).textContent;
            keepChecking = false;
        }
        
        if (c.value.length>0 && d.value.length>0 && r.value.length>0 
                && targetTable.rows.item(rowIndex).style.display=='none' && keepChecking) {
            btn.innerHTML = 'Insert';
            a.value = '';
            i.value = '';
        }
    }
    
    createDonutCharts();
}

function setBudgetId(x){
    
    var r = document.getElementById("bud_region");
    var d = document.getElementById("bud_department");
    var c = document.getElementById("sel_category");
    var a = document.getElementById("bud_amount");
    var i = document.getElementById("bud_id");
    
    i.value = x.cells.item(0).textContent;
    for (var z=0; z<r.length; z++) if (r.options[z].text == x.cells.item(2).textContent) r.value = r.options[z].value;
    for (var z=0; z<d.length; z++) if (d.options[z].text == x.cells.item(1).textContent) d.value = d.options[z].value;
    for (var z=0; z<c.length; z++) if (c.options[z].text == x.cells.item(3).textContent) c.value = c.options[z].value;
    a.value = x.cells.item(4).textContent;
    
    var btn = document.getElementById("btnInsertEdit");
    btn.innerHTML = "Edit";
}

function deleteBudget(x) {
    var i = document.getElementById("bud_id");
    var tbl = document.getElementById("tblData");
    var tblRow = tbl.rows.item(x.parentNode.rowIndex);
    
    var strConfirm = "Are you sure you want to delete this budget Record: \n\tRegion - " + tblRow.cells.item(1).textContent;
    strConfirm += "\n\tDepartment - " + tblRow.cells.item(2).textContent;
    strConfirm += "\n\tCategory - " + tblRow.cells.item(3).textContent;
    strConfirm += "\n\tTarget - R " + tblRow.cells.item(4).textContent;
    var cfrm = confirm(strConfirm);
    if (cfrm) {
        i.value = tblRow.cells.item(0).textContent;
        document.frmFilter.action = 'budget.delete.php';
        document.frmFilter.submit();   
    }
}

function fillGraph() {
    $("#tDiv").dxPieChart({
        dataSource: depChartData,
        title: "By Department",
    	tooltip: {
    		enabled: true,
    		format:"millions",
    		percentPrecision: 2,
    		customizeText: function() { 
    			return this.argumentText + " - " + this.percentText;
    		}
    	},
        legend: {
    		horizontalAlignment: "middle",
    		verticalAlignment: "bottom",
    		margin: 0
    	},
        palette: cPalette,
    	series: [{
    		type: "doughnut",
    		argumentField: "dp",
    		label: {
    			visible: false,
    			format: "millions",
    			connector: {
    				visible: false
    			}
    		}
    	}]
    });
}
function editDisabled() {
    alert('We\'re sorry :( \n\nEditing has been disabled, please contact IT Helpdesk for more info.');
}