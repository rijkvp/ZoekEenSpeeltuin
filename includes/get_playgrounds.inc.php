<?php

include "dbh.inc.php";

$query = "SELECT * FROM playgrounds";
$result = $conn->query($query); 

if (!$result) {
    http_response_code(500);
    exit();
}      

while($row = $result->fetch_row()) {
    $playgrounds[]=$row;
}

$conn->close();
$result->close();

header('Content-Type: application/json');
echo json_encode($playgrounds);