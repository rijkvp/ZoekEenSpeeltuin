<?php

include "dbh.inc.php";

$query = "SELECT * FROM playgrounds";
$result = $conn->query($query);     
if (!$result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}      

while($row = $result->fetch_row()) {
  $playgrounds[]=$row;
}



$result->close();

$conn->close();