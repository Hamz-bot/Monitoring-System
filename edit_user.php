<?php
// Start output buffering to prevent headers already sent error
ob_start();

// Include database configuration
require_once 'config.php';

// Handle edit mode
$editMode = false;
$editUserData = null;
if (isset($_GET['id'])) {
    $editMode = true;
    $editUserId = $_GET['id'];
    $editUserId = mysqli_real_escape_string($conn, $editUserId);
    
    $sql = "SELECT * FROM users WHERE id = $editUserId";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $editUserData = $result->fetch_assoc();
    } else {
        // User not found, redirect back
        header("Location: users.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $name = $_POST['name'];
    $org = $_POST['org'];
    $email = $_POST['email'];
    $manager = $_POST['manager'];
    $date_registered = $_POST['date_registered'];
    $app = $_POST['app'];
    $activation = $_POST['activation'];
    $tracking = $_POST['tracking'];

    // Sanitize inputs to prevent SQL injection
    $userId = mysqli_real_escape_string($conn, $userId);
    $name = mysqli_real_escape_string($conn, $name);
    $org = mysqli_real_escape_string($conn, $org);
    $email = mysqli_real_escape_string($conn, $email);
    $manager = mysqli_real_escape_string($conn, $manager);
    $date_registered = mysqli_real_escape_string($conn, $date_registered);
    $app = mysqli_real_escape_string($conn, $app);
    
    $sql = "UPDATE users SET 
            name = '$name', 
            org = '$org', 
            email = '$email', 
            manager = '$manager', 
            date_registered = '$date_registered', 
            app = '$app', 
            activation = $activation, 
            tracking = $tracking 
            WHERE id = $userId";

    if ($conn->query($sql) === TRUE) {
        header("Location: users.php?update_success=1");
        exit();
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | WordPress Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4b6cb7;
            --secondary: #182848;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 5px;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: bold;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .form-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .btn-wp {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
        }
        
        .btn-wp:hover {
            background: linear-gradient(to right, var(--secondary), var(--primary));
            color: white;
        }
        
        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .notification-badge {
            position: relative;
        }
        
        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }
        
        .cancel-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-none d-md-block">
                <div class="text-center mb-4">
                    <h3><i class="fab fa-wordpress"></i> WP Manager</h3>
                </div>
                <ul class="nav flex-column" id="sidebarLinks">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-plus-circle"></i> Add Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-plug"></i> Plugin Updates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="users.php">
                            <i class="fas fa-user-lock"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-key"></i> Password Manager
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <div class="header d-flex justify-content-between align-items-center">
                    <h2>Edit User</h2>
                    <div class="d-flex">
                        <div class="notification-badge mx-3">
                            <i class="fas fa-bell fa-lg text-muted"></i>
                            <span class="badge bg-danger">3</span>
                        </div>
                        <div class="user-info">
                            <img src="https://ui-avatars.com/api/?name=Admin+User&background=4b6cb7&color=fff" class="rounded-circle" width="40" height="40" alt="User">
                            <span class="ms-2">Admin User</span>
                        </div>
                    </div>
                </div>
                
                <!-- Edit User Form -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card form-card">
                            <div class="card-header bg-white">
                                <h5>Edit User</h5>
                            </div>
                            <div class="card-body">
                                <form id="editUserForm" method="POST" action="">
                                    <input type="hidden" name="user_id" value="<?php echo $editUserData['id']; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($editUserData['name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="org" class="form-label">Organization</label>
                                                <input type="text" class="form-control" id="org" name="org" value="<?php echo htmlspecialchars($editUserData['org']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($editUserData['email']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="manager" class="form-label">Manager</label>
                                                <input type="text" class="form-control" id="manager" name="manager" value="<?php echo htmlspecialchars($editUserData['manager']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="date_registered" class="form-label">Activation Date</label>
                                                <input type="date" class="form-control" id="date_registered" name="date_registered" value="<?php echo htmlspecialchars($editUserData['date_registered']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="app" class="form-label">App Version</label>
                                                <select class="form-select" id="app" name="app" required>
                                                    <option value="">Select Version</option>
                                                    <option value="Win(l): 2.0.9" <?php echo ($editUserData['app'] == 'Win(l): 2.0.9') ? 'selected' : ''; ?>>Win(l): 2.0.9</option>
                                                    <option value="Win(l): 2.1.0" <?php echo ($editUserData['app'] == 'Win(l): 2.1.0') ? 'selected' : ''; ?>>Win(l): 2.1.0</option>
                                                    <option value="Mac(l): 2.0.9" <?php echo ($editUserData['app'] == 'Mac(l): 2.0.9') ? 'selected' : ''; ?>>Mac(l): 2.0.9</option>
                                                    <option value="Mac(l): 2.1.0" <?php echo ($editUserData['app'] == 'Mac(l): 2.1.0') ? 'selected' : ''; ?>>Mac(l): 2.1.0</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="activation" class="form-label">Activation</label>
                                                <select class="form-select" id="activation" name="activation" required>
                                                    <option value="1" <?php echo ($editUserData['activation'] == 1) ? 'selected' : ''; ?>>Active</option>
                                                    <option value="0" <?php echo ($editUserData['activation'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="tracking" class="form-label">Tracking</label>
                                                <select class="form-select" id="tracking" name="tracking" required>
                                                    <option value="1" <?php echo ($editUserData['tracking'] == 1) ? 'selected' : ''; ?>>On</option>
                                                    <option value="0" <?php echo ($editUserData['tracking'] == 0) ? 'selected' : ''; ?>>Off</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-wp w-100">Update User</button>
                                            <a href="users.php" class="btn btn-outline-secondary w-100 cancel-btn">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Main Content -->
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script>
        // Sidebar active highlighting
        const currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('#sidebarLinks .nav-link').forEach(link => {
            if (link.getAttribute('href') === 'users.php') {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
<?php
// End output buffering
ob_end_flush();
?>