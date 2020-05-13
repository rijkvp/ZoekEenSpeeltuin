<?php

function showFooter($showDisclaimer)
{
    echo '<footer>';
    if ($showDisclaimer) {
        echo '<p><strong>Disclaimer:</strong> De gegevens van elke speeltuin worden door gebruikers toegevoegd en kunnen dus afwijken met de werkelijke situatie. Wil je een verbetering voorstellen? Neem dan contact met ons op: <a href="mailto:info@zoekeenspeeltuin.nl">info@zoekeenspeeltuin.nl</a> Wij passen het dan zo snel mogelijk aan!</p>';
    }
    echo '
        <br>
        <p class="copyright">Copyright &copy; 2020 - ZoekEenSpeeltuin</a>
    </footer>';
}
