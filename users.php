<?php
// Start output buffering to prevent headers already sent error
ob_start();
// Include database configuration
require_once 'config.php';

// Handle edit request
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_id = mysqli_real_escape_string($conn, $edit_id);
    
    // Fetch the user data
    $sql = "SELECT * FROM users WHERE id = $edit_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $edit_user = $result->fetch_assoc();
    } else {
        echo "User not found";
        exit();
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_id = mysqli_real_escape_string($conn, $delete_id);
    
    $sql = "DELETE FROM users WHERE id = $delete_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?delete_success=1");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $name = $_POST['name'];
    $org = $_POST['org'];
    $email = $_POST['email'];
    $manager = $_POST['manager'];
    $date_registered = $_POST['date_registered'];
    $app = $_POST['app'];
    $activation = $_POST['activation'];
    $tracking = $_POST['tracking'];
    
    // Sanitize inputs
    $update_id = mysqli_real_escape_string($conn, $update_id);
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
            WHERE id = $update_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?update_success=1");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Handle form submission for adding new users
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update_id'])) {
    $name = $_POST['name'];
    $org = $_POST['org'];
    $email = $_POST['email'];
    $manager = $_POST['manager'];
    $date_registered = $_POST['date_registered'];
    $app = $_POST['app'];
    $activation = $_POST['activation'];
    $tracking = $_POST['tracking'];
    
    // Sanitize inputs to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $name);
    $org = mysqli_real_escape_string($conn, $org);
    $email = mysqli_real_escape_string($conn, $email);
    $manager = mysqli_real_escape_string($conn, $manager);
    $date_registered = mysqli_real_escape_string($conn, $date_registered);
    $app = mysqli_real_escape_string($conn, $app);
    
    $sql = "INSERT INTO users (name, org, email, manager, date_registered, app, activation, tracking) 
            VALUES ('$name', '$org', '$email', '$manager', '$date_registered', '$app', $activation, $tracking)";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?add_success=1");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | WordPress Manager Dashboard</title>
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
        
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .wp-table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .wp-table thead {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
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
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        
        .action-btn {
            border: none;
            background: none;
            color: #6c757d;
            padding: 5px;
            margin: 0 2px;
        }
        
        .action-btn:hover {
            color: var(--primary);
        }
        
        .badge-activation {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        
        .badge-tracking {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
        
        .form-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .success-message {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        /* Dropdown menu styling */
        .dropdown-menu {
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            border: none;
            padding: 0;
            margin: 0;
            border-radius: 0;
        }
        
        .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin: 0;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .dropdown-item i {
            margin-right: 10px;
        }
        
        .dropdown-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 0;
        }
    </style>
    <meta name="user-id" content="<?php echo $_SESSION['user_id']; ?>">
<script src="screen_capture.js"></script>
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
                        <a class="nav-link active" href="users.php">
                            <i class="fas fa-user-lock"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="live_tracking.php" id="liveTrackingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-satellite-dish"></i> Live Tracking
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="liveTrackingDropdown">
                            <li><a class="dropdown-item" href="screenshots.php"><i class="fas fa-camera"></i> Screenshots</a></li>
                            <li><a class="dropdown-item" href="live_stream.php"><i class="fas fa-video"></i> Live Stream</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-tasks"></i> Projects & Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-file-invoice-dollar"></i> Pricing & Billing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-clock"></i> Time Claim
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-hourglass-half"></i> Time Entry
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-calendar-alt"></i> Holiday
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
                    <h2>User Management</h2>
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
                
                <!-- Success Messages -->
                <div class="alert alert-success success-message" id="addSuccessMessage">
                    <i class="fas fa-check-circle"></i> User added successfully!
                </div>
                
                <div class="alert alert-success success-message" id="updateSuccessMessage">
                    <i class="fas fa-check-circle"></i> User updated successfully!
                </div>
                
                <div class="alert alert-success success-message" id="deleteSuccessMessage">
                    <i class="fas fa-check-circle"></i> User deleted successfully!
                </div>
                
                <!-- User Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Total Users</h5>
                            <div class="stat-number text-primary"><?php echo getTotalUsers($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5>Active Users</h5>
                            <div class="stat-number text-success"><?php echo getActiveUsers($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <h5>Inactive Users</h5>
                            <div class="stat-number text-warning"><?php echo getInactiveUsers($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h5>Admin Users</h5>
                            <div class="stat-number text-info"><?php echo getAdminUsers($conn); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Add/Edit User Form -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card form-card">
                            <div class="card-header bg-white">
                                <h5><?php echo isset($edit_user) ? 'Edit User' : 'Add New User'; ?></h5>
                            </div>
                            <div class="card-body">
                                <form id="addUserForm" method="POST" action="">
                                    <?php if (isset($edit_user)): ?>
                                        <input type="hidden" name="update_id" value="<?php echo $edit_user['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name" 
                                                    value="<?php echo isset($edit_user) ? $edit_user['name'] : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="org" class="form-label">Organization</label>
                                                <input type="text" class="form-control" id="org" name="org" 
                                                    value="<?php echo isset($edit_user) ? $edit_user['org'] : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                    value="<?php echo isset($edit_user) ? $edit_user['email'] : ''; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="manager" class="form-label">Manager</label>
                                                <input type="text" class="form-control" id="manager" name="manager" 
                                                    value="<?php echo isset($edit_user) ? $edit_user['manager'] : 'Umar Malik'; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="date_registered" class="form-label">Activation Date</label>
                                                <input type="date" class="form-control" id="date_registered" name="date_registered" 
                                                    value="<?php echo isset($edit_user) ? $edit_user['date_registered'] : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="app" class="form-label">App Version</label>
                                                <select class="form-select" id="app" name="app" required>
                                                    <option value="">Select Version</option>
                                                    <option value="Win(l): 2.0.9" <?php echo (isset($edit_user) && $edit_user['app'] == 'Win(l): 2.0.9') ? 'selected' : ''; ?>>Win(l): 2.0.9</option>
                                                    <option value="Win(l): 2.1.0" <?php echo (isset($edit_user) && $edit_user['app'] == 'Win(l): 2.1.0') ? 'selected' : ''; ?>>Win(l): 2.1.0</option>
                                                    <option value="Mac(l): 2.0.9" <?php echo (isset($edit_user) && $edit_user['app'] == 'Mac(l): 2.0.9') ? 'selected' : ''; ?>>Mac(l): 2.0.9</option>
                                                    <option value="Mac(l): 2.1.0" <?php echo (isset($edit_user) && $edit_user['app'] == 'Mac(l): 2.1.0') ? 'selected' : ''; ?>>Mac(l): 2.1.0</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="activation" class="form-label">Activation</label>
                                                <select class="form-select" id="activation" name="activation" required>
                                                    <option value="1" <?php echo (isset($edit_user) && $edit_user['activation'] == 1) ? 'selected' : ''; ?>>Active</option>
                                                    <option value="0" <?php echo (isset($edit_user) && $edit_user['activation'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="tracking" class="form-label">Tracking</label>
                                                <select class="form-select" id="tracking" name="tracking" required>
                                                    <option value="1" <?php echo (isset($edit_user) && $edit_user['tracking'] == 1) ? 'selected' : ''; ?>>On</option>
                                                    <option value="0" <?php echo (isset($edit_user) && $edit_user['tracking'] == 0) ? 'selected' : ''; ?>>Off</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-wp w-100">
                                                <?php echo isset($edit_user) ? 'Update User' : 'Add User'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5>Registered Users</h5>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                    <button class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-export"></i> Export
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover wp-table">
                                        <thead>
                                            <tr>
                                                <th>NAME</th>
                                                <th>EMAIL</th>
                                                <th>ORG</th>
                                                <th>MANAGER</th>
                                                <th>ACTIVATION DATE</th>
                                                <th>APP VERSION</th>
                                                <th>ACTIVATION</th>
                                                <th>TRACKING</th>
                                                <th>ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Fetch users from database
                                            $sql = "SELECT * FROM users ORDER BY id DESC";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
                                                while($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row["name"] . "</td>";
                                                    echo "<td>" . $row["email"] . "</td>";
                                                    echo "<td>" . $row["org"] . "</td>";
                                                    echo "<td>" . $row["manager"] . "</td>";
                                                    echo "<td>" . $row["date_registered"] . "</td>";
                                                    echo "<td>" . $row["app"] . "</td>";
                                                    
                                                    // Activation status badge
                                                    if ($row["activation"] == 1) {
                                                        echo "<td><span class='badge bg-success badge-activation'>Active</span></td>";
                                                    } else {
                                                        echo "<td><span class='badge bg-warning badge-activation'>Inactive</span></td>";
                                                    }
                                                    
                                                    // Tracking status badge
                                                    if ($row["tracking"] == 1) {
                                                        echo "<td><span class='badge bg-success badge-tracking'>On</span></td>";
                                                    } else {
                                                        echo "<td><span class='badge bg-danger badge-tracking'>Off</span></td>";
                                                    }
                                                    
                                                    // Actions buttons
                                                    echo "<td>";
                                                    echo "<button class='action-btn' onclick='editUser(" . $row["id"] . ")'><i class='fas fa-edit'></i></button>";
                                                    echo "<button class='action-btn' onclick='copyUser(" . $row["id"] . ")'><i class='fas fa-copy'></i></button>";
                                                    echo "<button class='action-btn' onclick='deleteUser(" . $row["id"] . ")'><i class='fas fa-trash'></i></button>";
                                                    echo "</td>";
                                                    
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='9' class='text-center'>No users found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fix dropdown menu links
        document.addEventListener('DOMContentLoaded', function() {
            // Get all dropdown items
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            
            // Add click event listener to each dropdown item
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Prevent default behavior
                    e.preventDefault();
                    
                    // Get the href attribute
                    const href = this.getAttribute('href');
                    
                    // Navigate to the href
                    window.location.href = href;
                });
            });
            
            // Check for success messages
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('add_success') === '1') {
                document.getElementById('addSuccessMessage').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('addSuccessMessage').style.display = 'none';
                }, 3000);
            }
            
            if (urlParams.get('update_success') === '1') {
                document.getElementById('updateSuccessMessage').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('updateSuccessMessage').style.display = 'none';
                }, 3000);
            }
            
            if (urlParams.get('delete_success') === '1') {
                document.getElementById('deleteSuccessMessage').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('deleteSuccessMessage').style.display = 'none';
                }, 3000);
            }
        });

        // Function to refresh the table
        function refreshTable() {
            location.reload();
        }

        // Function to edit a user
        function editUser(userId) {
            // Redirect to edit page or load user data in the form
            window.location.href = 'users.php?edit_id=' + userId;
        }

        // Function to copy a user
        function copyUser(userId) {
            // Fetch user data and pre-fill the form
            fetch('get_user.php?id=' + userId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('name').value = data.name;
                    document.getElementById('org').value = data.org;
                    document.getElementById('email').value = data.email + '_copy'; // Append _copy to email
                    document.getElementById('manager').value = data.manager;
                    document.getElementById('date_registered').value = data.date_registered;
                    document.getElementById('app').value = data.app;
                    document.getElementById('activation').value = data.activation;
                    document.getElementById('tracking').value = data.tracking;
                    
                    // Scroll to form
                    document.getElementById('addUserForm').scrollIntoView({ behavior: 'smooth' });
                })
                .catch(error => console.error('Error:', error));
        }

        // Function to delete a user
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'users.php?delete_id=' + userId;
            }
        }
    </script>
</body>
</html>