<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(400);
    exit();
}

if (!(isset($_GET['id']) && isset($_POST['nickname']) && isset($_POST['comment']) && isset($_POST['rating'])))
{
    http_response_code(400);
    exit();
}

require 'dbh.inc.php';

$playgroundId = $_GET['id'];
$nickname =     $_POST['nickname'];
$comment =      $_POST['comment'];
$rating =       (int)$_POST['rating'];

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (empty($nickname) || strlen($nickname)<4 || strlen($nickname)>20) {
    header("Location: ../playground.php?id=".$playgroundId."&error=nickname");
    exit();
} else {
    $nickname = test_input($nickname);
}

$sql = "SELECT nickname, ip FROM reviews";
$result = $conn->query($sql); 
while ($row = $result -> fetch_row()) {
    if ($row[0] == $nickname && $row[1] != $ip)
    {
        header("Location: ../playground.php?id=".$playgroundId."&error=nicknameused");
        exit();
    }
}

if (empty($comment) || strlen($comment)<18 || strlen($comment)>240) {
    header("Location: ../playground.php?id=".$playgroundId."&error=comment");
    exit();
} else {
    $comment = test_input($comment);
}

if (empty($rating) || $rating < 1 || $rating > 5) {
    http_response_code(400);
    exit();
} else {
    $rating = test_input($rating);
}

$sql = "SELECT ip FROM reviews WHERE playground_id=".$playgroundId;
$result = $conn->query($sql); 
while ($row = $result -> fetch_row()) {
    if ($row[0] == $ip)
    {
        http_response_code(403); // Forbidden! Only 1 review per IP to prevent spam
        exit();
    }
}

$sql = "INSERT INTO reviews (playground_id, ip, rating, nickname, comment, review_date) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_stmt_init($conn);

$date = date("Y-m-d");

if (!mysqli_stmt_prepare($stmt, $sql))
{
    http_response_code(500);
    exit();
}
else
{
    mysqli_stmt_bind_param($stmt, "ssssss", $playgroundId, $ip, $rating, $nickname, $comment, $date);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: ../playground.php?id=".$playgroundId);
$conn->close();