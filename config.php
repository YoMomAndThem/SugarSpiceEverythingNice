<?php
$host = "127.0.0.1";
$userName = "root";
$password = "studly2003";
$dbName = "baited";

// Create database connection
$conn = new mysqli($host, $userName, $password, $dbName);

// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
?>