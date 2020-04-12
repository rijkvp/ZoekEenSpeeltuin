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
if (!$conn->query($sql)) {
    http_response_code(500);
    exit();
}

$conn -> select_db("playgrounds");

$sql = "CREATE TABLE IF NOT EXISTS parts_map (
    part_id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    item int(11),
)";

if (!$conn->query($sql)) {
    http_response_code(500);
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS parts (
    part_id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(30),
)";

if (!$conn->query($sql)) {
    http_response_code(500);
    exit();
}

$sql = "CREATE TABLE IF NOT EXISTS playgrounds (
    id int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(30),
    lat FLOAT,
    lng FLOAT
)";

if (!$conn->query($sql)) {
    http_response_code(500);
    exit();
}