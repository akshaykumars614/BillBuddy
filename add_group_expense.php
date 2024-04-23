<?php
session_start();

interface IUserGroupService {
    public function getUserGroups($userId);
    public function addExpense($groupId, $userId, $description, $amount);
}

class DatabaseService {
    protected $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
}

class UserService extends DatabaseService {
    public function getUserId($username) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($userId);
        $stmt->fetch();
        $stmt->close();
        return $userId;
    }
}

class GroupService extends DatabaseService implements IUserGroupService {
    public function getUserGroups($userId) {
        $stmt = $this->conn->prepare("SELECT g.group_id, g.group_name FROM groups g INNER JOIN user_group ug ON g.group_id = ug.group_id WHERE ug.user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $groups = [];
        while ($row = $result->fetch_assoc()) {
            $groups[] = $row;
        }
        $stmt->close();
        return $groups;
    }

    public function addExpense($groupId, $userId, $description, $amount) {
        $stmt = $this->conn->prepare("INSERT INTO group_expenses (group_id, user_id, description, amount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisd", $groupId, $userId, $description, $amount);
        if ($stmt->execute()) {
            $result = "Expense added successfully!";
        } else {
            $result = "Error adding expense: " . $stmt->error;
        }
        $stmt->close();
        return $result;
    }
}

// Redirect to login if not logged in or if username is not in the session
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'conn.php'; // Include your database connection configuration
$userService = new UserService($conn);
$groupService = new GroupService($conn);

$user_id = $userService->getUserId($_SESSION['username']);
if (!$user_id) {
    echo "User not found.";
    exit();
}

$groups = $groupService->getUserGroups($user_id);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['group_id'], $_POST['amount'], $_POST['description'])) {
    $group_id = filter_input(INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    echo $groupService->addExpense($group_id, $user_id, $description, $amount);
}

?>
<!DOCTYPE html>
<html lang="en">
<body>
<main>
    <div class="group-expense-box">
        <h2>Add Group Expense</h2>
        <ul>
            <?php foreach ($groups as $group): ?>
                <li>
                    <a href="add_expense_to_group.php?group_id=<?php echo urlencode($group['group_id']); ?>">
                        <?php echo htmlspecialchars($group['group_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
</body>
</html>
