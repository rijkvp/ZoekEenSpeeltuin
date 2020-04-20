<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Add Playground</title>
    <link rel="stylesheet" href="leaflet/leaflet.css" />
    <style>
        * {
            font-family: sans-serif;
        }
    </style>
  </head>
  <body>
  
    <h1>Voeg een speeltuin toe</h1>
    <a href="index.php">Terug</a>
    <form action="includes/add_playground.inc.php" method="post">
        <label for="name">Naam:</label>
        <br>
        <input type="text" id="name" name="name">
        <br>
        <label for="lat">Breedtegraad:</label>
        <br>
        <?php
            if (isset($_GET["lat"]))
                $lat = (float) $_GET["lat"];
            else
                $lat = 0.0;

            echo "<input type='number' step='any' id='lat' name='lat' value=";
            echo $lat;
            echo ">";
        ?>
        <br>
        <label for="lng">Lengtegraad:</label>
        <br>
        <?php
            if (isset($_GET["lng"]))
                $lng = (float) $_GET["lng"];
            else
                $lng = 0.0;
                
            echo "<input type='number' step='any' id='lng' name='lng' value=";
            echo $lng;
            echo ">";
        ?>
        <?php
            include 'includes/parts.inc.php';
            foreach ($parts as $part)
            {
                $inputname = "part".$part[0];
                echo("<br>");
                echo('<label for="'.$inputname.'">Aantal onderdelen van "'.$part[1].'"</label>');
                echo('<input type="number" name="'.$inputname.'" min="0" max="10" value="0">');
            }
        ?>
        <br>
        Deze speeltuin is leuk voor kinderen vanaf 
        <input type="number" name="ageFrom" min="0" max="18" value="3">
        jaar en t/m
        <input type="number" name="ageTo" min="0" max="18" value="5">
        jaar
        <br>
        <input type="checkbox" name="alwaysOpen" value="true">
        <label for="alwaysOpen">Deze speeltuin is altijd open</label>
        <br>
        <input type="checkbox" name="cateringAvailable" value="true">
        <label for="cateringAvailable">Is is horeca aanwezig</label>
        <br>
        <br>
        Zelf geef ik deze speeltuin het cijfer: <input type="number" name="rating" min="1" max="5" value="4">
        <br>
        TIP: Denk bijvoorbeeld aan: staat van onderhoud, omgeving, diversiteit
        <br>
        <input type="submit" value="Voeg toe">
    </form>
    </body>
</html>