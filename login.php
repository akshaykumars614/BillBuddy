<?php
session_start(); // Start the session at the very beginning

require_once 'conn.php'; // Include the database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assume the POST data exists and assign them to variables
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare and bind
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    // Execute the query
    $stmt->execute();

    // Bind result variables
    $stmt->bind_result($hashed_password);

    // Fetch the result
    if ($stmt->fetch()) {
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Store the username in the session
            $_SESSION['username'] = $username;

            // Redirect to index.php
            header('Location: index.php');
            exit();
        }
    }
    // If login is not successful
    echo "Login failed!";
    $stmt->close();
}

$conn->close();