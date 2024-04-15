<?php
require_once 'conn.php'; // Include the database configuration

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the username and passwords are set
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Proceed only if passwords match
        if ($password === $confirm_password) {
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
                if ($insert->execute()) {
                    echo "User registered successfully!";
                    // Include the email script here, make sure send_email.php is in the correct path
                    include 'send_email.php';
                } else {
                    // echo "Error: " . $conn->error;
                }
                $insert->close();
            }
            $stmt->close();
        } else {
            echo "Passwords do not match!";
        }
    } else {
        echo "Please fill in all required fields.";
    }
} else {
    echo "Invalid request method.";
}

?>
