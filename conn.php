<?php
$servername = "localhost"; // or your server's hostname
$username = "root";
$password = "";
$database = "billbuddy";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";
$conn->close();
?>
