function makeStarLayout(value) {
    var filledStars = Math.round(value);
    var emptyStars = 5 - filledStars
    var string1 = "";
    var string2 = "";
    for (var i = 0; i < filledStars; i++) {
        string1 += "&#9733";
    }
    for (var i = 0; i < emptyStars; i++) {
        string2 += "&#9733";
    }
    return '<span class="filledrating">' + string1 + '</span><span class="emptyrating">' + string2 + '</span>';
}

function makeAllStarLayouts() {
    var elements = document.getElementsByClassName("ratingstars");
    for (var i = 0; i < elements.length; i++) {
        elements[i].innerHTML = makeStarLayout(parseFloat(elements[i].innerHTML));
    }
}

var map;
var currentPosition;
var goToLocation = false;

function showLocation(map, goToLocation = true) {
    this.map = map
    this.goToLocation = goToLocation;
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, logLocationError);
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
}

function flyToLocation() {
    if (currentPosition) {
        map.flyTo([currentPosition.coords.latitude, currentPosition.coords.longitude], 14);
    }
}

function showPosition(position) {
    currentPosition = position;
    if (this.goToLocation) {
        map.setView([position.coords.latitude, position.coords.longitude], 12);
        map.flyTo([currentPosition.coords.latitude, currentPosition.coords.longitude], 14);
    }
    var locationIcon = L.icon({
        iconUrl: 'img/location-icon.png',
        shadowUrl: 'img/marker-icon-shadow.png',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        shadowSize: [40, 40],
        shadowAnchor: [20, 20],
        popupAnchor: [0, 0]
    });
    L.marker([position.coords.latitude, position.coords.longitude], { icon: locationIcon }).addTo(map);
}

function logLocationError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            console.log("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            console.log("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            console.log("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            console.log("An unknown error occurred.");
            break;
    }
}