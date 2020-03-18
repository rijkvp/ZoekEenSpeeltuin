<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Using MySQL and PHP with Google Maps</title>
    <link rel="stylesheet" href="leaflet/leaflet.css" />
    <script src="leaflet/leaflet.js"></script>
    <style>
        * {
            font-family: sans-serif;
        }

        #mapid { height: 800px; width: 800px; }

        .leaflet-popup-content-wrapper {
            border-radius: 2px;
        }

        .leaflet-popup-content-wrapper .leaflet-popup-content {
        }

        .leaflet-popup-tip-container {
        }

        .leaflet-popup-close-button {
            
        }

        .popup {
            width: 300px;
            min-height: 60px;
        }
        .popup img {
            float: left;
        }

        .popup b {
            font-size: 1.2rem;
        }

        .popup b,
        .popup p{
            margin: 0px;
            margin-left: 70px;
            display: block;
        }

        .rating {
            font-size: 1.2rem;
            color: #c9ba10;
        }
    </style>
  </head>
  <body>
    <h1>Speeltuinen website prototype versie 0.0.1</h1>
    <div id="filters">
        <h2>Filters</h2>
        <b>Waardering</b>
        <br>
        <input type="range" step="0.1" min="0" max="5" value="4" class="slider" id="ratingSlider">
        <span id="ratingValue">5</span>
        <br>
        <b>Onderdelen:</b>
        <br>
        <input type="checkbox" name="schommel">
        <label for="schommel">Schommel</label>
        <br>
        <input type="checkbox" name="wipwap">
        <label for="wipwap">Wip-wap</label>
        <br>
        <input type="checkbox" name="glijbaan">
        <label for="glijbaan">Glijbaan</label>
        <br>
        <b>Leeftijd:</b>
        <br>
        <select id="leertijdscategorie">
            <option value="1-5">1-3 jaar</option>
            <option value="3-5">3-5 jaar</option>
            <option value="5-8">5-8 jaar</option>
        </select>
    </div>
    <div id="mapid"></div>
    <script>
        var map = L.map('mapid').setView([52.43, 5.42], 8);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var popup = L.popup();

        function onMapClick(e) {
            popup.setLatLng(e.latlng)
                .setContent("You clicked the map at " + e.latlng.toString())
                .openOn(map);
        }

        map.on('click', onMapClick);

        L.marker([52.472011, 6.124878]).addTo(map)
            .bindPopup('Hier is ook een leuke!')
            .openPopup();

            L.marker([53.211134, 6.547422]).addTo(map)
            .bindPopup('Dit is de beste speeltuin!')
            .openPopup();
            
            L.marker([51.960452, 5.927124]).addTo(map)
            .bindPopup('Hier is een leuke speeltuin!')
            .openPopup();
        // specify popup options 
        var customOptions =
        {
            'height': '400',
            'maxWidth': '800',
            'width': '500',
        }
        var popup2 = L.popup(customOptions)
        .setContent('<div class="popup"><img src="https://picsum.photos/60/60"><b>Leuke Speeltuin</b><p>Onderdelen: Wip-wap, Schommel</p><p>Leeftijd: 5+</p><p> 3.4 <span class="rating">&#9733;&#9733;&#9733;&#9734;&#9734;</span></p></div>');
        L.marker([52.352119, 4.801025]).addTo(map).bindPopup(popup2).openPopup();
        
    </script>
    <script>
    var slider = document.getElementById("ratingSlider");
    var output = document.getElementById("ratingValue");
    output.innerHTML = slider.value; // Display the default slider value

    slider.oninput = function() {
        output.innerHTML = this.value;
    }

    </script>
    </body>
</html>