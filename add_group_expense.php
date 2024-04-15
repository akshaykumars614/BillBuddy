<?php
session_start();

// Redirect to login if not logged in or if username is not in the session
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'conn.php'; // Include your database connection configuration

// First, get the user_id from the username stored in the session
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Make sure we have a user_id
if (!$user_id) {
    echo "User not found.";
    exit();
}

// Now, fetch the groups for the logged-in user using the user_id
$stmt = $conn->prepare("SELECT g.group_id, g.group_name FROM groups g INNER JOIN user_group ug ON g.group_id = ug.group_id WHERE ug.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_id'], $_POST['amount'], $_POST['description'])) {
    // Validate and sanitize the input
    $group_id = filter_input(INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    // Insert the expense into the database
    $insert_stmt = $conn->prepare("INSERT INTO group_expenses (group_id, user_id, description, amount) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("iisd", $group_id, $user_id, $description, $amount);

    if ($insert_stmt->execute()) {
        echo "Expense added successfully!";
    } else {
        echo "Error adding expense: " . $insert_stmt->error;
    }

    $insert_stmt->close();
}
$stmt->close();

// Now, you can use $groups to display the list of groups to the user in HTML below
?>
<!-- HTML to display groups -->
<!DOCTYPE html>
<html lang="en">
<!-- ... -->
<body>
    <main>
    <div class="group-expense-box">
    <h2>Add Group Expense</h2>
    <ul>
        <?php foreach ($groups as $group): ?>
            <li>
                <!-- Here we make each group a link to the add_expense_to_group page with the group_id in the query string -->
                <a href="add_expense_to_group.php?group_id=<?php echo urlencode($group['group_id']); ?>">
                    <?php echo htmlspecialchars($group['group_name']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
        
    </main>
    
    <!-- ... -->
</body>
</html>
