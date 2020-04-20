<!DOCTYPE html >
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Speeltuin Toevoegen</title>
    <!-- Own CSS Stylesheet -->
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
  </head>
  <body>
    <header>
    <nav>
        <ul>
            <li id="logo"><a href="index.php">Speeltuinen</a></li>
            <li ><a href="index.php">Kaart</a></li>
            <li class="active"><a href="add_playground.php">Toevoegen</a></li>
        </ul>
        </nav>
    </header>
    <div id="content">
        <a class="btn" href="index.php">Terug</a>
        <h1>Speeltuin Toevoegen</h1>
        <form action="includes/add_playground.inc.php" method="post">
            <h2>Algemeen</h2>
            <table>
                <tr>
                    <td><label for="name">Naam</label></td>
                    <td><input type="text" id="name" name="name"></td>
                </tr>
                <tr>
                    <td><label for="lat">Breedtegraad</label></td>
                    <td>
                        <?php
                            if (isset($_GET["lat"]))
                                $lat = (float) $_GET["lat"];
                            else
                                $lat = 0.0;
                            echo "<input type='number' step='any' id='lat' name='lat' value=".$lat.">";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="lng">Lengtegraad</label></td>
                    <td>
                    <?php
                        if (isset($_GET["lng"]))
                            $lng = (float) $_GET["lng"];
                        else
                            $lng = 0.0;
                        echo "<input type='number' step='any' id='lng' name='lng' value=".$lng.">";
                    ?>
                    </td>
                </tr>
            </table>
            <br>
            <h2>Onderdelen</h2>
            <table>
                <?php
                    include 'includes/parts.inc.php';
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
                ?>
            </table>
            <br>
            <h2>Leeftijd & Uitdaging</h2>
            Deze speeltuin is leuk en uitdagend voor kinderen: <br>
            Van
            <input type="number" name="ageFrom" min="0" max="18" value="3">
            t/m
            <input type="number" name="ageTo" min="0" max="18" value="5">
            jaar
            <br><br>
            <h2>Voorzieningen</h2>
            <input type="checkbox" name="alwaysOpen" value="true">
            <label for="alwaysOpen">Deze speeltuin is altijd open</label>
            <br>
            <input type="checkbox" name="cateringAvailable" value="true">
            <label for="cateringAvailable">Is is horeca aanwezig</label>
            <br><br>
            <h2>Waardering</h2>
            Zelf geef ik deze speeltuin het cijfer: <input type="number" name="rating" min="1" max="5" value="4">
            <br>
            <i>TIP: Denk bijvoorbeeld aan: staat van onderhoud, omgeving, diversiteit...</i>
            <br><br>
            <input class="btn" type="submit" value="Voeg toe">
        </form>
            </div>
    </body>
</html>