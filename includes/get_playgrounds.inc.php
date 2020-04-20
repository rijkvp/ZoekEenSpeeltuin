<?php

include "dbh.inc.php";

if (empty($_GET)) {
    $minRating = 1;
    $minParts = 0;
    $minAge = 0;
    $maxAge = 18;
    $alwaysOpen = false;
    $cateringAvailable = false;
}
else if (isset($_GET['minRating']) && isset($_GET['minParts']) && isset($_GET['minAge']) && isset($_GET['maxAge'])  
&& isset($_GET['alwaysOpen'])  && isset($_GET['cateringAvailable']))
{
    $minRating = $_GET['minRating'];
    $minParts = $_GET['minParts'];
    $minAge = $_GET['minAge'];
    $maxAge = $_GET['maxAge'];
    $alwaysOpen = $_GET['alwaysOpen'];
    $cateringAvailable = $_GET['cateringAvailable'];
}
else
{
    http_response_code(400);
    exit();
}

$query = "SELECT id, name, lat, lng, age_from, age_to FROM playgrounds";
$result = $conn->query($query); 

if (!$result) {
    http_response_code(500);
    exit();
}      

while($row = $result->fetch_row()) {
    $playgrounds[]=$row;
}

$sql = "SELECT name FROM parts";
$result = $conn->query($sql); 

if (!$result) {
    die("ERROR ".$conn -> error);
    //http_response_code(500);
    //exit();
}
while ($row = $result -> fetch_row()) {
    $parts[] = $row[0];
}

$length = count($playgrounds);
for ($x = $length-1; $x >= 0; $x--)
{
    $playgroundId = $playgrounds[$x][0];

    // GET THE SUM OF THE PARTS
    $playgroundId = $playgrounds[$x][0];
    $sql = "SELECT SUM(amount) FROM parts_map WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $partsCount = ($result -> fetch_row())[0];  

    // GET THE AVERAGE RATING
    $sql = "SELECT AVG(rating) FROM ratings WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $avgRating = ($result -> fetch_row())[0];
   
    $sql = "SELECT always_open FROM playgrounds WHERE id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $playgroundAlwaysOpen = ($result -> fetch_row())[0] == 1;
    $sql = "SELECT catering_available FROM playgrounds WHERE id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $playgroundCateringAvailable = ($result -> fetch_row())[0] == 1;

    if (!($partsCount >= $minParts && $avgRating >= $minRating))
    {
        unset($playgrounds[$x]);
    }
    if (!($alwaysOpen == false || $alwaysOpen == $playgroundAlwaysOpen))
    {
        unset($playgrounds[$x]);
    }
    if (!($cateringAvailable == false || $cateringAvailable == $playgroundCateringAvailable))
    {
        unset($playgrounds[$x]);
    }
}

$length = count($playgrounds);
for ($x = 0; $x < $length; $x++)
{
    $playgroundId = $playgrounds[$x][0];

    // CREATE A STRING OF THE PARTS
    $sql = "SELECT part_id, amount FROM parts_map WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $partString = "";
    while ($row = $result -> fetch_row()) {
        $partString .= $parts[$row[0] - 1]." (".$row[1]."), ";
    }

    // GET THE AVERAGE RATING
    $sql = "SELECT AVG(rating) FROM ratings WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $avgRating = ($result -> fetch_row())[0];

    // GET THE RATING COUNT
    $sql = "SELECT COUNT(rating) FROM ratings WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $ratingCount = ($result -> fetch_row())[0];
    
    array_push($playgrounds[$x], $partString);
    array_push($playgrounds[$x], $avgRating);
    array_push($playgrounds[$x], $ratingCount);
}

$conn->close();
$result->close();

header('Content-Type: application/json');
echo json_encode($playgrounds);