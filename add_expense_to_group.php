<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'conn.php'; // Your DB connection file

class GroupManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getGroupMembers($groupId) {
        $stmt = $this->conn->prepare("SELECT u.id, u.username FROM users u INNER JOIN user_group ug ON u.id = ug.user_id WHERE ug.group_id = ?");
        $stmt->bind_param("i", $groupId);
        $stmt->execute();
        $result = $stmt->get_result();
        $members = [];
        while ($member = $result->fetch_assoc()) {
            $members[] = $member;
        }
        $stmt->close();
        return $members;
    }

    public function addExpenses($groupId, $description, $members) {
        $this->conn->begin_transaction();
        $error = false;

        foreach ($members as $member) {
            $member_amount = filter_input(INPUT_POST, 'amount_' . $member['id'], FILTER_VALIDATE_FLOAT);
            $member_id = $member['id'];

            if ($member_amount !== false && $member_amount > 0) {
                $stmt = $this->conn->prepare("INSERT INTO group_expenses (group_id, user_id, description, amount) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iisd", $groupId, $member_id, $description, $member_amount);
                if (!$stmt->execute()) {
                    echo "Error adding expense for user {$member['username']}: " . $stmt->error;
                    $error = true;
                    $stmt->close();
                    break;
                }
                $stmt->close();
            }
        }

        if ($error) {
            $this->conn->rollback();
            return false;
        } else {
            $this->conn->commit();
            return true;
        }
    }
}

$groupManager = new GroupManager($conn);
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
$members = $groupManager->getGroupMembers($group_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['description'])) {
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    if ($groupManager->addExpenses($group_id, $description, $members)) {
        echo "Expense added successfully.";
    } else {
        echo "An error occurred.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
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
