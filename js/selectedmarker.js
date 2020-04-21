function setupMap(lat, lng) {
    console.log(lat + " " + lng);
    var customIcon = L.icon({
        iconUrl: 'img/marker-icon.png',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, 0]
    });

    var map = L.map('smallmap').setView([lat, lng], 16);

    var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });
    map.addLayer(tileLayer);

    L.marker([lat, lng], { icon: customIcon }).addTo(map);
}