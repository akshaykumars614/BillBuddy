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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupName = $_POST['group_name'];
    $usernames = explode(',', $_POST['usernames']); // Split the usernames by commas
    $usernames = array_map('trim', $usernames); // Trim whitespace

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert group name into `groups` table
        $stmt = $conn->prepare("INSERT INTO groups (group_name) VALUES (?)");
        $stmt->bind_param("s", $groupName);
        $stmt->execute();
        $group_id = $stmt->insert_id; // Get the ID of the created group
        $stmt->close();

        // For each username, insert into `user_group` table
        $stmt = $conn->prepare("INSERT INTO user_group (user_id, group_id) SELECT id, ? FROM users WHERE username = ?");
        foreach ($usernames as $username) {
            $stmt->bind_param("is", $group_id, $username);
            $stmt->execute();
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();
        echo "Group created successfully with users added.";
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // Rollback the transaction on error
        echo "Error: " . $exception->getMessage();
    }
}

$conn->close();
?>
