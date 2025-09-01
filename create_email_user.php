<?php
require_once 'config.php';

// Check if user with this email already exists
$email = "admin@example.com";
$check_sql = "SELECT id FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing user's password
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $update_sql = "UPDATE users SET password = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", $password, $email);
    
    if ($update_stmt->execute()) {
        echo "SUCCESS: Password updated for existing user!<br>";
        echo "Email: admin@example.com<br>";
        echo "Password: admin123";
    } else {
        echo "ERROR: " . $conn->error;
    }
} else {
    // Create new user if doesn't exist - only using columns that exist
    $name = "Admin User";
    $org = "Admin Organization";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $activation = 1;

    // Try inserting without username column
    $sql = "INSERT INTO users (name, email, password, org, activation) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $password, $org, $activation);

    if ($stmt->execute()) {
        echo "SUCCESS: User created!<br>";
        echo "Email: admin@example.com<br>";
        echo "Password: admin123";
    } else {
        echo "ERROR: " . $conn->error;
    }
}
?>