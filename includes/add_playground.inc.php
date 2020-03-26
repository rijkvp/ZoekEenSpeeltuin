<?php

session_start();
if (isset($_POST['name']) || isset($_POST['lat']) || isset($_POST['lng']))
{
    require 'dbh.inc.php';

    $name = $_POST['name'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $sql = "INSERT INTO playgrounds (name, lat, lng) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        die ("FAIL: ".$stmt->error);
        header("Location: ../add_playground.php?error=sqlerror");
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "sss", $name, $lat, $lng);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../add_playground.php?error=false");
    }
}
else
{
    header("Location: ../add_playground.php?error=missingvalues");
    exit();
}