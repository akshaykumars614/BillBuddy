<?php
session_start();
unset($_SESSION['username']); // Remove the username from the session
session_destroy(); // Destroy the entire session
header('Location: index.php'); // Redirect to the home page
exit();
?>
