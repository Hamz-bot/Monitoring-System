<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect to login if not authenticated
function requireAuth() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit();
    }
}

// Login user by email
function loginByEmail($email, $password, $conn) {
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify password (assuming passwords are hashed with password_hash)
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            
            // Try to log activity (skip if table doesn't exist)
            try {
                logActivity($user['id'], 'login', 'User logged in', $_SERVER['REMOTE_ADDR']);
            } catch (Exception $e) {
                // Skip logging if table doesn't exist
            }
            
            return true;
        }
    }
    return false;
}

// Logout user
function logout() {
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        try {
            logActivity($user_id, 'logout', 'User logged out', $_SERVER['REMOTE_ADDR']);
        } catch (Exception $e) {
            // Skip logging if table doesn't exist
        }
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}

// Log user activity (with error handling for missing table)
function logActivity($user_id, $activity_type, $description, $ip_address = null) {
    global $conn;
    
    // Check if activity_log table exists
    $tableExists = false;
    $result = $conn->query("SHOW TABLES LIKE 'activity_log'");
    if ($result->num_rows > 0) {
        $tableExists = true;
    }
    
    // Only log if table exists
    if ($tableExists) {
        $ip = $ip_address ?? $_SERVER['REMOTE_ADDR'];
        $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity_type, description, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $activity_type, $description, $ip);
        $stmt->execute();
    }
}

// Get current user details
function getCurrentUser($conn) {
    if (!isLoggedIn()) return null;
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}
?>