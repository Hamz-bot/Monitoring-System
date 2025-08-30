<?php
// Start output buffering to prevent headers already sent error
ob_start();
// Include database configuration
require_once 'config.php';

// Functions to get tracking statistics
function getTotalScreenshots($conn) {
    try {
        $sql = "SELECT COUNT(*) as total FROM screenshots";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getTodayScreenshots($conn) {
    try {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM screenshots WHERE DATE(timestamp) = '$today'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getActiveStreams($conn) {
    try {
        $sql = "SELECT COUNT(*) as total FROM live_streams WHERE status = 'active'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalStreams($conn) {
    try {
        $sql = "SELECT COUNT(*) as total FROM live_streams";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getActiveUsers($conn) {
    try {
        $sql = "SELECT COUNT(*) as total FROM users WHERE activation = 1";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Tracking | WordPress Manager Dashboard</title>
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
        
        .feature-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-img {
            height: 180px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        
        .feature-img i {
            font-size: 4rem;
        }
        
        .feature-content {
            padding: 20px;
            background-color: white;
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .feature-description {
            color: #6c757d;
            margin-bottom: 20px;
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
        
        .recent-activity {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .activity-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
            color: white;
        }
        
        .activity-icon.screenshot {
            background-color: var(--primary);
        }
        
        .activity-icon.stream {
            background-color: var(--success);
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
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
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-user-lock"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="live_tracking.php" id="liveTrackingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                    <h2>Live Tracking</h2>
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
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Active Users</h5>
                            <div class="stat-number text-primary"><?php echo getActiveUsers($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <h5>Total Screenshots</h5>
                            <div class="stat-number text-success"><?php echo getTotalScreenshots($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            <h5>Active Streams</h5>
                            <div class="stat-number text-info"><?php echo getActiveStreams($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5>Today's Activity</h5>
                            <div class="stat-number text-warning"><?php echo getTodayScreenshots($conn); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Feature Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-img">
                                <i class="fas fa-camera"></i>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title">Screenshots</h3>
                                <p class="feature-description">Capture and view screenshots of user activity. Monitor productivity and ensure compliance with company policies.</p>
                                <a href="screenshots.php" class="btn btn-wp">View Screenshots</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="feature-img">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="feature-content">
                                <h3 class="feature-title">Live Stream</h3>
                                <p class="feature-description">Stream live desktop activity of selected users. Real-time monitoring for immediate support and supervision.</p>
                                <a href="live_stream.php" class="btn btn-wp">Start Streaming</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5>Recent Activity</h5>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="refreshActivity()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                    <button class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-export"></i> Export
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="recent-activity">
                                    <!-- Activity Item 1 -->
                                    <div class="activity-item d-flex">
                                        <div class="activity-icon screenshot">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong>Screenshot captured</strong>
                                                <span class="activity-time">10:30 AM</span>
                                            </div>
                                            <div>Amna Usman - Floorland</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Activity Item 2 -->
                                    <div class="activity-item d-flex">
                                        <div class="activity-icon stream">
                                            <i class="fas fa-video"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong>Live stream started</strong>
                                                <span class="activity-time">10:15 AM</span>
                                            </div>
                                            <div>Aoun Mahdi - Floorland</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Activity Item 3 -->
                                    <div class="activity-item d-flex">
                                        <div class="activity-icon screenshot">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong>Screenshot captured</strong>
                                                <span class="activity-time">10:00 AM</span>
                                            </div>
                                            <div>Aqib Malik - Lush Loom</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Activity Item 4 -->
                                    <div class="activity-item d-flex">
                                        <div class="activity-icon stream">
                                            <i class="fas fa-video"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong>Live stream ended</strong>
                                                <span class="activity-time">9:45 AM</span>
                                            </div>
                                            <div>Areeba Sajjad - Floorland</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Activity Item 5 -->
                                    <div class="activity-item d-flex">
                                        <div class="activity-icon screenshot">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong>Screenshot captured</strong>
                                                <span class="activity-time">9:30 AM</span>
                                            </div>
                                            <div>Bilal Khan - TechNova</div>
                                        </div>
                                    </div>
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
        });

        function refreshActivity() {
            location.reload();
        }
    </script>
</body>
</html>