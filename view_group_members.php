<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Group Members</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: auto;
            padding-top: 50px;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"], button {
            padding: 10px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {background-color: #f9f9f9;}
    </style>
</head>
<body>
    <div class="container">
        <h1>View Group Members</h1>
        <form action="view_group_members.php" method="post">
            <input type="text" name="group_name" placeholder="Enter group name" required>
            <button type="submit">View Members</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $host = 'localhost'; // Replace with your host
            $dbUsername = 'root'; // Replace with your database username
            $dbPassword = ''; // Replace with your database password
            $dbName = 'billbuddy'; // Replace with your database name

            // Create database connection
            $conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $groupName = $_POST['group_name'];

            $stmt = $conn->prepare("SELECT u.username FROM users u
                                     INNER JOIN user_group ug ON u.id = ug.user_id
                                     INNER JOIN groups g ON ug.group_id = g.group_id
                                     WHERE g.group_name = ?");
            $stmt->bind_param("s", $groupName);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                echo "<table><tr><th>Members of " . htmlspecialchars($groupName) . "</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . htmlspecialchars($row["username"]) . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "No members found for this group or group does not exist.";
            }
            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</body>
</html>
