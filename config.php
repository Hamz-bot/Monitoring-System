<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "monitoring_system");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Functions to get user statistics
function getTotalUsers($conn) {
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getActiveUsers($conn) {
    $sql = "SELECT COUNT(*) as total FROM users WHERE activation = 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getInactiveUsers($conn) {
    $sql = "SELECT COUNT(*) as total FROM users WHERE activation = 0";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getAdminUsers($conn) {
    return 0; // Adjust based on your schema
}
?>