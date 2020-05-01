<?php
    include 'includes/dbh.inc.php';
?>
<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Zoek een Speeltuin - Kaart</title>
    <!-- Leaflet -->
    <link rel="stylesheet" type="text/css" href="libs/leaflet/leaflet.css" />
    <script src="libs/leaflet/leaflet.js"></script>
    <!-- Library for custom sliders -->
    <link href="libs/noUiSlider/nouislider.min.css" rel="stylesheet">
    <script src="libs/noUiSlider/nouislider.min.js"></script>
    <!-- Own CSS Stylesheet -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!-- Own JS -->
    <script src="js/util.js"></script>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  </head>
  <body>
    <header>
        <nav>
            <?php include "navigation.php"; navigation("home"); ?>
        </nav>
    </header>
    <section id="main">
        <button id="filtersCollapse" class="btn smallbtn">Filters</button>
        <div id="filterspanel">
            <div id="filters">
            <h2>Filters</h2>
                <div class="filterdiv">
                    <h3>Beoordeling</h3>
                    <div id="ratingSlider"></div>
                    <span id="ratingSliderValue">1.0</span>
                </div>
                <div class="filterdiv">
                    <h3>Min. Onderdelen</h3>
                    <div id="minPartsSlider"></div>
                    <span id="minPartsSliderValue">0</span>
                </div>
                <div class="filterdiv">
                    <h3>Leeftijd / Uitdaging</h3>
                    <div id="ageSlider"></div>
                    <div id="ageSliderValue"></div>
                </div>
                <div class="filterdiv">
                    <h3>Voorzieningen</h3>
                    <div class="checkbox">
                        <input type="checkbox" name="alwaysOpen" onclick="changeAlwaysOpen(this)">
                        <label for="alwaysOpen">Altijd geopend</label>
                    </div>
                    <br>
                    <div class="checkbox">
                        <input type="checkbox" name="cateringAvailable" onclick="changeCateringAvailable(this)">
                        <label for="cateringAvailable">Horeca aanwezig</label>
                    </div>
                </div>
                <div class="filterdiv">
                    <h3>Onderdelen</h3>
                    <?php
                        include 'includes/parts.inc.php';
                        echo '<table>';
                        foreach ($parts as $part)
                        {
                            $inputname = "part".$part[0];
                            echo'<tr>
                                    <td>
                                        <label for="'.$inputname.'">'.$part[1].'</label>
                                    </td>
                                    <td>
                                        <div class="checkbox">
                                            <input type="checkbox" onclick="changePartFilter(this)" name="'.$inputname.'">
                                            <label></label>
                                        </div>
                                    </td>
                                </tr>
                                ';
                        }
                        echo '</table>';
                    ?>
                </div>  
            </div>
            <script>
                function applyFilters()
                {
                    var requiredParts = "";
                    for (var partId in partFilters) {
                        if (partFilters[partId])
                        {
                            requiredParts += partId.substring(4) + "%";
                        }
                    }
                    requestFilteredPlaygroundData(minRatingFilter, minPartsFilter, minAgeFilter, maxAgeFilter, alwaysOpenFilter, cateringAvailableFilter, requiredParts);
                }

                var minRatingFilter = 1;
                var minPartsFilter = 0;
                var minAgeFilter = 0;
                var maxAgeFilter = 18;
                var alwaysOpenFilter = false;
                var cateringAvailableFilter = false;
                var partFilters = {};

                function changeAlwaysOpen(value) {
                    alwaysOpenFilter = value.checked;
                    applyFilters();
                }
                function changeCateringAvailable(value) {
                    cateringAvailableFilter = value.checked;
                    applyFilters();
                }

                function changePartFilter(value)
                {
                    partFilters[value.name] = value.checked;
                    applyFilters();
                }

                var ratingSlider = document.getElementById('ratingSlider');
                noUiSlider.create(ratingSlider, {
                    start: 1,
                    step: 0.1,
                    connect: 'lower',
                    range: {
                        'min': 1,
                        'max': 5,
                    }
                });
                ratingSlider.noUiSlider.on('change', applyFilters);
                ratingSlider.noUiSlider.on('update', function(value) {
                    minRatingFilter = value;
                    document.getElementById('ratingSliderValue').innerHTML = "Minimaal " + parseFloat(value).toFixed(1);
                });

                var minPartsSlider = document.getElementById('minPartsSlider');
                noUiSlider.create(minPartsSlider, {
                    start: 0,
                    step: 1,
                    connect: 'lower',
                    range: {
                        'min': 0,
                        'max': 20,
                    }
                });
                minPartsSlider.noUiSlider.on('change', applyFilters);
                minPartsSlider.noUiSlider.on('update', function(value) {
                    minPartsFilter = value;
                    document.getElementById('minPartsSliderValue').innerHTML = "Minimaal " + Math.round(value);
                });

                var ageSlider = document.getElementById('ageSlider');

                noUiSlider.create(ageSlider, {
                    start: [0, 18],
                    connect: true,
                    step: 1,
                    range: {
                        'min': 0,
                        'max': 18,
                    }
                });
                ageSlider.noUiSlider.on('change', applyFilters);
                ageSlider.noUiSlider.on('update', function(value) {
                    minAgeFilter = Math.round(value[0]);
                    maxAgeFilter = Math.round(value[1]);
                    document.getElementById('ageSliderValue').innerHTML = minAgeFilter + " t/m " + maxAgeFilter + " jaar";
                });
            </script>
        </div>
        <script>
            var coll = document.getElementById("filtersCollapse");
            coll.addEventListener("click", function() {
                this.classList.toggle("active");
                var content = document.getElementById("filterspanel");
                if (content.style.maxHeight){
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                } 
            });
        </script>
        <div id="map">
        </div>
    </section>
    <script>
        function requestPlaygroundData()
        {
            // Get all of the playgrounds from the database with a GET request
            var http = new XMLHttpRequest();
            http.open("GET", "includes/get_playgrounds.inc.php", true);
            http.send();
            http.onload = () => displayMarkers(http.responseText);
        }

        function requestFilteredPlaygroundData(minRating, minParts, minAge, maxAge, alwaysOpen, cateringAvailable, requiredParts)
        {
            var http = new XMLHttpRequest();
            http.open("GET", "includes/get_playgrounds.inc.php?minRating="+minRating+"&minParts="+minParts+"&minAge="+minAge+"&maxAge="+maxAge + "&alwaysOpen="+alwaysOpen+"&cateringAvailable="+cateringAvailable+"&requiredParts="+requiredParts, true);
            http.send();
            http.onload = () => displayMarkers(http.responseText);
        }

        requestPlaygroundData();

        var current_lat;
        var current_lng;
        var map = L.map('map').setView([52.43, 5.42], 8);
        var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        });
        map.addLayer(tileLayer);
        var layerGroup = L.layerGroup().addTo(map);
        var customOptions =
        {
            'maxWidth': '240',
            'width': '240',
            'className' : 'popup'
        }
        var popup = L.popup(customOptions);

        var customIcon = L.icon({
            iconUrl: 'img/marker-icon.png',
            shadowUrl: 'img/marker-icon-shadow.png',
            iconSize:     [32, 32],
            iconAnchor:   [16, 16],
            shadowSize:   [40, 40],
            shadowAnchor: [20, 20],
            popupAnchor:  [0, 0]
        });

        function openAddPlaygroundPopup(e) {
            
            popup.setLatLng(e.latlng)
                .setContent('<b class="coordlabel">' + e.latlng.lat.toFixed(4) + ", " + e.latlng.lng.toFixed(4)
                 + "</b><button class='btn smallbtn' onClick='addPlayground(" + e.latlng.lat + ", " + e.latlng.lng + ")'>Speeltuin toevoegen</button>")
                .openOn(map);
        }

        function addPlayground(lat, lng) {
            window.location.replace("add_playground.php?lat="+lat+"&lng="+lng);
        }

        function displayMarkers(data)
        {
            // Decode the JSON
            layerGroup.clearLayers();
            try
            {
                var playgrounds = JSON.parse(data);
            }
            catch
            {
                return;
            }
            for (var i = 0; i < playgrounds.length; i++)
            {
                var customOptions =
                {
                    'height': '400',
                    'maxWidth': '800',
                    'width': '500',
                    'className' : 'popup'
                }
                var imageSrc = "";
                var hasImage = false;
                if (playgrounds[i][9] != null)
                {
                    hasImage = true;
                    imageSrc = playgrounds[i][9];
                }
                if (hasImage)
                {
                    var customPopup = L.popup(customOptions)
                    .setContent('<div class="popupdiv popupdivimg"><img class="playgroundIcon" src="' + imageSrc + '"><h3><a class="headinglink" href="playground.php?id=' + playgrounds[i][0] + '">' + playgrounds[i][1] +
                    '</a></h3><p><strong>Onderdelen:</strong> ' + playgrounds[i][6] +'</p><p><strong>Leeftijd:</strong> '
                    + playgrounds[i][4] + " t/m " + playgrounds[i][5] +' jaar</p><p><span class="ratinglabelsmall">'
                    + parseFloat(playgrounds[i][7]).toFixed(1) + '</span> <span class="ratingstars ratingsmall">' + makeStarLayout(parseFloat(playgrounds[i][7])) + '</span> ('
                    + playgrounds[i][8] 
                    + ' reviews) </p><p><a class="btn extrasmallbtn" href="playground.php?id=' + playgrounds[i][0] + '">Meer Info</a></p></div>');
                } else {
                    var customPopup = L.popup(customOptions)
                    .setContent('<div class="popupdiv"><h3><a class="headinglink" href="playground.php?id=' + playgrounds[i][0] + '">' + playgrounds[i][1] +
                    '</a></h3><p><strong>Onderdelen:</strong> ' + playgrounds[i][6] +'</p><p><strong>Leeftijd:</strong> '
                     + playgrounds[i][4] + " t/m " + playgrounds[i][5] +' jaar</p><p><span class="ratinglabelsmall">'
                    + parseFloat(playgrounds[i][7]).toFixed(1) + '</span> <span class="ratingstars ratingsmall">' + makeStarLayout(parseFloat(playgrounds[i][7])) + '</span> ('
                     + playgrounds[i][8] 
                     + ' reviews) </p><p><a class="btn extrasmallbtn" href="playground.php?id=' + playgrounds[i][0] + '">Meer Info</a></p></div>');
                }
                
                
                L.marker([playgrounds[i][2], playgrounds[i][3]], {icon: customIcon}).addTo(layerGroup)
                .bindPopup(customPopup);
            }
        }
        map.on('click', openAddPlaygroundPopup);        
    </script>
    </body>
</html>