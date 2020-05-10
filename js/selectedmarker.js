function setupMap(lat, lng) {
    var customIcon = L.icon({
        iconUrl: 'img/marker-icon.png',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        popupAnchor: [0, 0]
    });

    var map = L.map('smallmap').setView([lat, lng], 16);
    var tileLayer = L.tileLayer('https://geodata.nationaalgeoregister.nl/tiles/service/wmts/brtachtergrondkaart/EPSG:3857/{z}/{x}/{y}.png', {
        minZoom: 8,
        maxZoom: 19,
        bounds: [
            [50.5, 3.25],
            [54, 7.6]
        ],
        attribution: 'Kaartgegevens &copy; <a href="kadaster.nl">Kadaster</a>'
    });
    map.addLayer(tileLayer);

    L.marker([lat, lng], { icon: customIcon }).addTo(map);

    showLocation(map)
}