<?php

if (isset($_GET['id']) && isset($_POST['nickname']) && isset($_POST['comment']) && isset($_POST['rating']))
{
    require 'dbh.inc.php';


    // TODO: Check ip - one review per user
    $ip = getenv('HTTP_CLIENT_IP')?:
    getenv('HTTP_X_FORWARDED_FOR')?:
    getenv('HTTP_X_FORWARDED')?:
    getenv('HTTP_FORWARDED_FOR')?:
    getenv('HTTP_FORWARDED')?:
    getenv('REMOTE_ADDR');

    $playgroundId = $_GET['id'];
    $nickname = $_POST['nickname'];
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];
        
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

    $sql = "INSERT INTO reviews (playground_id, ip, nickname, comment) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        die ("FAIL: ".$stmt->error);
        header("Location: ../add_playground.php?error=sqlerror");
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "ssss", $playgroundId, $ip, $nickname, $comment);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: ../playground.php?id=".$playgroundId);
    $conn->close();
}
else
{
    header("Location: ../add_playground.php?error=missingvalues");
    exit();
}