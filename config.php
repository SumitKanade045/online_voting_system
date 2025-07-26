<?php
$host = "127.0.0.1"; // Always use IP instead of "localhost" when using custom ports
$port = 3307;
$dbname = "college_src_voting";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
