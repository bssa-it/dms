function initialize() {
    createMap();
    geocoder = new google.maps.Geocoder();
    
}

function createMap() {
    var mapOptions = {
      /*center: new google.maps.LatLng(-33.9253,18.4239),    /* <-  Cape Town */
      center: new google.maps.LatLng(-28.620552361393116,24.91210937499999),
      zoom: 6
    };
    map = new google.maps.Map(document.getElementById("map-canvas"),mapOptions);
    google.maps.event.addListener(map, 'click', function(e) {
        document.getElementById('divLat').innerHTML = 'lat: ' + e.latLng.lat().toFixed(6);
        document.getElementById('divLon').innerHTML = 'lon: ' + e.latLng.lng().toFixed(6);
      });
}
      
function codeAddress(addrValue,lblText,trow) {
  var address = addrValue;
  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      var lat = results[0].geometry.location.lat().toFixed(4);
      var lon = results[0].geometry.location.lng().toFixed(4);
      
      if (lon<15||lon>34) {
        alert(trow.cells.item(1).textContent + ', postal code: ' + trow.cells.item(4).textContent + ' not found in South Africa');
        trow.cells.item(0).firstChild.checked = false;
        return;
      }
      
      map.setCenter(results[0].geometry.location);
      map.setZoom(12);
      var marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location
      });
      markers.push(marker);
      addInfoWindow(marker, lblText);
      
      var pcode = trow.cells.item(4).textContent;
      loadLatLng(pcode,lat,lon);
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
      trow.cells.item(0).firstChild.checked = false;
    }
  });
}

function loadLatLng(pcode,lat,lng) {
    var targetTable = document.getElementById('tblPostalCodes');
    for (var rowIndex = 0; rowIndex < targetTable.rows.length; rowIndex++) {
        var row = targetTable.rows.item(rowIndex);
        if (row.cells.item(4).textContent==pcode) {
            row.cells.item(0).firstChild.checked = true;
            row.cells.item(5).firstChild.value = lat;
            row.cells.item(6).firstChild.value = lng;   
        }
    }  
}

function markMap(row,isPostalCode) {
   clearMarkers();
   if (row.cells.item(0).firstChild.checked&&row.cells.item(5).firstChild.value.length==0) {
        var lblText = row.cells.item(1).textContent + '<br />' + row.cells.item(2).textContent + '<br />' + row.cells.item(4).textContent;
        var col = (isPostalCode) ? 4:2;
        var searchAddress = row.cells.item(1).textContent + ', ' + row.cells.item(col).textContent; 
        codeAddress(searchAddress,lblText,row);
   }
}

function clearMarkers() {
  if (markers.length==0) return;
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(null);
  }
}

function addInfoWindow(marker, message) {
    var info = message;
    var infoWindow = new google.maps.InfoWindow({
        content: message
    });
    google.maps.event.addListener(marker, 'click', function () {
        infoWindow.open(map, marker);
    });
}

function reInitialize() {
    clearMarkers();
    createMap();
}

function showAll(el) {
    var cb = document.getElementsByName('ids[]');
    for (var i=1;i<cb.length;i++) cb[i].checked = el.checked;
    
    if (el.checked) {
        var targetTable = document.getElementById('tblPostalCodes');
        for (var rowIndex = 0; rowIndex < targetTable.rows.length; rowIndex++) {
            var row = targetTable.rows.item(rowIndex);
            setTimeout(function() {markMap(row,true)},111);
        }   
    }
}

function findMe(row) {
    if (row.cells.item(0).firstChild.checked) {
        markMap(row,true);   
    } else {
        row.cells.item(5).firstChild.value = '';
        row.cells.item(6).firstChild.value = '';
    }
}

function findArea(row) {
    row.cells.item(0).firstChild.checked = true;
    markMap(row,false);
}

function save() {
    document.frmGeocodes.submit();
}

function checkBox(row) {
    if (!row.cells.item(0).firstChild.checked) {
        if (row.cells.item(5).firstChild.value.length>0
            &&row.cells.item(6).firstChild.value.length>0) 
                row.cells.item(0).firstChild.checked = true; 
    }
}