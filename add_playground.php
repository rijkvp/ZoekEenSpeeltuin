<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Zoek een Speeltuin - Toevoegen</title>
    <!-- Own CSS Stylesheet -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">>
  </head>
  <body>
    <header>
        <nav>
            <?php include "navigation.php"; navigation("add_playground"); ?>
        </nav>
    </header>
    <div id="content">
        <form action="includes/add_playground.inc.php" method="post" enctype="multipart/form-data">
            <?php 
                if (isset($_GET['error']))
                {
                    $type = $_GET['error'];
                    $msg = 'Onbekende error!';
                    switch($type)
                    {
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
                            $msg = 'Upload een foto met het bestandstype .JPG, .PNG, .JPEG of .GIF!';
                            break;
                        case 'picturesize':
                            $msg = 'Upload een foto met een bestandsgrootte van minder dan 2 MB!';
                            break;
                        case 'location':
                            $msg = 'Deze locatie is niet geldig! Zorg dat er geen andere speeltuinen in de buurt zijn!';
                            break;
                    }
                    echo '<div class="errordiv">
                            <h2>FOUT</h2>
                            '.$msg.'
                            </div>';
                }
                $editMode = false;
                if (isset($_GET['action']) && isset($_GET['id']))
                {
                    if ($_GET['action'] == "edit")
                    {
                        include 'includes/dbh.inc.php';

                        $editMode = true;
                        $editId = (int)$_GET['id'];
                        $sql = "SELECT name, lat, lng, age_from, age_to, always_open, catering_available FROM playgrounds where id=".$editId;
                        $result = $conn->query($sql);
                        if (!$result)
                        {
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

                        $sql = "SELECT path FROM pictures WHERE playground_id=".$editId;
                        $result = $conn->query($sql);
                        if (!$result)
                        {
                            http_response_code(500);
                            exit();
                        }
                        $path = ($result->fetch_row())[0];

                        echo '  <input type="hidden" name="action" value="update">
                                <input type="hidden" name="updateId" value="'.$editId.'">';
                        
                    }
                }
                if (!$editMode) {
                    echo '
                    <a class="btn" href="index.php">Terug</a>
                    <h1>Speeltuin Toevoegen</h1>';
                }
                else {
                    echo '
                    <a class="btn" href="playground.php?id='.$editId.'">Terug</a>
                    <h1>Speeltuin Bewerken</h1>';
                }
            ?>
            <h2>Algemeen</h2>
            <table>
                <tr>
                    <td><label for="name">Naam</label></td>
                    <td>
                        <?php 
                            if (!$editMode)
                            {
                                echo '<input type="text" id="name" name="name">';
                            }
                            else {
                                echo '<input type="text" id="name" value="'.$name.'" name="name">';
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="lat">Breedtegraad</label></td>
                    <td>
                        <?php
                            if (!$editMode)
                            {
                                if (isset($_GET["lat"]))
                                    $lat = (float) $_GET["lat"];
                                else
                                    $lat = 0.0;
                            }
                            echo "<input type='number' step='any' id='lat' name='lat' value=".$lat.">";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="lng">Lengtegraad</label></td>
                    <td>
                    <?php
                        if (!$editMode)
                        {
                            if (isset($_GET["lng"]))
                                $lng = (float) $_GET["lng"];
                            else
                                $lng = 0.0;
                        }
                        echo "<input type='number' step='any' id='lng' name='lng' value=".$lng.">";
                    ?>
                    </td>
                </tr>
                <?php 
                if (!$editMode || !isset($path) || empty($path))
                {
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
                            <td>Foto</td>
                            <td>Al toegevoegd</td>
                        </tr>
                    ';
                }
                ?>
            </table>
            <br>
            <h2>Onderdelen</h2>
            <table>
                <?php
                    include 'includes/parts.inc.php';
                    if (!$editMode)
                    {
                        foreach ($parts as $part)
                        {
                            $inputname = "part".$part[0];
                            echo
                            '<tr>
                                <td>
                                    <label for="'.$inputname.'">'.$part[1].'</label>
                                </td>
                                <td>
                                    <input type="number" name="'.$inputname.'" min="0" max="10" value="0">
                                </td>
                            </tr>';
                        }
                    } else {
                        $sql = "SELECT part_id, amount FROM parts_map WHERE playground_id=".$editId;
                        $result = $conn->query($sql); 
                        if (!$result) {
                            http_response_code(500);
                            exit();
                        }
                        while($row = $result->fetch_row())
                        {
                            $partMap[(int)$row[0]] = (int)$row[1];
                        }
                        foreach ($parts as $part)
                        {
                            // Get amount of id $part[0] in
                            if (array_key_exists($part[0], $partMap)) {
                                $amount = $partMap[$part[0]];
                            }
                            else {
                                $amount = 0;
                            }
                            $inputname = "part".$part[0];
                            echo
                            '<tr>
                                <td>
                                    <label for="'.$inputname.'">'.$part[1].'</label>
                                </td>
                                <td>
                                    <input type="number" name="'.$inputname.'" min="0" max="10" value="'.$amount.'">
                                </td>
                            </tr>';
                        }
                    }
                ?>
            </table>
            <br>
            <h2>Leeftijd & Uitdaging</h2>
            Deze speeltuin is leuk en uitdagend voor kinderen: <br>
            Van
            <?php 
                if (!$editMode) {
                    echo '<input type="number" name="ageFrom" min="0" max="18" value="3">';
                } else {
                    echo '<input type="number" name="ageFrom" min="0" max="18" value="'.$ageFrom.'">';
                }
            ?>
            t/m
            <?php 
                if (!$editMode) {
                    echo '<input type="number" name="ageTo" min="0" max="18" value="5">';
                } else {
                    echo '<input type="number" name="ageTo" min="0" max="18" value="'.$ageTo.'">';
                }
            ?>
            jaar
            <br><br>
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
            <br>
            <br>
            <h2>Beoordeling</h2>
            <label for="nickname">Gebruikersnaam:</label>
            <?php
                if ($editMode)
                {
                    $sql = "SELECT nickname, comment, rating FROM reviews WHERE ip='".$ip."' AND playground_id=".$editId;
                    $result = $conn->query($sql);
                    if (!$result)
                    {
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
                echo '<input type="text" name="nickname" minlength="4" value="'.$nickname.'" maxlength="20">';
            ?>
            <br>
            <label for="comment">Tekst (optioneel):</label>
            <br>
            <?php 
                echo '<textarea name="comment" rows="5" cols="60" maxlength ="240">'.$comment.'</textarea>';
            ?>
            <br>
            Eigen cijfer (1 tot 5 sterren): 
            <?php 
                echo '<input type="number" name="rating" min="1" max="5" value="'.$rating.'">';
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