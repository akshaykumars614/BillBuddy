<?php session_start(); // Place this at the very top of the file ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill-Buddy - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Bill-Buddy</h1>
            <nav>
                <ul class="nav-links">
                    <!-- Navigation links go here -->
                </ul>
            </nav>
            
            <div class="auth-buttons">
                <?php if(isset($_SESSION['username'])): ?>
                    <!-- Display user's name and logout button if logged in -->
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="logout.php" class="button">Logout</a>
                <?php else: ?>
                    <!-- Display sign up and login buttons if not logged in -->
                    <a href="signup.html" class="button">Sign Up</a>
                    <a href="login.html" class="button">Login</a>
                <?php endif; ?>
                <a href="create_group.html" class="button">CG</a>
                <a href="view_group_members.php" class="button">VG</a>
            </div>
        </div>
    </header>
    <main>
        <div class="container">
            <div class="dashboard-box">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                <button onclick="location.href='add_personal_expense.php';">Add Personal Expense</button>
                <button onclick="location.href='add_group_expense.php';">Add Group Expense</button>
                <button onclick="location.href='view_expenses.php';">Add Group Expense</button>
                <!-- Additional content here -->
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Bill-Buddy. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
