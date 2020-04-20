<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Speeltuinen Website</title>
    <!-- Own CSS Stylesheet -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
  </head>
  <body>
    <header>
    <nav>
        <ul>
            <li id="logo"><a href="index.php">Speeltuinen</a></li>
            <li><a href="index.php">Kaart</a></li>
            <li><a href="add_playground.php">Toevoegen</a></li>
        </ul>
        </nav>
    </header>
    <div id="content">
    <a class="btn" href="index.php">Terug</a>

   <?php
        if (isset($_GET['id']))
        {
            require 'includes/dbh.inc.php';

            $playgroundId = $_GET['id'];
            
            $sql = "SELECT * FROM playgrounds WHERE id=".$playgroundId;
            $result = $conn->query($sql); 

            if (!$result) {
                http_response_code(500);
                exit();
            }
            $playground = $result -> fetch_row();
            echo'<h1>'.$playground[1].'</h1>
                <img src="https://picsum.photos/800/450">
                <h2>Algemeen</h2>
                <table>
                <tr><td>Locatie</td><td>'.$playground[2].', '.$playground[3].'</td></tr>
                <tr><td>Leeftijd/Uitdaging</td><td>'.$playground[4].' t/m '.$playground[5].' jaar</td></tr>
                </table>
                ';
            if ($playground[6] == 1)
                echo("<p>Is altijd open</p>");
            else
                echo("<p>Is NIET altijd open</p>");

            if ($playground[7] == 1)
                echo("<p>Horeca aanwezig</p>");

            echo'<h2>Onderdelen</h2>';
            $sql = "SELECT name FROM parts";
            $result = $conn->query($sql); 

            if (!$result) {
                http_response_code(500);
                exit();
            }
            while ($playground = $result -> fetch_row()) {
                $parts[] = $playground[0];
            }

            $sql = "SELECT part_id, amount FROM parts_map WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                http_response_code(500);
                exit();
            }
            echo '<table>';
            while ($playground = $result -> fetch_row()) {
                echo '<tr>
                    <td>'.$playground[1].'</td> 
                    <td>'.$parts[$playground[0] - 1].'</td>
                </tr>';
            }
            echo '</table>';
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
            while ($playground = $result -> fetch_row()) {
                $reviews[] = $playground;
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
            echo '
                <h2>Geef een review</h2>
                <form action="includes/add_review.inc.php?id='.$_GET['id'].'" method="post">
                    <label for="nickname">Gebruikersnaam:</label>
                    <input type="text" name="nickname">
                    <br>
                    <label for="comment">Tekst:</label>
                    <br>
                    <textarea name="comment" playgrounds="10" cols="30"></textarea>
                    <br>
                    Zelf geef ik deze speeltuin het cijfer: <input type="number" name="rating" min="1" max="5" value="4">
                    <br>
                    TIP: Denk bijvoorbeeld aan: staat van onderhoud, omgeving, diversiteit
                    <br>
                    <input type="submit" value="Verzonden">
                </form>
            ';
        }
        else
        {
            echo "<h1>ERROR</h1>
            <p>Je hebt geen speeltuin geselecteerd</p>";
        }
        
    ?>
    </div>
    </body>
</html>

