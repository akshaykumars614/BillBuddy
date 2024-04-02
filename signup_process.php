<?php
// Database configuration
$host = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'billbuddy';

// Create database connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if ($password != $confirm_password) {
    echo "Passwords do not match!";
} else {
    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username already exists!";
    } else {
        // Hash password and insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insert->bind_param("ss", $username, $hashed_password);
        if ($insert->execute()) 
        {
            echo "User registered successfully!";
            include 'send_email.php';
            
        } else {
            echo "Error: " . $insert->error;
        }
        $insert->close();
    }
    $stmt->close();
}

$conn->close();
?>
