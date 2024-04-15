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

require_once 'conn.php'; // Include your database connection configuration


// Fetch the user's expenses
$stmt = $conn->prepare("SELECT description, amount, date FROM group_expenses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$expenses = [];
while ($expense = $result->fetch_assoc()) {
    $expenses[] = $expense;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Expenses - Bill-Buddy</title>
    <!-- Link to your stylesheet -->
</head>
<body>
    <main>
        <div class="container">
            <h1>My Expenses</h1>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($expense['description']); ?></td>
                        <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                        <td><?php echo htmlspecialchars($expense['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </main>
</body>
</html>
