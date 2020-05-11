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

if (empty($name) || strlen($name)<4 || strlen($name)>30) {
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

$updatePlayground = false;
if (isset($_POST['action']) && isset($_POST['updateId'])) {
    if ($_POST['action'] == "update")
    {
        // Check if user has rights to edit the playground
        $updateId = $_POST['updateId'];
        $sql = "SELECT ip FROM playgrounds WHERE id=".$updateId;
        $result = $conn->query($sql);
        if (!$result)
        {
            http_response_code(500);
            exit();
        }
        $playgroundIp = ($result->fetch_row())[0];
        if ($ip != $playgroundIp)
        {
            http_response_code(403); // Forbidden!
            exit();
        }

        $updatePlayground = true;
    }
}

if (!$updatePlayground)
{
    $sql = "SELECT lat, lng FROM playgrounds";
} else {
    $sql = "SELECT lat, lng FROM playgrounds WHERE NOT id=".$updateId;
}

$result = $conn->query($sql); 
if (!$result) {
    http_response_code(500);
    exit();
}

class Location {
    public $lat;
    public $lng;

    function __construct($lat, $lng) {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    function get_distance($otherLat, $otherLng) {
        // Pythagorean theorem
        // A*A+B*B=C*C 
        // C = SQRT(A*A + B*B)
        return sqrt(($this->lat - $otherLat) * ($this->lat - $otherLat)
         + ($this->lng - $otherLng) * ($this->lng - $otherLng));
    }
}

$newLocation = new Location($lat, $lng);

$locations = array();

while ($row = $result -> fetch_row()) {
    array_push($locations, new Location((float)$row[0], (float)$row[1]));
}

foreach($locations as $location)
{
    $dist = $location->get_distance($newLocation->lat, $newLocation->lng);
    if ($dist < 0.001) // < ~100m ?!
    {
        header("Location: ../add_playground.php?error=location");
        exit();
    }
}

if (!isset($ageFrom) || (int)$ageFrom < 0 || (int)$ageFrom > 18) {
    http_response_code(400);
    exit();
} else {
    $ageFrom = test_input($ageFrom);
}

if (!isset($ageTo) || (int)$ageTo < 0 || (int)$ageTo > 18) {
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

if (strlen($comment)>240) {
    http_response_code(400);
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

if (!$updatePlayground)
{
    $sql = "SELECT MAX(id) FROM playgrounds";
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $playgroundId = (int)($result -> fetch_row())[0] + 1;
}
else {
    $playgroundId = $updateId;
}


$sql = "SELECT path FROM pictures WHERE playground_id=".$playgroundId;
$result = $conn->query($sql);
if (!$result)
{
    http_response_code(500);
    exit();
}
$path = ($result->fetch_row())[0];
$pictureFound = true;
if (!isset($path) || empty($path))
{
    $pictureFound = false;
}

$uploadPicture = !$updatePlayground || !$pictureFound;

if ($updatePlayground && $pictureFound)
{
    // Remove file
    $sql = "DELETE FROM pictures WHERE path='".$path."'";
    if (!$conn->query($sql))
    {
        http_response_code(500);
        exit();
    }
    unlink("../".$path);
    $uploadPicture = true;
}
// Upload the picture if set
if ($uploadPicture)
{
    if(isset($_FILES["pictureToUpload"]["name"]) && !empty($_FILES["pictureToUpload"]["name"])) {
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
            header("Location: ../add_playground.php?error=picturefile");
            exit();
        }
        if ($uploadOk == 1)
        {
            // Check if file already exists
            if (file_exists($target_file)) {
                http_response_code(500);
                exit();
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                header("Location: ../add_playground.php?error=picturefiletype");
                exit();
            }
            // Check file size
            if ($_FILES["pictureToUpload"]["size"] > 12 * 1024 * 1024) { // Should be less than 12 MB
                header("Location: ../add_playground.php?error=picturesize");
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
}

foreach($_POST as $key => $value)
{
    if (strpos($key, 'part') !== false) 
    {
        if ($value != 0)
        {
            $partId = (int)substr($key,4);
            $partsToInsert[$partId] = (int)$value;
        }
    }
}

$uploadDate = date("Y-m-d");

if (!$updatePlayground)
{
    $sql = "INSERT INTO playgrounds (name, lat, lng, age_from, age_to, always_open, catering_available, ip, upload_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        http_response_code(500);
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "sssssssss", $name, $lat, $lng, $ageFrom, $ageTo, $alwaysOpen, $cateringAvailable, $ip, $uploadDate);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
} else {
    $sql = "UPDATE playgrounds SET name=?, lat=?, lng=?, age_from=?, age_to=?, always_open=?, catering_available=?, upload_date=? WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql))
    {  
        http_response_code(500);
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "sssssssss", $name, $lat, $lng, $ageFrom, $ageTo, $alwaysOpen, $cateringAvailable, $uploadDate, $updateId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }   
}

if ($updatePlayground) {
    $sql = "DELETE FROM parts_map WHERE playground_id=".$playgroundId;
    if (!$conn->query($sql))
    {
        http_response_code(500);
        exit();
    }
}

foreach($partsToInsert as $partId => $amount)
{
    $sql = "INSERT INTO parts_map (playground_id, part_id, amount) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        http_response_code(500);
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "sss", $playgroundId, $partId, $amount);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

if (!$updatePlayground)
{
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
} else {
    $sql = "UPDATE reviews SET nickname=?, comment=?, rating=?, review_date=? WHERE playground_id = ? AND ip = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql))
    {
        http_response_code(500);
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($stmt, "ssssss", $nickname, $comment, $rating, $uploadDate, $playgroundId, $ip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: ../playground.php?id=".$playgroundId);
$conn->close();