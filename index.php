<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Speeltuinen Website</title>
    <!-- Leaflet -->
    <link rel="stylesheet" type="text/css" href="leaflet/leaflet.css" />
    <script src="leaflet/leaflet.js"></script>
    <!-- Own CSS Stylesheet -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
  </head>
  <body>
    <header>
    <nav>
        <ul>
            <li class="active" id="logo"><a href="index.php">Speeltuinen</a></li>
            <li class="active"><a href="index.php">Home</a></li>
            <li><a href="add_playground.php">Toevoegen</a></li>
        </ul>
        </nav>
    </header>
    <section id="main">
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
        <div id="map">
        </div>
    </section>
   
    <?php
        include 'includes/playgrounds.inc.php';
    ?>
    <script>
        // Get all of the playgrounds from the database with a GET request
        var http = new XMLHttpRequest();
        http.open("GET", "includes/get_playgrounds.inc.php", true);
        http.send();
        http.onload = () => displayMarkers(http.responseText);

        var current_lat;
        var current_lng;
        var map = L.map('map').setView([52.43, 5.42], 8);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var popup = L.popup();

        function openAddPlaygroundPopup(e) {
            popup.setLatLng(e.latlng)
                .setContent(e.latlng.lat.toFixed(3) + " " + e.latlng.lng.toFixed(3) + "<button onClick='addPlayground(" + e.latlng.lat + ", " + e.latlng.lng + ")'>Voeg speeltuin toe </button>")
                .openOn(map);
        }

        function addPlayground(lat, lng) {
            console.log("Add playground " + lat + " " + lng);
            window.location.replace("add_playground.php?lat="+lat+"&lng="+lng);
        }

        function displayMarkers(data)
        {
            // Decode the JSON
            var playgrounds = JSON.parse(data);
            for (var i = 0; i < playgrounds.length; i++)
            {
                L.marker([playgrounds[i][2], playgrounds[i][3]]).addTo(map)
                .bindPopup(playgrounds[i][1])
                .openPopup();
            }
        }

        map.on('click', openAddPlaygroundPopup);

        // Styled popup example
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