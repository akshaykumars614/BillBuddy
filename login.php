<?php
// Database configuration
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'BillBuddy';

// Create database connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input from form
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and bind
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);

// Execute the query
$stmt->execute();

// Bind result variables
$stmt->bind_result($hashed_password);

// Fetch value
if ($stmt->fetch() && password_verify($password, $hashed_password)) {
    echo "Login successful!";
} else {
    echo "Login failed!";
}

$stmt->close();
$conn->close();
?>
