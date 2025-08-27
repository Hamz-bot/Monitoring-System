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
                        <a class="nav-link" href="index.php">
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
                
                <!-- User Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Total Users</h5>
                            <div class="stat-number text-primary"><?php echo getTotalUsers(); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5>Active Users</h5>
                            <div class="stat-number text-success"><?php echo getActiveUsers(); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <h5>Inactive Users</h5>
                            <div class="stat-number text-warning"><?php echo getInactiveUsers(); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h5>Admin Users</h5>
                            <div class="stat-number text-info"><?php echo getAdminUsers(); ?></div>
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
                                            // Database connection
                                            $servername = "localhost";
                                            $username = "root"; // Your DB username
                                            $password = ""; // Your DB password
                                            $dbname = "monitoring_system";

                                            // Create connection
                                            $conn = new mysqli($servername, $username, $password, $dbname);

                                            // Check connection
                                            if ($conn->connect_error) {
                                                die("Connection failed: " . $conn->connect_error);
                                            }

                                            // Fetch users data
                                            $sql = "SELECT * FROM users";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
                                                // Output data of each row
                                                while($row = $result->fetch_assoc()) {
                                                    // Convert activation from 1/0 to Active/Inactive
                                                    $activationStatus = ($row["activation"] == 1) ? "Active" : "Inactive";
                                                    $activationBadge = ($row["activation"] == 1) ? "bg-success" : "bg-danger";
                                                    
                                                    // Convert tracking from 1/0 to On/Off
                                                    $trackingStatus = ($row["tracking"] == 1) ? "On" : "Off";
                                                    $trackingBadge = ($row["tracking"] == 1) ? "bg-success" : "bg-danger";
                                                    
                                                    echo "<tr>
                                                        <td>
                                                            <div class='d-flex align-items-center'>
                                                                <img src='https://ui-avatars.com/api/?name=" . urlencode($row["name"]) . "&background=4b6cb7&color=fff' class='user-avatar me-2' alt='User'>
                                                                <div>" . $row["name"] . "</div>
                                                            </div>
                                                        </td>
                                                        <td>" . $row["email"] . "</td>
                                                        <td>" . $row["org"] . "</td>
                                                        <td>" . $row["manager"] . "</td>
                                                        <td>" . $row["date_registered"] . "</td>
                                                        <td>" . $row["app"] . "</td>
                                                        <td><span class='badge " . $activationBadge . " badge-activation'>" . $activationStatus . "</span></td>
                                                        <td><span class='badge " . $trackingBadge . " badge-tracking'>" . $trackingStatus . "</span></td>
                                                        <td>
                                                            <button class='action-btn' onclick='editUser(" . $row["id"] . ")'>
                                                                <i class='fas fa-edit'></i>
                                                            </button>
                                                            <button class='action-btn' onclick='deleteUser(" . $row["id"] . ")'>
                                                                <i class='fas fa-trash'></i>
                                                            </button>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='9' class='text-center'>No users found</td></tr>";
                                            }
                                            $conn->close();
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
        
        // Edit user function
        function editUser(userId) {
            alert('Editing user with ID: ' + userId);
            // Here you would typically open a modal or redirect to an edit page
        }
        
        // Delete user function
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Here you would typically send an AJAX request to delete the user
                alert('User with ID ' + userId + ' has been deleted');
                // Refresh the page or remove the row from the table
                location.reload();
            }
        }
        
        // Refresh table function
        function refreshTable() {
            location.reload();
        }
    </script>
    
    <?php
    // Functions to get user statistics
    function getTotalUsers() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "monitoring_system";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $sql = "SELECT COUNT(*) as total FROM users";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        
        return $row['total'];
    }
    
    function getActiveUsers() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "monitoring_system";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $sql = "SELECT COUNT(*) as total FROM users WHERE activation = 1";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        
        return $row['total'];
    }
    
    function getInactiveUsers() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "monitoring_system";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $sql = "SELECT COUNT(*) as total FROM users WHERE activation = 0";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        
        return $row['total'];
    }
    
    function getAdminUsers() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "monitoring_system";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Count users with specific org names that might be considered admin
        $sql = "SELECT COUNT(*) as total FROM users WHERE org IN ('Admin', 'Management', 'Executive')";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $conn->close();
        
        return $row['total'];
    }
    ?>
</body>
</html>