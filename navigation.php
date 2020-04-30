<?php

function navigation($activepage)
{

    if ($activepage == "home")
    {
        echo '
            <ul>
                <li id="logo"><a href="index.php">Zoekeenspeeltuin.nl</a></li>
                <li id="logosmall"><a href="index.php">ZES</a></li>
                <li class="active"><a href="index.php">Kaart</a></li>
                <li><a href="add_playground.php">Toevoegen</a></li>
                <li><a href="about.php">Over</a></li>
            </ul>';
    } else if ($activepage == "add_playground") {
        echo '
        <ul>
            <li id="logo"><a href="index.php">Zoekeenspeeltuin.nl</a></li>
            <li id="logosmall"><a href="index.php">ZES</a></li>
            <li><a href="index.php">Kaart</a></li>
            <li class="active"><a href="add_playground.php">Toevoegen</a></li>
            <li><a href="about.php">Over</a></li>
        </ul>';
    } else if ($activepage == "about") {
        echo '
            <ul>
                <li id="logo"><a href="index.php">Zoekeenspeeltuin.nl</a></li>
                <li id="logosmall"><a href="index.php">ZES</a></li>
                <li><a href="index.php">Kaart</a></li>
                <li><a href="add_playground.php">Toevoegen</a></li>
                <li class="active"><a href="about.php">Over</a></li>
            </ul>';
    } else if ($activepage == "none") {
        echo '
            <ul>
                <li id="logo"><a href="index.php">Zoekeenspeeltuin.nl</a></li>
                <li id="logosmall"><a href="index.php">ZES</a></li>
                <li><a href="index.php">Kaart</a></li>
                <li><a href="add_playground.php">Toevoegen</a></li>
                <li><a href="about.php">Over</a></li>
            </ul>';
    } else {
        http_response_code(500);
        exit();
    }
}