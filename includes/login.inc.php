<?php

if (isset($_POST['login-submit']))
{
    require 'dbh.inc.php';

    $uid = $_POST['uid'];
    $password = $_POST['pwd'];

    if (empty($uid) || empty($password))
    {
        header("Location: ../login.php?error=emptyfields&uid=".$uid);
        exit();
    }
    else
    {
        $sql = "SELECT * FROM users WHERE uidUsers=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql))
        {
            header("Location: ../login.php?error=sqlerror");
            // Use die() when you have errors!
            exit();
        }
        else
        {
            mysqli_stmt_bind_param($stmt, "s", $uid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);    
            if ($row = mysqli_fetch_assoc($result))
            {
                $pwdCheck = password_verify($password, $row['pwdUsers']);
                if ($pwdCheck == false)
                {
                    header("Location: ../login.php?error=wrongpwd&uid=".$uid);
                    exit();
                }
                else if ($pwdCheck == true)
                {
                    // Password is correct
                    session_start();
                    $_SESSION['userId'] = $row['idUsers'];
                    $_SESSION['userUid'] = $row['uidUsers'];

                    header("Location: ../login.php?login=success");
                    exit();
                }
                else
                {
                    header("Location: ../login.php?error=wrongpwd&uid=".$uid);
                    exit();
                }
            }
            else
            {
                header("Location: ../login.php?error=nouser");
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