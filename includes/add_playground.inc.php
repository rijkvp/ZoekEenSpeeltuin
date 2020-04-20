<?php

if (isset($_POST['name']) && isset($_POST['lat']) && isset($_POST['lng']) && isset($_POST['ageFrom']) && isset($_POST['ageTo']) && isset($_POST['rating']))
{
    require 'dbh.inc.php';

    $name = $_POST['name'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $ageFrom = $_POST['ageFrom'];
    $ageTo = $_POST['ageTo'];
    
    $rating = $_POST['rating'];

    if (isset($_POST['alwaysOpen']))
        $alwaysOpen = true;
    else
        $alwaysOpen = false;
    
    if (isset($_POST['cateringAvailable']))
        $cateringAvailable = true;
    else
        $cateringAvailable = false;

    foreach($_POST as $key => $value)
    {
        if (strpos($key, 'part') === 0) {
            if ($value != 0)
            {
                $partId = (int)substr($key,4,1);
                $partsToInsert[$partId] = (int)$value;
            }
        }
    }

    $sql = "INSERT INTO playgrounds (name, lat, lng, age_from, age_to, always_open, catering_available) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        die ("FAIL: ".$stmt->error);
        header("Location: ../add_playground.php?error=sqlerror");
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "sssssss", $name, $lat, $lng, $ageFrom, $ageTo, $alwaysOpen, $cateringAvailable);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $playgroundId = mysqli_insert_id($conn);
    }

    foreach($partsToInsert as $partId => $amount)
    {
        $sql = "INSERT INTO parts_map (playground_id, part_id, amount) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        
        if (!mysqli_stmt_prepare($stmt, $sql))
        {
            die ("FAIL: ".$stmt->error);
            header("Location: ../add_playground.php?error=sqlerror");
            exit();
        }
        else
        {
            mysqli_stmt_bind_param($stmt, "sss", $playgroundId, $partId, $amount);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    $ip = getenv('HTTP_CLIENT_IP')?:
    getenv('HTTP_X_FORWARDED_FOR')?:
    getenv('HTTP_X_FORWARDED')?:
    getenv('HTTP_FORWARDED_FOR')?:
    getenv('HTTP_FORWARDED')?:
    getenv('REMOTE_ADDR');
    
    $sql = "INSERT INTO ratings (playground_id, ip, rating) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        die ("FAIL: ".$stmt->error);
        header("Location: ../add_playground.php?error=sqlerror");
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "sss", $playgroundId, $ip, $rating);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    header("Location: ../add_playground.php?error=false");
    $conn->close();
}
else
{
    header("Location: ../add_playground.php?error=missingvalues");
    exit();
}