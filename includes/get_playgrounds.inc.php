<?php

include "dbh.inc.php";

if (empty($_GET)) {
    $minRating = 1;
    $minParts = 0;
    $minAge = 0;
    $maxAge = 18;
    $alwaysOpen = false;
    $cateringAvailable = false;
    $requiredParts = array();
}
else if (isset($_GET['minRating']) && isset($_GET['minParts']) && isset($_GET['minAge']) && isset($_GET['maxAge'])  
&& isset($_GET['alwaysOpen'])  && isset($_GET['cateringAvailable']))
{
    $minRating = (float)$_GET['minRating'];
    $minParts = (int)$_GET['minParts'];
    $minAge = (int)$_GET['minAge'];
    $maxAge = (int)$_GET['maxAge'];
    $alwaysOpen = $_GET['alwaysOpen'] == 'true';
    $cateringAvailable = $_GET['cateringAvailable'] == 'true';
    $requiredPartsStr = (string)$_GET['requiredParts'];
    if (substr("$requiredPartsStr", -1) == "%")
    {
        $requiredPartsStr = substr($requiredPartsStr, 0, -1);
    }
    $requiredPartsStrArray = explode("%", $requiredPartsStr);
    if (empty($requiredPartsStrArray[0]))
    {
        unset($requiredPartsStrArray);
        $requiredPartsStrArray = array();
    }    
    $requiredParts = array();
    foreach($requiredPartsStrArray as $part)
    {
        array_push($requiredParts, (int)$part);
    }
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
    http_response_code(500);
    exit();
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
    $sql = "SELECT AVG(rating) FROM reviews WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $avgRating = ($result -> fetch_row())[0];
   
    $sql = "SELECT always_open, catering_available, age_from, age_to FROM playgrounds WHERE id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $row = ($result -> fetch_row());
    $playgroundAlwaysOpen = $row[0] == 1;
    $playgroundCateringAvailable = $row[1] == 1;
    $ageFrom = (int)$row[2];
    $ageTo = (int)$row[3];

    if (!($partsCount >= $minParts && $avgRating >= $minRating))
    {
        unset($playgrounds[$x]);
        continue;
    }
    if ($alwaysOpen && !$playgroundAlwaysOpen)
    {
        unset($playgrounds[$x]);
        continue;
    }
    if ($cateringAvailable && !$playgroundCateringAvailable)
    {
        unset($playgrounds[$x]);
        continue;
    }
    if ($minAge > $ageTo || $maxAge < $ageFrom)
    {
        unset($playgrounds[$x]);
        continue;
    }
    $sql = "SELECT part_id FROM parts_map WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $partIds = array();
    while ($row = $result -> fetch_row())
    {
        array_push($partIds, (int)$row[0]); 
    }

    foreach($requiredParts as $requiredPart)
    {
        if (!in_array($requiredPart, $partIds))
        {
            unset($playgrounds[$x]);
            break;
        }
    }
}
$playgrounds = array_values($playgrounds);  // Make sure its an indexed array somtimes it becomes an associative array. 
                                            // This ensures its an indexed array.

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
        $partString .= $row[1]."x ".$parts[$row[0] - 1].", ";
    }
    $partString = substr($partString, 0, -2);
    
    // GET THE AVERAGE RATING
    $sql = "SELECT AVG(rating) FROM reviews WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $avgRating = ($result -> fetch_row())[0];

    // GET THE RATING COUNT
    $sql = "SELECT COUNT(rating) FROM reviews WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $ratingCount = ($result -> fetch_row())[0];

    // GET THE PATH IF THERE IS
    $sql = "SELECT path FROM pictures WHERE playground_id=".$playgroundId;
    $result = $conn->query($sql); 
    if (!$result) {
        http_response_code(500);
        exit();
    }
    $picturePath = ($result -> fetch_row())[0];
    if (empty($picturePath) || !isset($picturePath))
        $picturePath = NULL;

    array_push($playgrounds[$x], $partString);
    array_push($playgrounds[$x], $avgRating);
    array_push($playgrounds[$x], $ratingCount);
    array_push($playgrounds[$x], $picturePath);
}

$conn->close();
$result->close();


header('Content-Type: application/json');
echo json_encode($playgrounds);