var geocoder;
var map;
var markers = [];
var icon = "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
var byDepartment = [];
var byLocation = [];
var byValue = [];
function initialize() {
    var mapOptions = {
        /*center: new google.maps.LatLng(-33.9253,18.4239),    /* <-  Cape Town */
        center: new google.maps.LatLng(-28.620552361393116,24.91210937499999),
        zoom: 6
    };
    map = new google.maps.Map(document.getElementById("map-canvas"),mapOptions);
    geocoder = new google.maps.Geocoder();
}
google.maps.event.addDomListener(window, 'load', initialize);


function showLocations() {
    for (var i=0;i<byLocation.length;i++) {
        insertMarker(byLocation[i].lat,byLocation[i].lon)
    }
}


/*
function markMap(wot) {
    clearMarkers();
    switch (wot) {
        case 'byLocation':
            for (var i=0;i<byLocation.length;i++) {
                setInterval(insertMarker(byLocation[i].lat,byLocation[i].lon),5000);
            }
            break;
        case 'byDepartment':
            for (var i=0;i<byDepartment.length;i++) {
                insertMarker(byDepartment[i].lat,byDepartment[i].lon);
            }
            break;
        case 'byValue':
            for (var i=0;i<byValue.length;i++) {
                insertMarker(byValue[i].lat,byValue[i].lon);
            }
            break;
    }
}
*/
function clearMarkers() {
  if (markers.length==0) return;
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(null);
  }
}

function insertMarker(lat,lon) {
  var myLatlng = new google.maps.LatLng(lon,lat);
  var marker = new google.maps.Marker({
      map: map,
      icon: new google.maps.MarkerImage(icon),
      position: myLatlng
  });
  markers.push(marker);
}