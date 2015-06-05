function get_random_color() {
    var letters = '0123456789ABCDEF'.split('');
    var colorOk = false;
    while (colorOk==false){
        var color = '#';
        for (var i = 0; i < 6; i++ ) {
            color += letters[Math.round(Math.random() * 15)];
        }
        colorOk = isRandomColorOk(color);    
    }
    return color;
}

function isRandomColorOk(hexcolor){
    var r = parseInt(hexcolor.substr(0,2),16);
    var g = parseInt(hexcolor.substr(2,2),16);
    var b = parseInt(hexcolor.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    return (yiq >= 128) ? false : true;
}

function createDonutCharts(){
    /*var canvasDepartment = document.getElementById("departmentChart");
    canvasDepartment.width = 200;
    canvasDepartment.height = 200;
    var ctxDepartment = canvasDepartment.getContext("2d");
    var dataDepartment = getData(1,false);
    new Chart(ctxDepartment).Doughnut(dataDepartment);
    createLegend(dataDepartment,'departmentLegendDiv');
    
    var canvasCategory = document.getElementById("categoryChart");
    canvasCategory.width = 200;
    canvasCategory.height = 200;
    var ctxCategory = canvasCategory.getContext("2d");
    var dataCategory = getData(3,true);
    new Chart(ctxCategory).Doughnut(dataCategory);
    createLegend(dataCategory,'categoryLegendDiv');
    */
    setChartData();
    fillGraph();
    setBudgetTotal();
    setAllocationRemainder();
}

function getData(colIndx,useRandomColor) {
    
    var targetTable = document.getElementById('tblData');
    var data = [];
    var summ = [];
    
    for (var rowIndex = 1; rowIndex < targetTable.rows.length; rowIndex++) {
        var row = targetTable.rows.item(rowIndex);
        if (row.style.display == 'none') continue;
        
        var k = row.cells.item(colIndx).textContent;
        var v = row.cells.item(4).textContent;
        var c = row.cells.item(6).textContent;
        if (useRandomColor) c = get_random_color();  
        while (v.indexOf(' ')>-1) v = v.replace(' ','');
        var objPos = lookup(summ,k); 
        if (objPos==-1) {
            summ.push({
                value: parseFloat(v),
                color: c,
                desc: k
            })
        } else {
            summ[objPos]['value'] += parseFloat(v);
        }
    }
    return summ;
}

function setChartData() {
    
    var targetTable = document.getElementById('tblData');
    var data = [];
    var summ = [];
    var colors = [];
    
    for (var rowIndex = 1; rowIndex < targetTable.rows.length; rowIndex++) {
        var row = targetTable.rows.item(rowIndex);
        if (row.style.display == 'none') continue;
        
        var k = row.cells.item(1).textContent.substr(0,1);
        var v = row.cells.item(4).textContent;
        while (v.indexOf(' ')>-1) v = v.replace(' ','');
        var objPos = lookup(summ,k); 
        if (objPos==-1) {
            summ.push({
                val: parseFloat(v),
                dp: k
            });
            colors.push(row.cells.item(6).textContent);
        } else {
            summ[objPos]['val'] += parseFloat(v);
        }
    }
    depChartData = summ;
    cPalette = colors;
}

function lookup( arr,name ) {
    for(var i = 0, len = arr.length; i < len; i++) {
        if( arr[ i ].dp === name )
            return i;
    }
    return -1;
}

function createLegend(arr,divId) {
    var dataArr = sortByKey(arr,'desc');
    var str = '<table class="tblLegend" cellspacing="0" cellpadding="5" width="100% align="center">'; 
    str += '<tr style="font-weight: bold;color: #fff;background-color: #254B7C;cursor: auto;"><td colspan="2">Legend</td></tr>';
    
    var tot = 0;
    for ( var i=0; i< dataArr.length; i++) tot += parseFloat(dataArr[i].value);
    for ( var i=0; i< dataArr.length; i++){
        var per = dataArr[i].value/tot*100;
        var dsc = dataArr[i].desc.split(' - ');
        str += '<tr style="background-color: '+ dataArr[i].color + ';cursor: auto;"';
        str += '><td>' + dsc[1];
        str += '</td><td style="text-align: right;">' + per.toFixed(2) + ' %</td></tr>';
    }
    str += '</table>';
    
    var d = document.getElementById(divId);
    d.innerHTML = str;
}

function sortByKey(array, key) {
    return array.sort(function(a, b) {
        var x = a[key]; var y = b[key];
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    });
}

function setBudgetTotal() {
    var str = 'FILTERED TOTAL: R ';
    var tot = 0;
    
    var targetTable = document.getElementById('tblData');
    for (var rowIndex = 1; rowIndex < targetTable.rows.length; rowIndex++) {
        var row = targetTable.rows.item(rowIndex);
        if (row.style.display == 'none') continue;
        
        var v = row.cells.item(4).textContent;
        while (v.indexOf(' ')>-1) v = v.replace(' ','');
        tot += parseFloat(v);
    }
    var d = document.getElementById('budTotalDiv');
    d.innerHTML = str + tot.toLocaleString();
}

function setAllocationRemainder() {
    var currentDepartment = document.getElementById('bud_department');
    var str1 = 'ALLOCATED AMOUNT: R ';
    var str2 = ' | REMAINING AMOUNT: R ';
    var totalCaptured = 0;
    var totalAllocated = 0;
    
    for (var idx = 0; idx < departments.length; idx++) {
        if (currentDepartment.value == departments[idx].depId||currentDepartment.value =='') {
            totalAllocated += parseFloat(departments[idx].value);   
        }
    }
    str1 += totalAllocated.toLocaleString();
    
    var selected_dp = currentDepartment.options[currentDepartment.selectedIndex].text;
    var targetTable = document.getElementById('tblData');
    for (var rowIndex = 1; rowIndex < targetTable.rows.length; rowIndex++) {
        var row = targetTable.rows.item(rowIndex);
        var dp = row.cells.item(1).textContent;
        if (selected_dp==dp||currentDepartment.value =='') {
            var v = row.cells.item(4).textContent;
            while (v.indexOf(' ')>-1) v = v.replace(' ','');
            totalCaptured += parseFloat(v);
        }
    }
    var totalRemaining = parseFloat(totalAllocated) - parseFloat(totalCaptured);
    str2 += totalRemaining.toLocaleString();
    
    var d = document.getElementById('allocRemainderDiv');
    d.innerHTML = str1 + str2;
}