<?php
// Include database configuration
require_once 'config.php';

// Check if user ID is provided
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $userId = mysqli_real_escape_string($conn, $userId);
    
    // Delete the user
    $deleteSql = "DELETE FROM users WHERE id = $userId";
    if ($conn->query($deleteSql) === TRUE) {
        header("Location: users.php?delete_success=1");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
} else {
    // No user ID provided, redirect back
    header("Location: users.php");
    exit();
}

// Close the database connection
$conn->close();
?>