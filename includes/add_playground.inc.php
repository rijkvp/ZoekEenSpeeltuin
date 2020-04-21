<?php

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(400);
    exit();
}

require 'dbh.inc.php';

$name = $_POST['name'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$ageFrom = $_POST['ageFrom'];
$ageTo = $_POST['ageTo'];
$rating = $_POST['rating'];
$nickname = $_POST['nickname'];
$comment = $_POST['comment'];

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (empty($name) || strlen($name)<4 || strlen($name)>20) {
    header("Location: ../add_playground.php?error=name");
    exit();
} else {
    $nickname = test_input($nickname);
}
if (empty($lat) || (float)$lat < 50 || (float)$lat > 54) {
    header("Location: ../add_playground.php?error=lat");
    exit();
} else {
    $lat = test_input($lat);
}

if (empty($lng) || (float)$lng < 2 || (float)$lng > 8) {
    header("Location: ../add_playground.php?error=lng");
    exit();
} else {
    $lng = test_input($lng);
}

if (empty($ageFrom) || (int)$ageFrom < 0 || (int)$ageFrom > 18) {
    http_response_code(400);
    exit();
} else {
    $ageFrom = test_input($ageFrom);
}

if (empty($ageTo) || (int)$ageTo < 0 || (int)$ageTo > 18) {
    http_response_code(400);
    exit();
} else {
    $ageTo = test_input($ageTo);
}

if ($ageFrom > $ageTo)
{
    header("Location: ../add_playground.php?error=age");
    exit();
}

if (empty($rating) || $rating < 1 || $rating > 5) {
    http_response_code(400);
    exit();
} else {
    $rating = test_input($rating);
}

if (empty($nickname) || strlen($nickname)<4 || strlen($nickname)>20) {
    header("Location: ../add_playground.php?error=nickname");
    exit();
} else {
    $nickname = test_input($nickname);
}

if (strlen($comment)>140) {
    header("Location: ../add_playground.php?error=comment");
    exit();
} else {
    $comment = test_input($comment);
}

if (isset($_POST['alwaysOpen']))
    $alwaysOpen = true;
else
    $alwaysOpen = false;

if (isset($_POST['cateringAvailable']))
    $cateringAvailable = true;
else
    $cateringAvailable = false;

$sql = "SELECT MAX(id) FROM playgrounds";
$result = $conn->query($sql); 
if (!$result) {
    http_response_code(500);
    exit();
}
$playgroundId = (int)($result -> fetch_row())[0] + 1;

// Upload the picture if set
if(isset($_FILES["pictureToUpload"]) && !empty($_FILES["pictureToUpload"])) {
    $target_dir = "../uploaded_pictures/";
    $target_file = $target_dir."playground".$playgroundId.".".pathinfo($_FILES["pictureToUpload"]["name"], PATHINFO_EXTENSION);
    $path_from_root = "uploaded_pictures/"."playground".$playgroundId.".".pathinfo($_FILES["pictureToUpload"]["name"], PATHINFO_EXTENSION);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["pictureToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
    if ($uploadOk == 1)
    {
        // Check if file already exists
        if (file_exists($target_file)) {
            http_response_code(500);
            exit();
        }
        // Check file size
        if ($_FILES["pictureToUpload"]["size"] > 2 * 1024 * 1024) { // Should be less than 2 MB
            header("Location: ../add_playground.php?error=picturesize");
            exit();
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            header("Location: ../add_playground.php?error=picturefiletype");
            exit();
        }

        if (move_uploaded_file($_FILES["pictureToUpload"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO pictures (playground_id, path) VALUES (?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql))
            {
                http_response_code(500);
                exit();
            }
            else
            {
                mysqli_stmt_bind_param($stmt, "ss", $playgroundId, $path_from_root);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        else {
            http_response_code(500);
            exit();
        }
    }    
}

foreach($_POST as $key => $value)
{
    if (strpos($key, 'part') === 0) 
    {
        if ($value != 0)
        {
            $partId = (int)substr($key,4,1);
            $partsToInsert[$partId] = (int)$value;
        }
    }
}
$uploadDate = date("Y-m-d");

$sql = "INSERT INTO playgrounds (name, lat, lng, age_from, age_to, always_open, catering_available, ip, upload_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql))
{
    die ("FAIL: ".$stmt->error);
    header("Location: ../add_playground.php?error=sqlerror");
    exit();
}
else
{
    mysqli_stmt_bind_param($stmt, "sssssssss", $name, $lat, $lng, $ageFrom, $ageTo, $alwaysOpen, $cateringAvailable, $ip, $uploadDate);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
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


$sql = "INSERT INTO reviews (playground_id, nickname, ip, rating, comment, review_date) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql))
{
    http_response_code(500);
    exit();
}
else
{
    mysqli_stmt_bind_param($stmt, "ssssss", $playgroundId, $nickname, $ip, $rating, $comment, $uploadDate);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


header("Location: ../playground.php?id=".$playgroundId);
$conn->close();