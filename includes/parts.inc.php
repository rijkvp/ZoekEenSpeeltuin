<?php

include "dbh.inc.php";

$query = "SELECT * FROM parts";
$result = $conn->query($query);     
if (!$result) {
  printf("Query failed: %s\n", $mysqli->error);
  exit;
}      

while($row = $result->fetch_row()) {
  $parts[]=$row;
}
