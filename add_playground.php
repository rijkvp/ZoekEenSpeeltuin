<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Zoek een Speeltuin - Toevoegen</title>
    <!-- Own CSS Stylesheet -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">
    <!-- Leaflet -->
    <link rel="stylesheet" type="text/css" href="libs/leaflet/leaflet.css" />
    <script src="libs/leaflet/leaflet.js"></script>
    <!-- Own JS -->
    <script src="js/util.js"></script>
    <!-- AdSense -->
    <script data-ad-client="ca-pub-0210402010508195" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>

<body>
    <header>
        <nav>
            <?php include "navigation.php";
            navigation("add_playground"); ?>
        </nav>
    </header>
    <div id="content">
        <form action="includes/add_playground.inc.php" method="post" enctype="multipart/form-data">
            <?php
            if (isset($_GET['error'])) {
                $type = $_GET['error'];
                $msg = 'Onbekende error!';
                switch ($type) {
                    case 'name':
                        $msg = 'Voer een naam in van 4 tot 30 tekens!';
                        break;
                    case 'lat':
                        $msg = 'Voer een geldige breedtegraad in!';
                        break;
                    case 'lng':
                        $msg = 'Voer een geldige lengtegraad in!';
                        break;
                    case 'nickname':
                        $msg = 'Voer een gebruikersnaam in van 4 tot 20 tekens!';
                        break;
                    case 'age':
                        $msg = 'Voer een geldige leeftijd in!';
                        break;
                    case 'picturefile':
                        $msg = 'Upload een bestand dat een foto is!';
                        break;
                    case 'picturefiletype':
                        $msg = 'Upload een foto met het bestandstype .PNG, .JPEG, JPG of .GIF!';
                        break;
                    case 'picturesize':
                        $msg = 'Upload een foto met een bestandsgrootte van minder dan 12 MB!';
                        break;
                    case 'location':
                        $msg = 'Deze locatie is niet geldig! Zorg dat er geen andere speeltuinen in de buurt zijn!';
                        break;
                }
                echo '<div class="errordiv">
                            <h2>FOUT</h2>
                            ' . $msg . '
                            </div>';
            }
            $editMode = false;
            if (isset($_GET['action']) && isset($_GET['id'])) {
                if ($_GET['action'] == "edit") {
                    include 'includes/dbh.inc.php';

                    $editMode = true;
                    $editId = (int) $_GET['id'];
                    $sql = "SELECT name, lat, lng, age_from, age_to, always_open, catering_available FROM playgrounds where id=" . $editId;
                    $result = $conn->query($sql);
                    if (!$result) {
                        http_response_code(500);
                        exit();
                    }
                    $row = $result->fetch_row();

                    $name = $row[0];
                    $lat = $row[1];
                    $lng = $row[2];
                    $ageFrom = $row[3];
                    $ageTo = $row[4];
                    $alwaysOpen = $row[5];
                    $cateringAvaiblable = $row[6];

                    $sql = "SELECT path FROM pictures WHERE playground_id=" . $editId;
                    $result = $conn->query($sql);
                    if (!$result) {
                        http_response_code(500);
                        exit();
                    }
                    $path = ($result->fetch_row())[0];

                    echo '  <input type="hidden" name="action" value="update">
                                <input type="hidden" name="updateId" value="' . $editId . '">';
                }
            }
            if (!$editMode) {
                echo '
                    <a class="btn" href="index.php">Terug</a>
                    <h1>Speeltuin Toevoegen</h1>';
            } else {
                echo '
                    <a class="btn" href="playground.php?id=' . $editId . '">Terug</a>
                    <h1>Speeltuin Bewerken</h1>';
            }
            ?>
            <h2>Algemeen</h2>
            <table>
                <tr>
                    <td><label for="name">Naam</label></td>
                    <td>
                        <?php
                        if (!$editMode) {
                            echo '<input type="text" id="name" name="name">';
                        } else {
                            echo '<input type="text" id="name" value="' . $name . '" name="name">';
                        }
                        ?>
                    </td>
                </tr>

                <?php
                if (!$editMode || !isset($path) || empty($path)) {
                    echo '
                        <tr>
                            <td>Foto (optioneel)</td>
                            <td>
                                <input type="file" name="pictureToUpload">
                            </td>
                        </tr>
                    ';
                } else {
                    echo '
                        <tr>
                            <td>Foto Vervangen</td>
                            <td>
                                <input type="file" name="pictureToUpload">
                            </td>
                        </tr>
                    ';
                }
                ?>
            </table>
            <hr>
            <h2>Locatie</h2>
            <p>Klik op de kaart om een locatie te selecteren:</p>
            <div id="smallmap">
            </div>
            <table>
                <tr>
                    <td><label for="lat">Breedtegraad</label></td>
                    <td>
                        <?php
                        if (!$editMode) {
                            $lat = 0;
                        }
                        echo "<input type='number' step='0.0001' id='lat' name='lat' value=" . $lat . ">";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="lng">Lengtegraad</label></td>
                    <td>
                        <?php
                        if (!$editMode) {
                            $lng = 0;
                        }
                        echo "<input type='number' step='0.0001' id='lng' name='lng' value=" . $lng . ">";
                        ?>
                    </td>
                </tr>
            </table>
            <script>
                var currentLat = 52.379191;
                var currentLng = 4.900956;

                var map = L.map('smallmap').setView([52.43, 5.42], 9);
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
                map.on('click', function(e) {
                    currentLat = e.latlng.lat;
                    currentLng = e.latlng.lng;
                    updateLocation();
                });

                showLocation(map);

                var customIcon = L.icon({
                    iconUrl: 'img/marker-icon.png',
                    shadowUrl: 'img/marker-icon-shadow.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                    shadowSize: [40, 40],
                    shadowAnchor: [20, 20],
                    popupAnchor: [0, 0]
                });
                var marker = L.marker(customIcon, {
                    icon: customIcon
                });


                navigator.geolocation.getCurrentPosition(setDefaultPosition, null);

                function setDefaultPosition(position) {
                    currentLat = position.coords.latitude;
                    currentLng = position.coords.longitude;
                    updateLocation();
                }

                var latInput = document.getElementById('lat');
                latInput.addEventListener('change', function(e) {
                    currentLat = e.target.value;
                    updateLocation();
                });
                var lngInput = document.getElementById('lng');
                lngInput.addEventListener('change', function(e) {
                    currentLng = e.target.value;
                    updateLocation();
                });

                function updateLocation() {
                    marker.setLatLng([currentLat, currentLng]).addTo(map);
                    latInput.value = parseFloat(currentLat).toFixed(4);
                    lngInput.value = parseFloat(currentLng).toFixed(4);
                }
            </script>
            <hr>
            <h2>Onderdelen</h2>
            <table>
                </script>
                <?php
                include 'includes/parts.inc.php';
                if (!$editMode) {
                    foreach ($parts as $part) {
                        $inputname = "part" . $part[0];
                        $subButtonId = "subPart" . $part[0];
                        $addButtonId = "addPart" . $part[0];
                        echo
                            '<tr>
                                <td>
                                    <label for="' . $inputname . '">' . $part[1] . '</label>
                                </td>
                                <td>
                                    <button type="button" class="addSubButton" id="' . $subButtonId . '"> - </button>
                                    <input type="number" id="' . $inputname . '" name="' . $inputname . '" min="0" max="20" value="0">
                                    <button type="button" class="addSubButton" id="' . $addButtonId . '"> + </button>
                                    <script>
                                        var ' . $inputname . ' = document.getElementById("' . $inputname . '");
                                        var ' . $subButtonId . ' = document.getElementById("' . $subButtonId . '");
                                        var ' . $addButtonId . ' = document.getElementById("' . $addButtonId . '");
                                        ' . $subButtonId . '.addEventListener("click", function () {
                                            var newValue = parseFloat(' . $inputname . '.value) - 1;
                                            if (newValue >= 0 && newValue <= 20)
                                                ' . $inputname . '.value = newValue;
                                        });
                                        ' . $addButtonId . '.addEventListener("click", function () {
                                            var newValue = parseFloat(' . $inputname . '.value) + 1;
                                            if (newValue >= 0 && newValue <= 20)
                                                ' . $inputname . '.value = newValue;
                                        });
                                    </script>
                                </td>
                            </tr>';
                    }
                } else {
                    $sql = "SELECT part_id, amount FROM parts_map WHERE playground_id=" . $editId;
                    $result = $conn->query($sql);
                    if (!$result) {
                        http_response_code(500);
                        exit();
                    }
                    $partMap = array();
                    while ($row = $result->fetch_row()) {
                        $partMap[(int) $row[0]] = (int) $row[1];
                    }
                    foreach ($parts as $part) {
                        // Get amount of id $part[0] in
                        if (array_key_exists($part[0], $partMap)) {
                            $amount = $partMap[$part[0]];
                        } else {
                            $amount = 0;
                        }
                        $inputname = "part" . $part[0];
                        echo
                            '<tr>
                                <td>
                                    <label for="' . $inputname . '">' . $part[1] . '</label>
                                </td>
                                <td>
                                    <input type="number" name="' . $inputname . '" min="0" max="10" value="' . $amount . '">
                                </td>
                            </tr>';
                    }
                }
                ?>
            </table>
            <hr>
            <h2>Leeftijd & Uitdaging</h2>
            Deze speeltuin is leuk en uitdagend voor kinderen: <br>
            Van
            <?php
            if (!$editMode) {
                echo '<input type="number" name="ageFrom" min="0" max="18" value="3">';
            } else {
                echo '<input type="number" name="ageFrom" min="0" max="18" value="' . $ageFrom . '">';
            }
            ?>
            t/m
            <?php
            if (!$editMode) {
                echo '<input type="number" name="ageTo" min="0" max="18" value="5">';
            } else {
                echo '<input type="number" name="ageTo" min="0" max="18" value="' . $ageTo . '">';
            }
            ?>
            jaar
            <hr>
            <h2>Voorzieningen</h2>
            <div class="checkbox">
                <?php
                if (!$editMode) {
                    echo '<input type="checkbox"  name="alwaysOpen" value="true" checked>';
                } else {
                    if ($alwaysOpen == 1)
                        echo '<input type="checkbox"  name="alwaysOpen" value="true" checked>';
                    else
                        echo '<input type="checkbox"  name="alwaysOpen" value="true">';
                }
                ?>
                <label for="alwaysOpen">Deze speeltuin is altijd open</label>
            </div>
            <br>
            <div class="checkbox">
                <?php
                if (!$editMode) {
                    echo '<input type="checkbox" name="cateringAvailable" value="true">';
                } else {
                    if ($cateringAvaiblable == 1)
                        echo '<input type="checkbox" name="cateringAvailable" value="true" checked>';
                    else
                        echo '<input type="checkbox" name="cateringAvailable" value="true">';
                }
                ?>
                <label for="cateringAvailable">Er is horeca aanwezig</label>
            </div>
            <hr>
            <h2>Beoordeling</h2>
            <label for="nickname">Gebruikersnaam:</label>
            <?php
            if ($editMode) {
                $sql = "SELECT nickname, comment, rating FROM reviews WHERE ip='" . $ip . "' AND playground_id=" . $editId;
                $result = $conn->query($sql);
                if (!$result) {
                    http_response_code(500);
                    exit();
                }
                $row = $result->fetch_row();
                $nickname = $row[0];
                $comment = $row[1];
                $rating = $row[2];
            } else {
                $nickname = "";
                $comment = "";
                $rating = 4;
            }
            ?>
            <?php
            echo '<input type="text" name="nickname" minlength="4" value="' . $nickname . '" maxlength="20">';
            ?>
            <br>
            <label for="comment">Tekst (optioneel):</label>
            <br>
            <?php
            echo '<textarea name="comment" maxlength ="240">' . $comment . '</textarea>';
            ?>
            <br>
            Eigen cijfer (1 tot 5 sterren):
            <?php
            echo '<input type="number" name="rating" min="1" max="5" value="' . $rating . '">';
            ?>
            <br><br>
            <i>TIP: Denk bij de beoordeling aan bijvoorbeeld de staat van onderhoud, omgeving, diversiteit...</i>
            <br><br>
            <?php
            if (!$editMode) {
                echo '<input class="btn" type="submit" value="Voeg toe">';
            } else {
                echo '<input class="btn" type="submit" value="Opslaan">';
            }
            ?>
        </form>
    </div>
</body>

</html>