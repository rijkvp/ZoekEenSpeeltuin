<?php

$servername = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "database_website";

$conn = mysqli_connect($servername, $dBUsername, $dBPassword);

if($conn->connect_error) {
    die("Connection failed: ".mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS playgrounds ";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn -> select_db("playgrounds");

$sql = "CREATE TABLE IF NOT EXISTS playgrounds (
    id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(30),
    lat FLOAT,
    lng FLOAT
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabel voor speeltuinen succesvol aangemaakt.";
} else {
    die("Error creating table: " . $conn->error);
}

//$conn->close();