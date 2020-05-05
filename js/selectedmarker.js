function setupMap(lat, lng) {
    var customIcon = L.icon({
        iconUrl: 'img/marker-icon.png',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, 0]
    });

    var map = L.map('smallmap').setView([lat, lng], 16);

    var tileLayer = L.tileLayer('https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
    });
    map.addLayer(tileLayer);

    L.marker([lat, lng], { icon: customIcon }).addTo(map);

    showLocationMarker(map)
}