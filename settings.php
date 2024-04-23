<?php
// Assuming you have a session started and user's name is stored in $_SESSION['username']

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the new name is provided
    if (!empty($_POST["new_name"])) {
        // Update the username in the session
        $_SESSION['username'] = $_POST["new_name"];
        // Optionally, you can save the new name to a database or file for permanent storage
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
</head>
<body>
    <h2>Settings</h2>
    <p>Name: <?php echo $_SESSION['username']; ?></p>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="new_name">Change Name:</label>
        <input type="text" id="new_name" name="new_name">
        <button type="submit">Save</button>
    </form>
</body>
</html>

