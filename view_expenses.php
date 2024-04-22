<?php
session_start();

class UserManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserIdByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();
        return $userId;
    }
}

class ExpenseManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserExpenses($userId) {
        $stmt = $this->conn->prepare("SELECT description, amount, date FROM group_expenses WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $expenses = [];
        while ($expense = $result->fetch_assoc()) {
            $expenses[] = $expense;
        }
        $stmt->close();
        return $expenses;
    }
}

require_once 'conn.php'; // Include your database connection configuration

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$userManager = new UserManager($conn);
$expenseManager = new ExpenseManager($conn);

// Retrieve the user ID from the session
$user_id = $userManager->getUserIdByUsername($_SESSION['username']);

// Fetch the user's expenses
$expenses = $expenseManager->getUserExpenses($user_id);
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
