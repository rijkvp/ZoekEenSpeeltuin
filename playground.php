<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Zoek een Speeltuin - Speeltuin Informatie</title>
    <!-- Leaflet -->
    <link rel="stylesheet" type="text/css" href="libs/leaflet/leaflet.css" />
    <script src="libs/leaflet/leaflet.js"></script>
    <!-- Own CSS Stylesheet -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!-- Own JS -->
    <script src="js/util.js"></script>
    <script src="js/selectedmarker.js"></script>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  </head>
  <body>
    <header>
        <nav>
            <?php include "navigation.php"; navigation("none"); ?>
        </nav>
    </header>
    <div id="content">
    <a class="btn" href="index.php">Terug</a>
    <?php 
        if (isset($_GET['error']))
        {
            $type = $_GET['error'];
            $msg = 'Onbekende error!';
            switch($type)
            {
                case 'nickname':
                    $msg = 'Voer een naam in van 4 tot 30 tekens!';
                    break;
                case 'nicknameused':
                    $msg = 'Deze gebruikersnaam is al eerder door iemand anders gebruikt! Voer aub een andere gebruikersnaam in om verwarring te voorkomen.';
                    break;
                case 'comment':
                    $msg = 'Voer een stukje tekst in van 18 tot 240 tekens!';
                    break;
            }
            echo '<div class="errordiv">
                    <h2>FOUT</h2>
                    '.$msg.'
                    </div>';
        }
    ?>
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
            if (empty($playground) || !isset($playground))
            {
                echo "Deze speeltuin bestaat niet (meer)!";
                return;
            }
            $playgroundIp = $playground[8];
            
            $lat = $playground[2];
            $lng = $playground[3];
            $date = DateTime::createFromFormat('Y-m-d', $playground[9]);

            $sql = "SELECT path FROM pictures WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                http_response_code(500);
                exit();
            }
            $picture_path = ($result -> fetch_row())[0];
            echo'
                <p class="date">'.date_format($date,"d-m-Y").'</p>
                <h1>'.$playground[1].'</h1>
                <a href="http://www.google.com/maps/place/'.$lat.','.$lng.'">Zie op Google Maps</a>';
            if (!empty($picture_path) && isset($picture_path))
            {
                echo '<img class="playgroundImage" src='.$picture_path.'><hr>';
            }
            if ($ip == $playgroundIp)
            {
                echo '
                <hr>
                    <h2>Toegevoegd door jou</h2>
                    <p>Laatst bewerkt op: '.date_format($date,"d-m-Y").'</p>
                    <a href="add_playground.php?action=edit&id='.$playgroundId.'" class="btn smallbtn">Speeltuin bewerken</a>
                    <hr>
                ';
            }
            echo'
                <h2>Kaart</h2>
                <div id="smallmap"></div>
                <hr>
                <h2>Algemeen</h2>
                <table>
                <tr><td>Locatie</td><td>'.$lat.', '.$lng.'</td></tr>
                <tr><td>Leeftijd/Uitdaging</td><td>'.$playground[4].' t/m '.$playground[5].' jaar</td></tr>
                <tr>
                    <td>Altijd open</td>';
                if ($playground[6] == 1)
                    echo("<td>Ja</td>");
                else
                    echo("<td><strong>Nee</strong></td>");
                echo '</tr>
                      <tr>
                        <td>Horeca aanwezig</td>';
                        if ($playground[7] == 1)
                            echo("<td><strong>Ja</strong></td>");
                        else 
                            echo("<td>Nee</td>");
                echo'</tr></table><hr>';
                      

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
            echo '<table>
                    <tr><th>Onderdeel</th><th>Aantal</th></tr>';
            while ($playground = $result -> fetch_row()) {
                echo '  <tr>
                            <td>'.$parts[$playground[0] - 1].'</td>
                            <td>'.$playground[1].'</td> 
                        </tr>';
            }
            echo '  </table><hr>
                    <h2>Reviews</h2>';
            $sql = "SELECT AVG(rating) FROM reviews WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                http_response_code(500);
                exit();
            }
            $avgRating = ($result -> fetch_row())[0];
            $avgRating = number_format((float)$avgRating, 1, '.', '');
            $sql = "SELECT COUNT(rating) FROM reviews WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                http_response_code(500);
                exit();
            }
            $ratingCount = ($result -> fetch_row())[0];
            echo '
                <div id="averageRating">
                    <span class="ratinglabel">'.$avgRating.'</span>
                    <span class="ratingstars">'.$avgRating.'</span>
                    <span>('.$ratingCount.' reviews)</span>
                </div>';
            
            $sql = "SELECT nickname, comment, ip, rating FROM reviews WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql); 
            if (!$result) {
                http_response_code(500);
                exit();
            }
            while ($playground = $result -> fetch_row()) {
                $reviews[] = $playground;
            }
            if (isset($reviews))
            {
                echo "<br>";
                $sql = "SELECT ip FROM playgrounds WHERE id='".$playgroundId."'";
                $result = $conn->query($sql); 
                if (!$result) {
                    http_response_code(500);
                    exit();
                }
                $uploadIp = ($result -> fetch_row())[0];
                foreach($reviews as $review)
                {
                    $sql = "SELECT COUNT(rating) FROM reviews WHERE ip='".$review[2]."'";
                    $result = $conn->query($sql); 
                    if (!$result) {
                        http_response_code(500);
                        exit();
                    }
                    $userReviewCount = ($result -> fetch_row())[0];

                    $sql = "SELECT rating FROM reviews WHERE playground_id=".$playgroundId." AND ip='".$review[2]."'";
                    $result = $conn->query($sql); 
                    if (!$result) {
                        http_response_code(500);
                        exit();
                    }
                    $rating = ($result -> fetch_row())[0];
                    if ($review[2] == $ip)
                    {
                        $label = "[ Jij ] ";
                    }
                    else if ($review[2] == $uploadIp)
                    {
                        $label = "[ Uploader ] ";
                    }
                    else
                    {
                        $label = "";
                    }
                    echo'<div class="review">
                        <b class="reviewNickName">'.$label.$review[0].'</b>
                        <p class="reviewCount">'.$userReviewCount.' reviews</p>
                        <p class="ratingstars ratingsmall">'.$review[3].'</p>
                        <p>'.$review[1].'</p>
                    </div>';
                }
                echo "<script> makeAllStarLayouts(); setupMap(".$lat.", ".$lng.");</script>";
            }
            else
            {
                echo "Nog geen reviews";
            }
            $sql = "SELECT ip FROM reviews WHERE playground_id=".$playgroundId;
            $result = $conn->query($sql);
            if (!$result) {
                http_response_code(500);
                exit();
            } 
            $alreadyReviewed = false;
            while ($row = $result -> fetch_row()) {
                if ($row[0] == $ip)
                {
                    $alreadyReviewed = true;
                }
            }
            if ($alreadyReviewed)
            {
                $sql = "SELECT nickname, comment, rating FROM reviews WHERE playground_id=".$playgroundId." AND ip='".$ip."'";
                $result = $conn->query($sql);
                if (!$result) {
                    http_response_code(500);
                    exit();
                }
                $row = $result -> fetch_row();
                $nickname = $row[0];
                $comment = $row[1];
                $rating = $row[2];
            }
            if ($ip != $playgroundIp && !$alreadyReviewed)
            {
                echo '<hr>
                <h2>Geef een review</h2>
                <form action="includes/add_review.inc.php?id='.$_GET['id'].'" method="post">
                    <label for="nickname">Gebruikersnaam:</label>
                    <input type="text" name="nickname" minlength="4" maxlength="20">
                    <br>
                    <label for="comment">Tekst:</label>
                    <br>
                    <textarea name="comment" rows="5" cols="60" minlength="18" maxlength ="240"></textarea>
                    <br>
                    Eigen cijfer (1 tot 5 sterren): <input type="number" name="rating" min="1" max="5" value="4">
                    <br>
                    TIP: Denk bijvoorbeeld aan: staat van onderhoud, omgeving, diversiteit
                    <br>
                    <input class="btn smallbtn" type="submit" value="Versturen">
                </form>
                ';
            }
            else if ($alreadyReviewed) {
                echo '<hr>
                <div id="review">
                    <h2>Review</h2>
                    <p>Je hebt al gereviewd.</p>
                    <button onclick="showEditReview()"class="btn smallbtn">Review bewerken</button>
                </div>
                <div id="editreview">
                    <h2>Review bewerken</h2>
                    <form action="includes/add_review.inc.php?id='.$_GET['id'].'" method="post">
                        <input type="hidden" name="action" value="update">
                        <label for="nickname">Gebruikersnaam:</label>
                        <input type="text" name="nickname" minlength="4" maxlength="20" value="'.$nickname.'">
                        <br>
                        <label for="comment">Tekst:</label>
                        <br>
                        <textarea name="comment" rows="5" cols="60" minlength="18" maxlength ="240">'.$comment.'</textarea>
                        <br>
                        Eigen cijfer (1 tot 5 sterren): <input type="number" name="rating" min="1" max="5" value="'.$rating.'">
                        <br>
                        TIP: Denk bijvoorbeeld aan: staat van onderhoud, omgeving, diversiteit
                        <br>
                        <input class="btn smallbtn" type="submit" value="Opslaan">
                    </form>
                </div>
                <script>
                    var review = document.getElementById("review");
                    var editReview = document.getElementById("editreview");
                    editReview.style.display = "none";
                    function showEditReview()
                    {
                        review.style.display = "none";
                        editReview.style.display = "block";
                    }
                </script>';
            }
            else {
                echo "Je kan geen review meer toevoegen omdat je deze speeltuin zelf hebt geregistreed!";
            }
            
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

