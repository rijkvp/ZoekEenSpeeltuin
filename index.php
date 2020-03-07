<html>
    <head>
        <title>My Database Website</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css"> 
    </head>
    <body>
        <h1>My Database Website</h1>
        <hr>
        <p>This is just a test page testing if php & sql works. If it doesn't it should display some error message.</p>
        <br>
        <a href="map_testing.php">Map Testing - Google Maps API</a>
        <br>
        <a href="openstreetmap_test.php">Map Testing - Openstreeetmap</a>
        <br>
        <h1>PHP TEST</h1>
        <?php 
            echo'<h2>PHP WORKS!</h2>';
            echo'<p>Ready to start coding?</p>'
        ?>
        <br>
        <h1>MySQLi TEST</h1>
        <?php 
            include 'includes/dbh.inc.php';
            echo'<h2>MySQLi WORKS!</h2>';
            echo'<p>Let"s get that data!</p>';
        ?>
    </body>
</html>