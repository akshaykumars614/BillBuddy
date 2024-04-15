<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'conn.php'; // Your DB connection file

// ... existing code to get $user_id ...

// Initialize group_id from the GET parameter
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// Fetch all users in this group
$members_stmt = $conn->prepare("SELECT u.id, u.username FROM users u INNER JOIN user_group ug ON u.id = ug.user_id WHERE ug.group_id = ?");
$members_stmt->bind_param("i", $group_id);
$members_stmt->execute();
$members_result = $members_stmt->get_result();

$members = [];
while ($member = $members_result->fetch_assoc()) {
    $members[] = $member;
}
$members_stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the shared description
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    
    // Start transaction
    $conn->begin_transaction();

    $error = false;
    foreach ($members as $member) {
        // Validate and sanitize each member's input
        $member_amount = filter_input(INPUT_POST, 'amount_'.$member['id'], FILTER_VALIDATE_FLOAT);
        $member_id = $member['id'];

        if ($member_amount !== false && $member_amount > 0) {
            // Prepare an insert statement for each member
            $stmt = $conn->prepare("INSERT INTO group_expenses (group_id, user_id, description, amount) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisd", $group_id, $member_id, $description, $member_amount);

            // Execute the statement and check for success
            if (!$stmt->execute()) {
                echo "Error adding expense for user {$member['username']}: " . $stmt->error;
                $error = true;
                break; // Exit the loop on error
            }
            $stmt->close();
        }
    }

    if ($error) {
        // Rollback transaction on error
        $conn->rollback();
    } else {
        // Commit transaction on success
        $conn->commit();
        echo "Expense added successfully.";
    }
}

// Continue with the HTML form...
?>
<!DOCTYPE html>
<html lang="en">
<!-- ... rest of the head ... -->
<body>
    <main>
        <div class="container">
            <form action="add_expense_to_group.php?group_id=<?php echo urlencode($group_id); ?>" method="post">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required>
                <br>
                <?php foreach ($members as $member): ?>
                    <label for="amount_<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['username']); ?>'s share:</label>
                    <input type="number" step="0.01" id="amount_<?php echo $member['id']; ?>" name="amount_<?php echo $member['id']; ?>" required>
                    <br>
                <?php endforeach; ?>
                <input type="submit" value="Split Expense">
            </form>
        </div>
    </main>
</body>
</html>
