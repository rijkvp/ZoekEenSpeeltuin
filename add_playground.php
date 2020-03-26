<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Using MySQL and PHP with Google Maps</title>
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
            $lat = (float) $_GET["lat"];
            
            echo "<input type='number' step='any' id='lat' name='lat' value=";
            echo $lat;
            echo ">";
        ?>
        <br>
        <label for="lng">Lengtegraad:</label>
        <br>
        <?php
            $lng = (float) $_GET["lng"];
            echo "<input type='number' step='any' id='lng' name='lng' value=";
            echo $lng;
            echo ">";
        ?>
        <br>
        <input type="submit" value="Voeg toe">
    </form>
    </body>
</html>