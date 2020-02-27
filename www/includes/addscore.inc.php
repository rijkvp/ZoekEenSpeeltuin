<?php

session_start();
if (isset($_POST['addscore-submit']) && isset($_SESSION['userId']))
{
    require 'dbh.inc.php';

    $uid = $_SESSION['userUid'];

    $sql = "SELECT * FROM scores WHERE uidUsers=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        header("Location: ../login.php?error=sqlerror");
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "s", $uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);    
        if ($row = mysqli_fetch_assoc($result))
        {
            $sql = "UPDATE scores SET score=? WHERE uidUsers=?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql))
            {
                header("Location: ../login.php?error=sqlerror");
                exit();
            }
            else
            {
                $newscore = $row['score'] + 1;
                mysqli_stmt_bind_param($stmt, "ss", $newscore, $uid);
                mysqli_stmt_execute($stmt);
                $_SESSION['score'] = $newscore;
                header("Location: ../login.php");
                exit();
            }
        }
        else
        {
            $sql = "INSERT INTO scores (uidUsers, score) VALUES (?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql))
            {
                header("Location: ../login.php?error=sqlerror");
                exit();
            }
            else
            {
                $default_value = 1;
                mysqli_stmt_bind_param($stmt, "ss", $uid, $default_value);
                mysqli_stmt_execute($stmt);
                $_SESSION['score'] = $default_value;
                header("Location: ../login.php");
                exit();
            }
        }
    }
}
else
{
    header("Location: ../login.php");
    exit();
}