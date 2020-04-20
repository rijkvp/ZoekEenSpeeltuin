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
            <li id="logo"><a href="index.php">Speeltuinen</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="add_playground.php">Toevoegen</a></li>
        </ul>
        </nav>
    </header>
   <?php
        if (isset($_GET['id']))
        {
            require 'includes/dbh.inc.php';

            $playgroundId = $_GET['id'];
            
            $sql = "SELECT * FROM playgrounds WHERE id=".$playgroundId;
            $result = $conn->query($sql); 

            if (!$result) {
                die("ERROR ".$conn -> error);
                //http_response_code(500);
                //exit();
            }
            $row = $result -> fetch_row();
            echo("<h1>".$row[1]."</h1>");
            echo("<p>".$row[2].", ".$row[3]."</p>");
            echo("<p>Voor kinderen van ".$row[4]." t/m ".$row[5]." jaar</p>");
            if ($row[6] == 1)
                echo("<p>Is altijd open</p>");
            else
                echo("<p>Is NIET altijd open</p>");

            if ($row[7] == 1)
                echo("<p>Horeca aanwezig</p>");

            echo("<h2>Onderdelen</h2>");
            $sql = "SELECT name FROM parts";
            $result = $conn->query($sql); 

            if (!$result) {
                die("ERROR ".$conn -> error);
                //http_response_code(500);
                //exit();
            }
            while ($row = $result -> fetch_row()) {
                $parts[] = $row[0];
            }

            $sql = "SELECT part_id, amount FROM parts_map WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 

            if (!$result) {
                die("ERROR ".$conn -> error);
                //http_response_code(500);
                //exit();
            }
            while ($row = $result -> fetch_row()) {
                echo("Er zijn ".$row[1]." ".$parts[$row[0] - 1]);
                echo("<br>");
            }
            echo("<h2>Waardering</h2>");
            $sql = "SELECT AVG(rating) FROM ratings WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                die("ERROR ".$conn -> error);
                //http_response_code(500);
                //exit();
            }
            $avgRating = ($result -> fetch_row())[0];
            $sql = "SELECT COUNT(rating) FROM ratings WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                die("ERROR ".$conn -> error);
                //http_response_code(500);
                //exit();
            }
            $ratingCount = ($result -> fetch_row())[0];
            echo("Gemiddeld cijfer: ".$avgRating);
            echo("<br>Aantal waarderingen: ".$ratingCount);
            
            echo("<h2>Reviews</h2>");
            $sql = "SELECT nickname, comment, ip FROM reviews WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                die("ERROR ".$conn -> error);
                //http_response_code(500);
                //exit();
            }
            while ($row = $result -> fetch_row()) {
                $reviews[] = $row;
            }
            if (isset($reviews))
            {
                foreach($reviews as $review)
                {
                    $sql = "SELECT rating FROM ratings WHERE playground_id=".$playgroundId." AND ip='".$review[2]."'";
                    $result = $conn->query($sql); 
                    if (!$result) {
                        die("ERROR ".$conn -> error);
                        //http_response_code(500);
                        //exit();
                    }
                    $rating = ($result -> fetch_row())[0];
                    echo($rating." STERREN<br>");
                    echo("<b>".$review[0].":</b> ".$review[1]);
                }
            }
            else
            {
                echo "Nog geen reviews";
            }
        }
        else
        {
            echo "No playground selected";
        }
        
    ?>
    <h2>Geef een review</h2>
    <?php
      echo '<form action="includes/add_review.inc.php?id='.$_GET['id'].'" method="post">';
    ?>
        <label for="nickname">Gebruikersnaam:</label>
        <input type="text" name="nickname">
        <br>
        <label for="comment">Tekst:</label>
        <br>
        <textarea name="comment" rows="10" cols="30"></textarea>
        <br>
        Zelf geef ik deze speeltuin het cijfer: <input type="number" name="rating" min="1" max="5" value="4">
        <br>
        TIP: Denk bijvoorbeeld aan: staat van onderhoud, omgeving, diversiteit
        <br>
        <input type="submit" value="Verzonden">
    </form>
    </body>
</html>

